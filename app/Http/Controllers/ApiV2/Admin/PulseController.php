<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginSession;
use App\Transaction;
use App\User;
use App\UserTracking;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PulseController extends Controller
{
    public function index()
    {
        $usd_value = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        $firstLoginSession = LoginSession::orderBy('id', 'asc')->first();

         //*Daily
         $currentDay = now()->format('Y-m-d');
         $previousDay = now()->subDay()->format("Y-m-d");
         
         $startCurrentDay = $currentDay. " 00:00:00";
         $endCurrentDay = $currentDay. " 23:59:59";
 
         $startPreviousDay = $previousDay. " 00:00:00";
         $endPreviousDay = $previousDay. " 23:59:59";
 
         $tranxCurrentPreviousCryptoDaily = Transaction::where('status','success')->where('created_at','>=', $startPreviousDay)->where('created_at','<=',$endCurrentDay)->get(); 
         $tranxCurrentPreviousUtilDaily = UtilityTransaction::where('status','success')->where('created_at','>=', $startPreviousDay)->where('created_at','<=',$endCurrentDay)->get();

         foreach ($tranxCurrentPreviousUtilDaily as $value) {
            $value->amount = $value->amount/$usd_value;
        }

         $tranxCurrentPreviousDaily = collect([])->concat($tranxCurrentPreviousCryptoDaily)->concat($tranxCurrentPreviousUtilDaily);
         $userCurrentPreviousDaily = User::where('created_at','>=', $startPreviousDay)->where('created_at','<=',$endCurrentDay)->get(); 
 
         $tranxCurrentDaily = $this->searchCollection($tranxCurrentPreviousDaily, $startCurrentDay, $endCurrentDay);
         $tranxPreviousDaily =$this->searchCollection($tranxCurrentPreviousDaily, $startPreviousDay, $endPreviousDay);
 
         $userCurrentDaily = $this->searchCollection($userCurrentPreviousDaily, $startCurrentDay, $endCurrentDay);
         $userPreviousDaily = $this->searchCollection($userCurrentPreviousDaily, $startPreviousDay, $endPreviousDay);
        //? Activation Rate
         $websiteSession = LoginSession::where('created_at','>=', $startCurrentDay)->where('created_at','<=',$endCurrentDay)->count();
         $dailyActivationRate = ($tranxCurrentDaily->count() == 0) ? 0 : ($websiteSession/$tranxCurrentDaily->count())*100; 

         //?Total Number of user
         $dailyTotalNoOfUsers = $userCurrentDaily->count();

         //? Transaction Frequency
         $dailyTransactionFrequency = 0;

         foreach ($tranxCurrentDaily->groupBy('user_id') as $key => $value) {
            if($value->count() >= 2)
            {
                $dailyTransactionFrequency ++;
            }
        }

        //?total no of transactions
        $dailyTranxTotal = $tranxCurrentDaily->count();

         //*Monthly
         $startCurrentMonth = now()->startOfMonth()->addHour()->format('Y-m-d');
         $startCurrentMonth = $startCurrentMonth. " 00:00:00";

         $endCurrentMonth = now()->endOfMonth()->format('Y-m-d');
         $endCurrentMonth = $endCurrentMonth. " 23:59:59";

         $startPreviousMonth = now()->subMonth()->startOfMonth()->addHour()->format('Y-m-d');
         $startPreviousMonth = $startPreviousMonth. " 00:00:00";

         $endPreviousMonth = now()->subMonth()->endOfMonth()->format('Y-m-d');
         $endPreviousMonth = $endPreviousMonth. " 23:59:59";

         $tranxCurrentPreviousCryptoMonthly = Transaction::where('status','success')->where('created_at','>=', $startPreviousMonth)->where('created_at','<=', $endCurrentMonth)->get();
         $tranxCurrentPreviousUtilMonthly = UtilityTransaction::where('status','success')->where('created_at','>=', $startPreviousMonth)->where('created_at','<=', $endCurrentMonth)->get();

         foreach ($tranxCurrentPreviousUtilMonthly as $value) {
            $value->amount = $value->amount/$usd_value;
        }

         $tranxCurrentPreviousMonthly = collect([])->concat($tranxCurrentPreviousCryptoMonthly)->concat($tranxCurrentPreviousUtilMonthly);
         $userCurrentPreviousMonthly = User::where('created_at','>=', $startPreviousMonth)->where('created_at','<=', $endCurrentMonth)->get();

         $tranxCurrentMonth = $this->searchCollection($tranxCurrentPreviousMonthly, $startCurrentMonth, $endCurrentMonth);
         $tranxPreviousMonth = $this->searchCollection($tranxCurrentPreviousMonthly, $startPreviousMonth, $endPreviousMonth);

         $userCurrentMonth = $this->searchCollection($userCurrentPreviousMonthly, $startCurrentMonth, $endCurrentMonth);
         $userPreviousMonth = $this->searchCollection($userCurrentPreviousMonthly, $startPreviousMonth, $endPreviousMonth);

        //?Activation Monthly
        $websiteSessionMonthly = LoginSession::where('created_at','>=', $startCurrentMonth)->where('created_at','<=',$endCurrentMonth)->count();
        $activationRateMonthly = (now()->diffInMonths($firstLoginSession->created_at) < 1) ? "No Data Available" : round(($websiteSessionMonthly/$tranxCurrentMonth->count())*100,2);

        //?Churn Rate Monthly
        $uniqueUsersPreviousChurnMonthly = $tranxPreviousMonth->groupBy('user_id')->count();
        $uniqueUsersCurrentChurnMonthly = $tranxCurrentMonth->groupBy('user_id')->count();
        $newUsersCurrentChurnMonthly = $userCurrentMonth->count();

        $monthlyChurnRate = (($uniqueUsersPreviousChurnMonthly - ($uniqueUsersCurrentChurnMonthly - $newUsersCurrentChurnMonthly)) / $uniqueUsersPreviousChurnMonthly)*100;

        //? Revenue growth rate Monthly
        $previousMonthlyRevenue = $tranxPreviousMonth->sum('amount');
        $currentMonthlyRevenue = $tranxCurrentMonth->sum('amount');

        $monthlyRevenue = (($currentMonthlyRevenue - $previousMonthlyRevenue)/$previousMonthlyRevenue)* 100;

        //? Retention Rate
        $monthlyRetentionRate = 1 - (($uniqueUsersPreviousChurnMonthly - ($uniqueUsersCurrentChurnMonthly - $newUsersCurrentChurnMonthly))/$uniqueUsersPreviousChurnMonthly);

        //?totalNoUsers
        $monthlyTotalUsers = $userCurrentMonth->count();

        //?Retained Users
        $activePreviousMonthUsers = $tranxPreviousMonth->groupBy('user_id');
        $activeCurrentMonthUsers = $tranxCurrentMonth->groupBy('user_id');

        $retainedUsersKeysMonthly = $activePreviousMonthUsers->intersectByKeys($activeCurrentMonthUsers);
        $retainedUsersKeysMonthly = $retainedUsersKeysMonthly->all();

        $retainedUserDataMonthly = array(); 
        foreach ($retainedUsersKeysMonthly as $key => $value) {
            if($value->first()->user)
            {
                $retainedUserDataMonthly[] = $value->first()->user;
            }
        }

        //?transaction frequency
        $monthlyTransactionFrequency = 0;

        foreach ($tranxCurrentMonth->groupBy('user_id') as $key => $value) {
           if($value->count() >= 2)
           {
               $monthlyTransactionFrequency ++;
           }
       }

       //? churned users
       $churnedUsersKeysMonthly = $activePreviousMonthUsers->diffKeys($activeCurrentMonthUsers);

       $churnUserDataMonthly = array(); 
       foreach ($churnedUsersKeysMonthly as $key => $value) {
           if($value->first()->user)
           {
               $churnUserDataMonthly[] = $value->first()->user;
           }
       }

       //?DAU/MAU
       $DAU = $tranxCurrentDaily->groupBy('user_id')->count();
       $MAU = $tranxCurrentMonth->groupBy('user_id')->count();

       $dailyToMonthlyRatio = $DAU/$MAU;

       //? no of transactions
       $monthlyTranxNo = $tranxCurrentMonth->count();

         //*Quarterly
        $startCurrentQuarter = now()->subMonth(2)->startOfMonth()->addHour()->format('Y-m-d');
        $startCurrentQuarter = $startCurrentQuarter. " 00:00:00";

        $endCurrentQuarter = now()->endOfMonth()->format('Y-m-d');
        $endCurrentQuarter = $endCurrentQuarter. " 23:59:59";

        $startPreviousQuarter = now()->subMonth(5)->startOfMonth()->addHour()->format('Y-m-d');
        $startPreviousQuarter = $startPreviousQuarter. " 00:00:00";

        $endPreviousQuarter = now()->subMonth(3)->endOfMonth()->format('Y-m-d');
        $endPreviousQuarter = $endPreviousQuarter. " 23:59:59";

        $tranxCurrentPreviousCryptoQuarter = Transaction::where('status','success')->where('created_at','>=',$startPreviousQuarter)->where('created_at','<=',$endCurrentQuarter)->get();
        $tranxCurrentPreviousUtilQuarter = UtilityTransaction::where('status','success')->where('created_at','>=',$startPreviousQuarter)->where('created_at','<=',$endCurrentQuarter)->get();

        foreach ($tranxCurrentPreviousUtilQuarter as $value) {
            $value->amount = $value->amount/$usd_value;
        }

        $tranxCurrentPreviousQuarter = collect([])->concat($tranxCurrentPreviousCryptoQuarter)->concat($tranxCurrentPreviousUtilQuarter);
        $userCurrentPreviousQuarter = User::where('created_at','>=',$startPreviousQuarter)->where('created_at','<=',$endCurrentQuarter)->get();

        $tranxCurrentQuarter = $this->searchCollection($tranxCurrentPreviousQuarter, $startCurrentQuarter, $endCurrentQuarter);
        $tranxPreviousQuarter = $this->searchCollection($tranxCurrentPreviousQuarter, $startPreviousQuarter, $endPreviousQuarter);

        $userCurrentQuarter = $this->searchCollection($userCurrentPreviousQuarter, $startCurrentQuarter, $endCurrentQuarter);
        $userPreviousQuarter = $this->searchCollection($userCurrentPreviousQuarter, $startPreviousQuarter, $endPreviousQuarter);

        //?Activation Quarterly
        $websiteSessionQuarter = LoginSession::where('created_at','>=', $startCurrentQuarter)->where('created_at','<=',$endCurrentQuarter)->count();
        $activationRateQuarter = (now()->diffInMonths($firstLoginSession->created_at) < 3) ? "No Data Available" : round(($websiteSessionQuarter/$tranxCurrentQuarter->count())*100,2);

        //?churn Rate Quarterly
        $uniqueUsersPreviousChurnQuarterly = $tranxPreviousQuarter->groupBy('user_id')->count();
        $uniqueUsersCurrentChurnQuarterly = $tranxCurrentQuarter->groupBy('user_id')->count();
        $newUsersCurrentChurnQuarterly = $userCurrentQuarter->count();

        $quarterlyChurnRate = (($uniqueUsersPreviousChurnQuarterly - ($uniqueUsersCurrentChurnQuarterly - $newUsersCurrentChurnQuarterly)) / $uniqueUsersPreviousChurnQuarterly)*100;

        //? Revenue growth rate Quarterly
        $previousQuarterlyRevenue = $tranxPreviousQuarter->sum('amount');
        $currentQuarterlyRevenue = $tranxCurrentQuarter->sum('amount');

        $quarterlyRevenue = (($currentQuarterlyRevenue - $previousQuarterlyRevenue)/$previousQuarterlyRevenue)* 100;

        //?Retention Rate Quarterly
        $quarterlyRetentionRate = 1 - (($uniqueUsersPreviousChurnQuarterly - ($uniqueUsersCurrentChurnQuarterly - $newUsersCurrentChurnQuarterly))/$uniqueUsersPreviousChurnQuarterly);

        //?totalNoUsers
        $quarterTotalUsers = $userCurrentQuarter->count();

        //?Retained Users
        $activePreviousMonthQuarterUsers = $tranxPreviousQuarter->groupBy('user_id');
        $activeCurrentMonthQuarterUsers = $tranxCurrentQuarter->groupBy('user_id');

        $retainedUsersKeysQuarterly = $activePreviousMonthQuarterUsers->intersectByKeys($activeCurrentMonthQuarterUsers);
        $retainedUsersKeysQuarterly = $retainedUsersKeysQuarterly->all();

        $retainedUserDataQuarterly = array(); 
        foreach ($retainedUsersKeysQuarterly as $key => $value) {
            if($value->first()->user)
            {
                $retainedUserDataQuarterly[] = $value->first()->user;
            }
        }

        //?transaction frequency
        $quarterlyTransactionFrequency = 0;

        foreach ($tranxCurrentQuarter->groupBy('user_id') as $key => $value) {
           if($value->count() >= 2)
           {
               $quarterlyTransactionFrequency ++;
           }
       }

       //?Quarterly Inactive Users
       $quarterlyInactiveUsers = UserTracking::where('Current_Cycle','QuarterlyInactive')->where('created_at','>=',$startCurrentQuarter)->where('created_at','<=',$endCurrentQuarter)->get();
       $quarterlyInactiveUsersNo = $quarterlyInactiveUsers->count();

        //?Quaterly Deserted Users
        $QuarterlyDesertedUser = "Not Available";
        $QuarterlyDesertedUserNo = 0;
        if(now()->diffInMonths($firstLoginSession->created_at) >= 3) 
        {
            $QDArray = array();
            $QDUsers = User::with('loginSession')->get();
            foreach($QDUsers as $value)
            {
                if($value->loginSession()->count() > 0)
                {
                    $lastUserLoginSession = $value->loginSession()->first();
                    if(now()->diffInMonths($lastUserLoginSession->created_at) >= 3)
                    {
                        $QDArray[] = $value; 
                    }
                }else{
                    $QDArray[] = $value; 
                }  
            }
            $QuarterlyDesertedUser = $QDArray;
            $QuarterlyDesertedUserNo = count($QDArray);
        }

        //? churned users
        $churnedUsersKeysQuarterly = $activePreviousMonthQuarterUsers->diffKeys($activeCurrentMonthQuarterUsers);

        $churnUserDataQuarterly = array(); 
        foreach ($churnedUsersKeysQuarterly as $key => $value) {
            if($value->first()->user)
            {
                $churnUserDataQuarterly[] = $value->first()->user;
            }
        }

        //?DAU/QAU
        $QAU = $tranxCurrentQuarter->groupBy('user_id')->count();

        $dailyToQuarterRatio = $DAU/$QAU;

        //?no of transactions
        $quarterTranxNo = $tranxCurrentQuarter->count();


        //*Annually
        $startCurrentYear = now()->startOfYear()->addHour()->format('Y-m-d');
        $startCurrentYear = $startCurrentYear. " 00:00:00";

        $endCurrentYear = now()->endOfYear()->format('Y-m-d');
        $endCurrentYear= $endCurrentYear. " 23:59:59";

        $startPreviousYear = now()->subYear()->startOfYear()->addHour()->format('Y-m-d');
        $startPreviousYear = $startPreviousYear. " 00:00:00";

        $endPreviousYear = now()->subYear()->endOfYear()->format('Y-m-d');
        $endPreviousYear= $endPreviousYear. " 23:59:59";

        $startOldPreviousYear = now()->subYear(2)->startOfYear()->addHour()->format('Y-m-d');
        $startOldPreviousYear = $startOldPreviousYear. " 00:00:00";

        $endOldPreviousYear = now()->subYear(2)->endOfYear()->format('Y-m-d');
        $endOldPreviousYear = $endOldPreviousYear. " 23:59:59";

        $tranxCurrentPreviousCryptoAnnually = Transaction::with('user')->where('status', 'success')->where('created_at','>=',$startOldPreviousYear)->where('created_at','<=',$endCurrentYear)->get();
        $tranxCurrentPreviousUtilAnnually = UtilityTransaction::with('user')->where('status', 'success')->where('created_at','>=',$startOldPreviousYear)->where('created_at','<=',$endCurrentYear)->get();

        foreach ($tranxCurrentPreviousUtilAnnually as $value) {
            $value->amount = $value->amount/$usd_value;
        }

        $tranxCurrentPreviousAnnually = collect([])->concat($tranxCurrentPreviousCryptoAnnually)->concat($tranxCurrentPreviousUtilAnnually);
        $userCurrentPreviousAnnually = User::with('transactions','utilityTransaction')->where('created_at','>=',$startOldPreviousYear)->where('created_at','<=',$endCurrentYear)->get();

        $tranxCurrentAnnual = $this->searchCollection($tranxCurrentPreviousAnnually, $startCurrentYear, $endCurrentYear);
        $tranxPreviousAnnual = $this->searchCollection($tranxCurrentPreviousAnnually, $startPreviousYear, $endPreviousYear);

        $userCurrentAnnual = $this->searchCollection($userCurrentPreviousAnnually, $startCurrentYear, $endCurrentYear);
        $userPreviousAnnual = $this->searchCollection($userCurrentPreviousAnnually, $startPreviousYear, $endPreviousYear);

        //?Activation Annually
        $websiteSessionAnnual = LoginSession::where('created_at','>=', $startCurrentYear)->where('created_at','<=',$endCurrentYear)->count();
        $activationRateAnnually = (now()->diffInYears($firstLoginSession->created_at) < 1) ? "No Data Available" : round(($websiteSessionAnnual/$tranxCurrentAnnual->count())*100,2);

        //? Churn Rate
        $uniqueUsersPreviousChurn = $tranxPreviousAnnual->groupBy('user_id')->count();
        $uniqueUsersCurrentChurn = $tranxCurrentAnnual->groupBy('user_id')->count();
        $newUsersCurrentChurn = $userCurrentAnnual->count();

        $annualChurnRate = (($uniqueUsersPreviousChurn - ($uniqueUsersCurrentChurn - $newUsersCurrentChurn))/$uniqueUsersPreviousChurn)*100;

        //? revenue growth rate
        $previousAnnualRevenue = $tranxPreviousAnnual->sum('amount');
        $currentAnnualRevenue = $tranxCurrentAnnual->sum('amount');

        $annualRevenue = (($currentAnnualRevenue - $previousAnnualRevenue)/$previousAnnualRevenue)* 100;


        //? Retention Rate 
        $annualRetentionRate = 1 - (($uniqueUsersPreviousChurn - ($uniqueUsersCurrentChurn - $newUsersCurrentChurn))/$uniqueUsersPreviousChurn);

        //?totalNoUsers
        $annualTotalUsers = $userCurrentAnnual->count();

        //?Resurrected Users
        $previousRUsersStart = now()->subMonths(6)->addHour()->format('Y-m-d'). " 00:00:00";
        $previousRUsersEnd = now()->subMonths(1)->addHour()->format('Y-m-d'). " 23:59:59";

        $currentRUsersStart = now()->subMonths(1)->addDay()->addHour()->format('Y-m-d'). " 00:00:00";
        $currentRUsersEnd = now()->format('Y-m-d')." 23:59:59";
        $resurrectedUserAnnually = array();
        foreach($userCurrentAnnual as $key => $value)
        { 
            $prevTranx = $value['transactions']->where('created_at','>=',$previousRUsersStart)->where('created_at','<=',$previousRUsersEnd)->count();
            $prevUtilTranx = $value['utilityTransaction']->where('created_at','>=',$previousRUsersStart)->where('created_at','<=',$previousRUsersEnd)->count(); 

            $currentTranx = $value['transactions']->where('created_at','>=',$currentRUsersStart)->where('created_at','<=',$currentRUsersEnd)->count();
            $currentUtilTranx = $value['utilityTransaction']->where('created_at','>=',$currentRUsersStart)->where('created_at','<=',$currentRUsersEnd)->count(); 
            if($prevTranx == 0 AND $prevUtilTranx == 0)
            {
                if($currentTranx >= 1 OR $currentUtilTranx >= 1)
                {
                    $resurrectedUserAnnually[] = $value;
                }

            }
        }
        
        //?Dead Users
        $DUsersStart = now()->subMonths(6)->addHour()->format('Y-m-d'). " 00:00:00";
        $DUsersEnd = now()->format('Y-m-d')." 23:59:59";

        $deadUserAnnually = array();
        foreach($userCurrentAnnual as $key => $value)
        {
            $tranx = $value['transactions']->where('created_at','>=',$DUsersStart)->where('created_at','<=',$DUsersEnd)->count();
            $utilTranx = $value['utilityTransaction']->where('created_at','>=',$DUsersStart)->where('created_at','<=',$DUsersEnd)->count();
            if( $tranx == 0 AND $utilTranx == 0 )
            {
                $deadUserAnnually[] = $value;
            }
        }

        //?Retained users
        $activePreviousMonthAnnualUsers = $tranxPreviousAnnual->groupBy('user_id');
        $activeCurrentMonthAnnualUsers = $tranxCurrentAnnual->groupBy('user_id');

        $retainedUsersKeys = $activePreviousMonthAnnualUsers->intersectByKeys($activeCurrentMonthAnnualUsers);
        $retainedUsersKeys = $retainedUsersKeys->all();

        $retainedUserDataAnnual = array(); 
        foreach ($retainedUsersKeys as $key => $value) {
            if($value->first()->user)
            {
                $retainedUserDataAnnual[] = $value->first()->user;
            }
        }

        //?transaction frequency
        $annualTransactionFrequency = 0;

        foreach ($tranxCurrentAnnual->groupBy('user_id') as $key => $value) {
           if($value->count() >= 2)
           {
               $annualTransactionFrequency ++;
           }
       }
        //?Quarterly Inactive Users
        $annualQuarterlyInactiveUsers = UserTracking::where('Current_Cycle','QuarterlyInactive')->where('created_at','>=',$startCurrentYear)->where('created_at','<=',$endCurrentYear)->get();
        $annualQuarterlyInactiveUsersNo = $annualQuarterlyInactiveUsers->count();


        //?Churned Users
        $churnedUsersKeys = $activePreviousMonthAnnualUsers->diffKeys($activeCurrentMonthAnnualUsers);

        $churnUserDataAnnual = array(); 
        foreach ($churnedUsersKeys as $key => $value) {
            if($value->first()->user)
            {
                $churnUserDataAnnual[] = $value->first()->user;
            }
        }

        //? Net Growth Rate
        $AnnualNetGrowthRate = ( ( $userCurrentAnnual->count() + count($resurrectedUserAnnually) ) / count($churnUserDataAnnual) ) * 100;

        //? DAU/AAU
        $AAU = $tranxCurrentAnnual->groupBy('user_id')->count();

        $dailyToAnnualRatio = $DAU/$AAU;

        //? no of transactions
        $annualTranxNo = $tranxCurrentAnnual->count();

        $daily = [
            "ActivationRate" => $dailyActivationRate,
            "ChurnRate" => $monthlyChurnRate,
            "NetGrowthRate" =>  $AnnualNetGrowthRate,
            "RevenueGrowthRate" => $monthlyRevenue,
            "RetentionRate" => $monthlyRetentionRate,
            "TotalUsersNo" => $dailyTotalNoOfUsers,
            "ResurrectedUsers" => count($resurrectedUserAnnually),
            "DeadUser" => count($deadUserAnnually),
            "RetainedUser" => count($retainedUserDataMonthly),
            "TransactionFrequency" => $dailyTransactionFrequency,
            "QuarterlyInactiveUsers" => $quarterlyInactiveUsersNo,
            "ChurnedUsers"=> count($churnUserDataMonthly),
            "QuarterlyDesertedUser" => $QuarterlyDesertedUserNo,
            "DAU/MAU" => $dailyToMonthlyRatio,
            "TotalNoOfTranx" => $dailyTranxTotal,
            "modalData" =>[
                "DeadUsers" => $deadUserAnnually,
                "ResurrectedUsers" => $resurrectedUserAnnually,
                "RetainedUsers" => $retainedUserDataMonthly,
                "ChurnedUsers" => $churnUserDataMonthly,
                "QuarterlyInactiveUsers" => $quarterlyInactiveUsers,
                "QuarterlyDesertedUsers" => $QuarterlyDesertedUser
            ]
        ];

        $monthly = [
            "ActivationRate" => $activationRateMonthly,
            "ChurnRate" => $monthlyChurnRate,
            "NetGrowthRate" =>  $AnnualNetGrowthRate,
            "RevenueGrowthRate" => $monthlyRevenue,
            "RetentionRate" => $monthlyRetentionRate,
            "TotalUsersNo" => $monthlyTotalUsers,
            "ResurrectedUsers" => count($resurrectedUserAnnually),
            "DeadUser" => count($deadUserAnnually),
            "RetainedUser" => count($retainedUserDataMonthly),
            "TransactionFrequency" => $monthlyTransactionFrequency,
            "QuarterlyInactiveUsers" => $quarterlyInactiveUsersNo,
            "ChurnedUsers"=> count($churnUserDataMonthly),
            "QuarterlyDesertedUser" => $QuarterlyDesertedUserNo,
            "DAU/MAU" => $dailyToMonthlyRatio,
            "TotalNoOfTranx" => $monthlyTranxNo,
            "modalData" =>[
                "DeadUsers" => $deadUserAnnually,
                "ResurrectedUsers" => $resurrectedUserAnnually,
                "RetainedUsers" => $retainedUserDataMonthly,
                "ChurnedUsers" => $churnUserDataMonthly,
                "QuarterlyInactiveUsers" => $quarterlyInactiveUsers,
                "QuarterlyDesertedUsers" => $QuarterlyDesertedUser
            ]
        ];

        $quarterly = [
            "ActivationRate" => $activationRateQuarter,
            "ChurnRate" => $quarterlyChurnRate,
            "NetGrowthRate" =>  $AnnualNetGrowthRate,
            "RevenueGrowthRate" => $quarterlyRevenue,
            "RetentionRate" => $quarterlyRetentionRate,
            "TotalUsersNo" => $quarterTotalUsers,
            "ResurrectedUsers" => count($resurrectedUserAnnually),
            "DeadUser" => count($deadUserAnnually),
            "RetainedUser" => count($retainedUserDataQuarterly),
            "TransactionFrequency" => $quarterlyTransactionFrequency,
            "QuarterlyInactiveUsers" => $quarterlyInactiveUsersNo,
            "ChurnedUsers"=> count($churnUserDataQuarterly),
            "QuarterlyDesertedUser" => $QuarterlyDesertedUserNo,
            "DAU/MAU" => $dailyToQuarterRatio,
            "TotalNoOfTranx" => $quarterTranxNo,
            "modalData" =>[
                "DeadUsers" => $deadUserAnnually,
                "ResurrectedUsers" => $resurrectedUserAnnually,
                "RetainedUsers" => $retainedUserDataQuarterly,
                "ChurnedUsers" => $churnUserDataQuarterly,
                "QuarterlyInactiveUsers" => $quarterlyInactiveUsers,
                "QuarterlyDesertedUsers" => $QuarterlyDesertedUser
            ]
        ];

        $annually = [
            "ActivationRate" => $activationRateAnnually,
            "ChurnRate" => $annualChurnRate,
            "NetGrowthRate" =>  $AnnualNetGrowthRate,
            "RevenueGrowthRate" => $annualRevenue,
            "RetentionRate" => $annualRetentionRate,
            "TotalUsersNo" => $annualTotalUsers,
            "ResurrectedUsers" => count($resurrectedUserAnnually),
            "DeadUser" => count($deadUserAnnually),
            "RetainedUser" => count($retainedUserDataAnnual),
            "TransactionFrequency" => $annualTransactionFrequency,
            "QuarterlyInactiveUsers" => $annualQuarterlyInactiveUsersNo,
            "ChurnedUsers"=> count($churnUserDataAnnual),
            "QuarterlyDesertedUser" => $QuarterlyDesertedUserNo,
            "DAU/MAU" => $dailyToAnnualRatio,
            "TotalNoOfTranx" => $annualTranxNo,
            "modalData" =>[
                "DeadUsers" => $deadUserAnnually,
                "ResurrectedUsers" => $resurrectedUserAnnually,
                "RetainedUsers" => $retainedUserDataAnnual,
                "ChurnedUsers" => $churnUserDataAnnual,
                "QuarterlyInactiveUsers" => $annualQuarterlyInactiveUsers,
                "QuarterlyDesertedUsers" => $QuarterlyDesertedUser
            ]
        ];

        return response()->json([
            'success' => true,
            'daily' => $daily,
            'monthly' => $monthly,
            'quarterly' => $quarterly,
            'annually' => $annually

         ],200);
    }

    public function searchCollection($collect, $start, $end)
    {
        return $collect->where('created_at','>=',$start)->where('created_at','<=',$end);
    }

    public function chart()
    {

        $current_time = now();
        $data = $this->ChartData($current_time);

        return response()->json([
            'success' => true,
            'data' => $data,
        ],200);
    }

    public function sortChart(Request $r){
        $validator = Validator::make($r->all(),[
            'date' => 'required|date',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $date = Carbon::parse($r->date)->addHour();

        $data = $this->ChartData($date);
        return response()->json([
            'success' => true,
            'data' => $data,
        ],200);
    }

    public function ChartData($date)
    {
        $usd_value = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        //? Weekly
        $startWeek = Carbon::parse($date)->subDays(7)->addHour()->format('Y-m-d');
        $endWeek = Carbon::parse($date)->format('Y-m-d');

        //TODO: how to format data for week for frontend
        $weeklyData = $this->dailyChart($startWeek, $endWeek, $usd_value);
        $revenueGrowthWeekly =  $this->calculateRevenueGrowth($weeklyData);

        //? Monthly
        $startMonthly = Carbon::parse($date)->subMonth()->addHour()->format('Y-m-d');
        $endMonthly = Carbon::parse($date)->format('Y-m-d');

        $monthlyData = $this->dailyChart($startMonthly, $endMonthly, $usd_value);
        $revenueGrowthMonthly = $this->calculateRevenueGrowth($monthlyData);

        //? Quaterly
        $monthsBack = 11;
        $qData = $this->monthlyChart($monthsBack, $date, $usd_value);
        
        $quarterlyData = $this->monthlyBreakdown($qData);
        $revenueGrowthQuarter = $this->calculateRevenueGrowth($quarterlyData);

        //?Annually
        $monthsBack = 11;
        $AnnualData = $this->monthlyChart($monthsBack, $date, $usd_value);

        $revenueGrowthAnnually = $this->calculateRevenueGrowth($AnnualData);
        
        $exportData = array(
            'weekly' => $revenueGrowthWeekly,
            'monthly' => $revenueGrowthMonthly,
            'quarter' => $revenueGrowthQuarter,
            'annually' => $revenueGrowthAnnually
        );

        return $exportData;
    }

    public function calculateRevenueGrowth($array)
    {
        /**
         * Undocumented function
         *
         * @param array $array
         * @return integer
         */

         $loopCounter = count($array) - 1;
         do {
            if($loopCounter == 0)
            {
                $array[0]['revenueGrowth'] = 0;
                break;
            }   
            $revenueGrowth = ($array[$loopCounter - 1]['revenue'] == 0) ? 0 : ($array[$loopCounter]['revenue'] - $array[$loopCounter - 1]['revenue']) / $array[$loopCounter - 1]['revenue'];

            $array[$loopCounter]['revenueGrowth'] = round($revenueGrowth,2);

            $loopCounter --;
         } while ($loopCounter >= 0);
         return $array;
    }

    public function monthlyBreakdown($array)
    {
        /**
         *
         * @param array $array
         * @return array
         * 
         */

        $quarterlyArray = array_chunk($array, 3);

        $quarter = array();
        foreach (collect($quarterlyArray) as $value) {
            $total = $value[0]['revenue'] + $value[1]['revenue'] + $value[2]['revenue'];
            $date = $value[0]['date']." - ".$value[2]['date'];

            $quarter[] = array(
                'revenue' => $total,
                'date' => $date,
            );
        }

        return $quarter;

    }

    public function dailyChart($startDate, $endDate, $usd_value)
    {
        /**
         * 
         * @param date $startDate
         * 
         * @param date $endDate
         * 
         * @param integer $usd_value
         * 
         * @return array $exportData
         * 
         */

        $durationTranxCrypto = Transaction::where('status', 'success')->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get();
        $durationTranxUtil = UtilityTransaction::where('status', 'success')->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get();

        foreach ($durationTranxUtil as $value) {
            $value->amount = $value->amount/$usd_value;
        }

        $durationTranx = collect([])->concat($durationTranxCrypto)->concat($durationTranxUtil);
        $loopCounter = Carbon::parse($startDate)->addHour()->diffInDays(Carbon::parse($endDate)->addHour());

        // return $durationTranx;
        $exportData = array();

        for($i = 0; $i <= $loopCounter; $i ++)
        {
            $day = Carbon::parse($startDate)->addHour()->addDays($i)->format('Y-m-d');
            $revenue = $durationTranx->where('created_at','>=',"$day 00:00:00")->where('created_at','<=',"$day 23:59:59")->sum('amount');

            if ($loopCounter <= 7)
            {
                $exportData[] = array(
                    'revenue' => $revenue,
                    'date' => Carbon::parse($day)->addHour()->format("l")
                );

            }
            else{
                $exportData[] = array(
                    'revenue' => $revenue,
                    'date' => Carbon::parse($day)->addHour()->format("d F Y")
                );
            }
            
        }

        return $exportData;
    }

    public function monthlyChart($monthsBack, $time, $usd_value)
    {
        /**
         * 
         * @param integer $monthsBack
         * 
         * @param date $time
         * 
         * @param integer $usd_value
         * 
         * @return array $exportData
         * 
         */

        $listOfMonths = array();
        do{
            $duration = Carbon::parse($time)->subMonths($monthsBack)->addHour();

            $startMonth = Carbon::parse($duration)->startOfMonth()->addHour();
            $endMonth = Carbon::parse($duration)->endOfMonth()->addHour();
            $listOfMonths[] = array(
                'start' => $startMonth,
                'end' => $endMonth,
            ); 
            $monthsBack -- ;
        }
        while($monthsBack >= 0);

        $endIndex = count($listOfMonths) - 1;

        $durationTranxCrypto = Transaction::where('status', 'success')->where('created_at','>=',$listOfMonths[0]['start'])->where('created_at','<=',$listOfMonths[$endIndex]['end'])->get();
        $durationTranxUtil = UtilityTransaction::where('status', 'success')->where('created_at','>=',$listOfMonths[0]['start'])->where('created_at','<=',$listOfMonths[$endIndex]['end'])->get();

        foreach ($durationTranxUtil as $value) {
            $value->amount = $value->amount/$usd_value;
        }

        $durationTranx = collect([])->concat($durationTranxCrypto)->concat($durationTranxUtil);
        $exportData =  array();

        for($i = 0; $i <= $endIndex; $i ++)
        {
            $revenue = $durationTranx->where('created_at','>=',$listOfMonths[$i]['start'])->where('created_at','<=',$listOfMonths[$i]['end'])->sum('amount');

            $exportData[] = array(
                'revenue' => $revenue,
                'date' => Carbon::parse($listOfMonths[$i]['start'])->addHour()->format("F Y")
            );
        }

        return $exportData; 


    }
}
