<?php

namespace App\Http\Controllers\Admin;

use App\EmailChecker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PriorityRanking;
use App\User;
use App\UserTracking;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SalesHelperController extends Controller
{
    //Account officers is the name of the new position in place of Sales OLD
    public $accountOfficers;

    public function __construct(){
        // getting the sales data on load of this class
        $this->accountOfficers = User::where('role',557)
                        ->orderBy('created_at','ASC')
                        ->where('status','active')->get();
    }

    public function runConfigData(){
        $this->divideUsersBetweenAccountOfficers();
        $this->dailyDashboardChecks();
        $this->FreeWithdrawalForAllQuarterlyInactive();
    }

    public function NewUserOldUserActiveUserFlow($category = NULL, $type = NULL){
        if($category == 'old'){
            $salesOLD = new BusinessDeveloperController();
            return $salesOLD->index($type);
        }
    }
    
    public function divideUsersBetweenAccountOfficers(){
        //getting all the quarterly inactive
        $quarterlyInactive = UserTracking::where('Current_Cycle','QuarterlyInactive')->get();
        $accountOfficers = $this->accountOfficers;

        // checking the quarterlyInactive to get the ID in charge of accounts
        $accountOfficersKeys = $quarterlyInactive->groupBy('custodian_id')->keys()->toArray();
        // checking there is a user that is not assigned to an account officer
        if(in_array('',$accountOfficersKeys)){
            $this->AssignUserToAccountOfficers($quarterlyInactive, $accountOfficers);
        }

        // checking if the account officers Count Changes
        $custodianCount = $quarterlyInactive->groupBy('custodian_id')->count();
        if($custodianCount != $accountOfficers->count()){
            $this->AssignUserToAccountOfficers($quarterlyInactive, $accountOfficers);
        }
    }

    public function AssignUserToAccountOfficers($collectionData, $accountOfficers){
        // NB: this split happens is to ensure that the account officers have equal number of users
        $splitNumber = ceil($collectionData->count()/$accountOfficers->count());

        // here the data is split to evenly
        $splitCollectionBetweenAccountOfficers = $collectionData->chunk($splitNumber);

        foreach($splitCollectionBetweenAccountOfficers as $SplitKey => $splitData){
            // the key is as a referral to the the account officer arrangement e.g key 0 means the first account officer person on the list 
            $accountOfficersPersonnel = $accountOfficers[$SplitKey];
            foreach($splitData as $splitUserInformation){
                // points to the individual user under that chunk data split 
                // update the user information to update the user Tracking data to that account officer;
                $splitUserInformation->update([
                    'custodian_id' => $accountOfficersPersonnel->id,
                ]);
            }
        }
    }

    
    public function dailyDashboardChecks(){
        //testing email
        $this->testingMail();
        //checking system calls
        //sending the second emails
        $this->sendingFollowUpEmailForFreeWithdrawal();

    }

    public function systemArtisanCallsForTrackingUser(){
        //these artisan call is for checking what happens as per human behavior in the system
        Artisan::call('check:active');
        Artisan::call('check:called');
        Artisan::call('check:Responded');
        Artisan::call('check:Recalcitrant');
        Artisan::call('noResponse:check');
        Artisan::call('check:quarterlyInactive');
    }

    public static function DataAnalytics() {
        $userTrackingByCustodianID = UserTracking::where('custodian_id',Auth::user()->id)->get();
        $userTrackingBySalesID = UserTracking::where('sales_id',Auth::user()->id)->get();
        ///////////////
        //NEW USERS

        // new inactive users
        $newInactiveUsers = $userTrackingByCustodianID
        ->where('Current_Cycle','NewInActiveUser')
        ->count();

        // new called users
        $newCalledUsers = $userTrackingBySalesID
        ->where('Current_Cycle','NewCalledUser')
        ->count();

        //unresponsive
        $newUnresponsiveUsers =  $userTrackingBySalesID
        ->where('Current_Cycle','NewUnresponsiveUser')
        ->count();

        ///////////////
        //OLD USERS
        $quarterlyInactive = $userTrackingByCustodianID
        ->where('Current_Cycle','QuarterlyInactive')
        ->count();

        $calledUsers = $userTrackingBySalesID
        ->whereIn('Current_Cycle',['Called','Responded','Recalcitrant'])
        ->count();

        $respondedUsers = $userTrackingBySalesID
        ->where('Current_Cycle','Responded')
        ->count();

        $export = [
            'newInactiveUsers' => $newInactiveUsers,
            'newCalledUsers' => $newCalledUsers,
            'newUnresponsiveUsers' => $newUnresponsiveUsers,
            'quarterlyInactive' => $quarterlyInactive,
            'calledUsers' => $calledUsers,
            'respondedUsers' => $respondedUsers,
        ];

        return $export;
    }


    public function sendingFollowUpEmailForFreeWithdrawal(){
        // get users that email has been sent to only once
        // NB: Bulk EMAIL needs to be implemented to this
        $usersForFollowUp = UserTracking::where('emailCount',1)->get();
        foreach($usersForFollowUp as $userData){
            BusinessDeveloperController::ActivationEmail($userData->user);
            $userData->update([
                'emailCount' => 2,
            ]);
        }
    }

    public function testingMail(){
        // just to confirm the mail is being sent
        $user = new User();
        $user->first_name = 'David';
        $user->last_name = 'David';
        $user->email = 'dantownsales@gmail.com';
        $user->username = 'David';
        BusinessDeveloperController::ActivationEmail($user);
    }

    public function FreeWithdrawalForAllQuarterlyInactive(){
        // setting 10 free withdrawal for all quarterly inactive
        $quarterlyInactive = UserTracking::where('Current_Cycle','QuarterlyInactive')->where('free_withdrawal',0)->get();
        foreach($quarterlyInactive as $qiUser){
            $qiUser->update([
                'free_withdrawal' => 10,
            ]);
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////  /////////////////////////////////////////////
    //OLD USER HELPER FLOW
    public static function quarterlyInactive(){
        // checking the quarterly inactive users under a user
        $table = UserTracking::where('Current_Cycle','QuarterlyInactive')
        ->with('transactions','user')
        ->where('custodian_id',Auth::user()->id)
        ->get();

        $data_table = self::quarterlyInactiveMonthlySort(null, null, $table);
        $data_table = $data_table->sortByDesc('transactionAmount');

        return $data_table;
    }

    public static function sortQuarterlyInactive(Request $request, $type, $call_categories){
        // Month Range is for how many month for going back
        $salesCategory = 'old';
        $monthRange = NULL;
        if($request){
            $monthRange = $request->month;
        }

        $startDate = NULL;
        $endDate = NULL;

        if($monthRange){
            $startDate = now()->subMonth($monthRange);
            $endDate = now();
        }

        $table = UserTracking::where('Current_Cycle','QuarterlyInactive')
        ->with('transactions','user')
        ->where('custodian_id',Auth::user()->id)
        ->get();

        $segment = "Quarterly Inactive";
        $data_table = self::quarterlyInactiveMonthlySort($startDate, $endDate, $table);
        
        if($monthRange != NULL){
            $data_table = $data_table->where('transactionAmount','>',0)->sortByDesc('transactionAmount');
        }else{
            $data_table = $data_table->sortByDesc('transactionAmount');
        }

        $count = $data_table->count();
        $data_table = $data_table->paginate(100);

        return view(
            'admin.business_developer.users',
            compact([
                'data_table','type','segment','call_categories','count','salesCategory'
            ])
        );
    }

    public static function quarterlyInactiveMonthlySort($startDate, $endDate, $collection){
        $options = [
            'join' => ', ',
            'parts' => 2,
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
        ];
        
        foreach($collection as $col){
            if($startDate != null AND $endDate != null){
                $sortTranx = $col['transactions']->where('created_at','>=',$startDate)->where('created_at','<=',$endDate);
            } else {
                $sortTranx = $col['transactions'];
            }
            $transactions = $sortTranx;
            // checking priority 
            $col->priority = self::priorityRanking($transactions->sum('amount'));
            $col->transactionCount = $transactions->count();
            $col->transactionAmount = $transactions->sum('amount');

            //getting the users successful transactions (NB: check the relationship in the model for more information)
            if($transactions->count() == 0){
                $col->last_transaction_date = 'No Transactions';
            } else {
                $col->last_transaction_date = $transactions->first()->created_at->format('d M Y, h:ia');
                
    
                $col->ltd_date = $transactions->first()->created_at->diffForHumans(now(),$options)." ago";
            }
        }
        return $collection;
    }

    public static function priorityRanking($amount){
        //converting the collection data to array
        $rankings = PriorityRanking::orderBy('priority_price', 'ASC')->get()->toArray();
        for($i=0; $i < count($rankings); $i++){
            $currentKey = $i;
            $nextKey = $i + 1;

            if(isset($rankings[$nextKey])){
                if($currentKey == 0){
                    //checking if the amount is greater than the lowest priority then it's below that priority
                    if($amount < $rankings[$currentKey]['priority_price']){
                        return "Below ".$rankings[$currentKey]['priority_name'];
                    }
                }

                // if the amount is greater than the current amount and less than the next amount return the current priority name 
                if($amount >= $rankings[$currentKey]['priority_price'] AND $amount < $rankings[$nextKey]['priority_price']){
                    return $rankings[$currentKey]['priority_name'];
                }
            }else{
                // if there is no next key means that the amount is greater than everything in the DB.
                return $rankings[$currentKey]['priority_name'];
            }
        }
    }

    public static function DisableMultiAccount(UserTracking $userTracking, $id){

        $userTracking->update([
            'Previous_Cycle' =>$userTracking->Current_Cycle,
            'current_cycle_count_date' => now(),
            'Current_Cycle' => "DeadUser",
            'sales_id' => Auth::user()->id,
            'called_date'=> now(),
        ]);
    }

    public static function noResponseUpdate(UserTracking $userTracking, $id){
        $streak = $userTracking->noResponse_streak;
        if($userTracking->Current_Cycle == "NoResponse"){
            ++$streak; // increment streak by one
        }

        $userTracking->update([
            'Previous_Cycle' =>$userTracking->Current_Cycle,
            'current_cycle_count_date' => now(),
            'Current_Cycle' => "NoResponse",
            'sales_id' => Auth::user()->id,
            'called_date'=> now(),
            'noResponse_streak'=>$streak,
        ]);

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // NEW USERS FLOW
    public function divideNewUsersBetweenAccountOfficers(){
        //getting all the New Users 
        
        $newActiveUsers = UserTracking::where('Current_Cycle','NewInActiveUser')->get();
        $accountOfficers = $this->accountOfficers;

        // checking the quarterlyInactive to get the ID in charge of accounts
        $accountOfficersKeys = $newActiveUsers->groupBy('custodian_id')->keys()->toArray();

        // checking there is a user that is not assigned to an account officer
        if(in_array(0,$accountOfficersKeys) OR in_array('',$accountOfficersKeys)){
            $this->AssignUserToAccountOfficers($newActiveUsers, $accountOfficers);
        }

        // checking if the account officers Count Changes
        $custodianCount = $newActiveUsers->groupBy('custodian_id')->count();
        if($custodianCount != $accountOfficers->count()){
            $this->AssignUserToAccountOfficers($newActiveUsers, $accountOfficers);
        }
    }

    public static function changingRecentlyJoinedUsersFromActiveToNewUsers(){
        //checking if the active data if there are active Users who just joined the system (based on the old build)
        $threeMonthsAgo = now()->subMonths(3);
        $activeUsers = UserTracking::where('Current_Cycle','Active')
        ->whereHas('user', function ($query) use ($threeMonthsAgo) {
            $query->where('created_at','>', $threeMonthsAgo);
        })->get();

        //changing those users to New Users 
        foreach($activeUsers as $user){
            $user->update([
                'Current_Cycle' => 'NewUser',
                'custodian_id' => NULL,
            ]);
        }
        return back()->with(['success'=>'Active Users Look Up Complete']);
    }

    public static function checkNewUserForNewInactiveOrActive(){
        $activeUsers = UserTracking::where('Current_Cycle','NewUser')->with('transactions','user')->get();

        foreach($activeUsers as $userData){
            $userCreationDate = Carbon::parse($userData->user->created_at);// parsing the created date as a timestamp
            $DifferenceInDays = $userCreationDate->diffInDays(now()); // checking if the user has been in the system for up to 14 days
            if($DifferenceInDays >= 14){
                //checking the user transactions in those 14 days
                $userTransactions = $userData->transactions;
                
                $recentUserTransactions = $userTransactions
                ->where('created_at','>=',$userCreationDate);

                //user transactions count 
                $userTransactionsCount = $recentUserTransactions->count();
                if($userTransactionsCount > 0){
                    //if the transaction is more than one when the user joined the the user is an active user
                    $userData->update([
                        'Current_Cycle' => "NewActiveUser",
                        'current_cycle_count_date' => now(),
                        'custodian_id' => NULL,
                    ]);
                } else {
                    //if the transaction is zero when the user joined the the user is an inactive user
                    $userData->update([
                        'Current_Cycle' => "NewInActiveUser",
                        'current_cycle_count_date' => now(),
                        'custodian_id' => NULL,
                    ]);
                }
            }
        }
    }

}
