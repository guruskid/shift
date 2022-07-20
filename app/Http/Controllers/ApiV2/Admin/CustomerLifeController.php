<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CustomerLifeController extends Controller
{
    public function index()
    {
        $current_date = now();
        $data = $this->loadData($current_date);
        return response()->json([
            'success' => true,
            'data' => $data
        ],200);
    }

    public function sorting(Request $request){
        $validate = Validator::make($request->all(), [
            'month' => 'required|integer',
            'year' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $date = Carbon::createFromDate($request->year, $request->month, 2);
        $data = $this->loadData($date);
        return response()->json([
            'success' => true,
            'data' => $data
        ],200);
    }

    public function loadData($current_date)
    {
        //?AnnuallyTransactions
        $yearlyTransaction = Transaction::whereYear('created_at', $current_date->year)->where('status', 'success')->get();
        $yearlyTransactionsVolume = $yearlyTransaction->sum('amount');
        $yearlyTransactionsCount = $yearlyTransaction->count();

        $yearlyUtilities = UtilityTransaction::whereYear('created_at', $current_date->year)->where('status', 'success')->get();
        $yearlyUtilitiesVolume = $yearlyUtilities->sum('amount');
        $yearlyUtilitiesCount = $yearlyUtilities->count();

        $yearlyVolume = $yearlyTransactionsVolume + $yearlyUtilitiesVolume;
        $yearlyCount = $yearlyTransactionsCount + $yearlyUtilitiesCount;

        $uniqueYearlyUsers = collect([])->concat($yearlyTransaction)->concat($yearlyUtilities)->groupBy('user_id')->count();

        //*Average Annual Purchase Value
        $AAPV =  ($yearlyCount < 1) ? 0 : $yearlyVolume / $yearlyCount;

        //*Average Annual Purchase Frequency
        $AAPF = ($uniqueYearlyUsers < 1) ?  0 : $yearlyCount / $uniqueYearlyUsers;

        //*Customer Value Annually
        $CVA = $AAPV * $AAPF;

        //*Average Customer LifeSpan Annually
        $previous_yearly_transactions = Transaction::whereYear('created_at', "=", ($current_date->year)-1 )->where('status', 'success')->get();
        $previous_yearly_utilities = UtilityTransaction::whereYear('created_at', "=", ($current_date->year)-1 )->where('status', 'success')->get();
        $uniqueYearlyUsersPT = collect([])->concat($previous_yearly_transactions)->concat($previous_yearly_utilities)->groupBy('user_id')->count();

        $new_users = array();
        $current_year_new_users =  User::with('transactions','utilityTransaction')->whereYear('created_at', $current_date->year)->get();
        foreach ($current_year_new_users as $value) {
            if( count( $value['transactions'] ) > 0 OR count( $value['utilityTransaction'] ) > 0 )
            {
                $new_users[] = $value;
            }
        }
        $new_users = ( collect( $new_users )->count() );

        $ACLA =  1 - ( ($uniqueYearlyUsersPT - ( $uniqueYearlyUsers - $new_users )) / $uniqueYearlyUsersPT) ;

        //*Average Customer Lifetime Value Annually
        $ACLVA = $CVA * $ACLA;

        $annualData = array(
            'AverageAnnualPurchaseValue' => $AAPV,
            'AverageAnnualPurchaseFrequency' => $AAPF,
            'CustomerValueAnnually' => $CVA,
            'AverageCustomerLifeSpanAnnually' => $ACLA,
            'AverageCustomerLifetimeValueAnnually' => $ACLVA,
            'AnnualTransactionFrequency' => $uniqueYearlyUsers,
        );

        //?Monthly

        $monthlyTransactions = Transaction::whereYear('created_at', $current_date->year)
        ->whereMonth('created_at', $current_date->month)->where('status', 'success')->get();

        $monthlyTransactionsVolume = $monthlyTransactions->sum('amount');
        $monthlyTransactionsCount = $monthlyTransactions->count();
        
        $monthlyUtilities = UtilityTransaction::whereYear('created_at', $current_date->year)
        ->whereMonth('created_at', $current_date->month)->where('status', 'success')->get();

        $monthlyUtilitiesVolume = $monthlyUtilities->sum('amount');
        $monthlyUtilitiesCount = $monthlyUtilities->count();

        $monthlyVolume = $monthlyTransactionsVolume + $monthlyUtilitiesVolume;
        $monthlyCount = $monthlyTransactionsCount + $monthlyUtilitiesCount;

        $uniqueMonthlyUsers = collect([])->concat($monthlyTransactions)->concat($monthlyUtilities)->groupBy('user_id')->count();

        //*Average Monthly Purchase Value
        $AMPV = ($monthlyCount < 1) ? 0 : $monthlyVolume / $monthlyCount;

        //*Average Monthly Purchase Frequency 
        $AMPF = ($uniqueMonthlyUsers < 1) ? 0 : $monthlyCount / $uniqueMonthlyUsers;

        //*Customer Value Monthly
        $CVM = $AMPV * $AMPF;

        //*Average Customer LifeSpan Monthly
        $previous_month = Carbon::parse($current_date)->subMonth();

        $previous_monthly_transactions = Transaction::whereYear('created_at', $previous_month->year)
        ->whereMonth('created_at', $previous_month->month)->where('status', 'success')->get();

        $previous_monthly_utilities = UtilityTransaction::whereYear('created_at', $previous_month->year)
        ->whereMonth('created_at', $previous_month->month)->where('status', 'success')->get();

        $uniqueMonthlyUserPT = collect([])->concat($previous_monthly_transactions)->concat($previous_monthly_utilities)->groupBy('user_id')->count();

        $new_users = array();
        $current_year_new_users =  User::with('transactions','utilityTransaction')->whereYear('created_at', $current_date->year)
        ->whereMonth('created_at', $current_date->month)->where('status', 'success')->get();

        foreach ($current_year_new_users as $value) {
            if( count( $value['transactions'] ) > 0 OR count( $value['utilityTransaction'] ) > 0 )
            {
                $new_users[] = $value;
            }
        }
        $new_users = ( collect( $new_users )->count() );

        $ACLM = 1 - ( ($uniqueMonthlyUserPT - ( $uniqueMonthlyUsers - $new_users )) / $uniqueMonthlyUserPT) ;

        //*Average Customer Lifetime Value Monthly
        $ACLVM = $CVM * $ACLM;

        $monthlyData = array(
            'AverageMonthlyPurchaseValue' => $AMPV,
            'AverageMonthlyPurchaseFrequency' => $AMPF,
            'CustomerValueMonthly' => $CVM,
            'AverageCustomerLifeSpanMonthly' => $ACLM,
            'AverageCustomerLifetimeValueMonthly' => $ACLVM,
            'MonthlyTransactionFrequency' => $uniqueMonthlyUsers,
        );

        $exportData = array(
            'Annual' => $annualData,
            'Monthly' => $monthlyData,
        );

        return $exportData;
    }

    public function ChartData()
    {
        $current_time = now();
        $usd_value = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        //?Weekly 
        $startWeek = Carbon::parse($current_time)->subDays(7)->addHour()->format('Y-m-d');
        $endWeek = Carbon::parse($current_time)->format('Y-m-d');

        $weeklyData = $this->daysChart($startWeek, $endWeek, $usd_value);
        

        //?monthly
        $startMonthly = Carbon::parse($current_time)->subMonth()->addHour()->format('Y-m-d');
        $endMonthly = Carbon::parse($current_time)->format('Y-m-d');

        $monthlyData = $this->daysChart($startMonthly, $endMonthly, $usd_value);
        

        //?Quarterly
        $monthsBack = 2;
        $quarterlyData = $this->monthsChart($monthsBack, $current_time, $usd_value);

        //?Annually
        $monthsBack = 11;
        $annualData = $this->monthsChart($monthsBack, $current_time, $usd_value);

        $exportData = array(
            'success' => true,
            'Weekly' => $weeklyData,
            'Monthly' => $monthlyData,
            'Quarterly' => $quarterlyData,
            'Annually' => $annualData
        );

        return response()->json(['f' => $exportData]);

    }

    public function sortChartData(Request $r)
    {
        /**
         * @param request $startDate
         * 
         * @param request $endDate
         * 
         * @return array $exportData
         */

        $validator = Validator::make($r->all(),[
            'startDate' => 'required|date',
            'endDate' => 'required|date'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $exportData = array();
        $usd_value = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        $start_date = Carbon::parse($r->startDate)->addHour();
        $end_date = Carbon::parse($r->endDate)->addHour();

        $daysDiff = $start_date->diffInDays($end_date);

        if($daysDiff <= 90)
        {
            $exportData = $this->daysChart($start_date->format('Y-m-d'), $end_date->format('Y-m-d'), $usd_value);
        }else{
            $monthsDiff = $start_date -> diffInMonths( $end_date );
            $monthsBack = $monthsDiff - 1;

            $exportData = $this->monthsChart($monthsBack, $end_date, $usd_value);
        }
        
        return response()->json([
            'success' => true,
            'data'=> $exportData
        ]);
    }

    public function daysChart($startDate, $endDate, $usd_value){
        /**
         * @param date $startDate
         * 
         * @param date $endDate
         * 
         * @param integer $usd_value
         * 
         * @return array $exportData
         */

        $durationNewUsers = User::whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get();

        $durationTransactionsCrypto = Transaction::where('status', 'success')->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get();
        $durationTransactionsUtilities = UtilityTransaction::where('status', 'success')->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)->get();

        foreach ($durationTransactionsUtilities as $value) {
            $value->amount = $value->amount/$usd_value;
        }

        $durationTransactions = collect([])->concat($durationTransactionsCrypto)->concat($durationTransactionsUtilities);

        $loopCounter = Carbon::parse($startDate)->addHour()->diffInDays(Carbon::parse($endDate)->addHour());
        $exportData = array();

        for( $i = 0; $i <= $loopCounter; $i ++)
        {
            $day = Carbon::parse($startDate)->addHour()->addDays($i)->format('Y-m-d');

            $transactions = $durationTransactions->where('created_at','>=',"$day 00:00:00")->where('created_at','<=',"$day 23:59:59");
            $tranxNo = $transactions->count();

            $turnover = $transactions->sum('amount');
            $uniqueUsers  = $transactions->groupBy('user_id')->count();

            $newUsers = $durationNewUsers->where('created_at','>=',"$day 00:00:00")->where('created_at','<=',"$day 23:59:59")->count();

            $exportData[] = array(
                'TransactionsNo' => $tranxNo,
                'TurnOver' => $turnover,
                'NewUsers' => $newUsers,
                'uniqueUsers' => $uniqueUsers,
                'date' => Carbon::parse($day)->addHour()->format("d F Y")
            );
        }

        return $exportData;
    }

    public function monthsChart($monthsBack, $time, $usd_value)
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
         */
        $listOfMonths = array();

        //* Generating list of months from monthsBack data
        do{
            $duration = Carbon::parse($time)->subMonths($monthsBack)->addHour();

            $startMonth = Carbon::parse($duration)->startOfMonth()->addHour();
            $endMonth = Carbon::parse($duration)->endOfMonth()->addHour();
            $listOfMonths[] = array(
                'start' => $startMonth,
                'end' => $endMonth,
            ); 
            $monthsBack -- ;
        }while($monthsBack >= 0);
        $endIndex = count($listOfMonths) - 1;

        $durationNewUsers = User::where('created_at','>=',$listOfMonths[0]['start'])->where('created_at','<=',$listOfMonths[$endIndex]['end'])->get();

        $durationTransactionsCrypto = Transaction::where('status', 'success')->where('created_at','>=',$listOfMonths[0]['start'])->where('created_at','<=',$listOfMonths[$endIndex]['end'])->get();
        $durationTransactionsUtilities = UtilityTransaction::where('status', 'success')->where('created_at','>=',$listOfMonths[0]['start'])->where('created_at','<=',$listOfMonths[$endIndex]['end'])->get();

        foreach ($durationTransactionsUtilities as $value) {
            $value->amount = $value->amount/$usd_value;
        }

        $durationTransactions = collect([])->concat($durationTransactionsCrypto)->concat($durationTransactionsUtilities);

        $exportData = array();

        for($i = 0; $i < count($listOfMonths); $i ++)
        {
            $transactions = $durationTransactions->where('created_at','>=',$listOfMonths[$i]['start'])->where('created_at','<=',$listOfMonths[$i]['end']);
            $tranxNo = $transactions->count();

            $turnover = $transactions->sum('amount');
            $uniqueUsers  = $transactions->groupBy('user_id')->count();

            $newUsers = $durationNewUsers->where('created_at','>=',$listOfMonths[$i]['start'])->where('created_at','<=',$listOfMonths[$i]['end'])->count();

            $exportData[] = array(
                'TransactionsNo' => $tranxNo,
                'TurnOver' => $turnover,
                'NewUsers' => $newUsers,
                'uniqueUsers' => $uniqueUsers,
                'date' => Carbon::parse($listOfMonths[$i]['start'])->addHour()->format("F Y")
            );
        }

        return $exportData;
    }
}
