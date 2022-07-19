<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CustomerLifeController extends Controller
{
    /** 
     * Undocumented function
     *
     * @return void
     * TODO first of all we just return the Annual, Quarterly
     * *this  lifespan part should not be among cause we are calculating churn rate
     * ? for the graph what am my plotting against : cause it makes no sense
     * 
     */
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
        $yearlyTransaction = Transaction::whereYear('created_at', "=", $current_date->year)->where('status', 'success')->get();
        $yearlyTransactionsVolume = $yearlyTransaction->sum('amount');
        $yearlyTransactionsCount = $yearlyTransaction->count();


        $yearlyUtilities = UtilityTransaction::whereYear('created_at', "=", $current_date->year)->where('status', 'success')->get();
        $yearlyUtilitiesVolume = $yearlyUtilities->sum('amount');
        $yearlyUtilitiesCount = $yearlyUtilities->count();


        $yearlyVolume = $yearlyTransactionsVolume + $yearlyUtilitiesVolume;
        $yearlyCount = $yearlyTransactionsCount + $yearlyUtilitiesCount;
        
        $monthlyTransactions = Transaction::whereYear('created_at', "=", $current_date->year)->whereMonth('created_at', "=", $current_date->month)->where('status', 'success')->get();
        $monthlyTransactionsVolume = $monthlyTransactions->sum('amount');
        $monthlyTransactionsCount = $monthlyTransactions->count();
        
        $monthlyUtilities = UtilityTransaction::whereYear('created_at', "=", $current_date->year)->whereMonth('created_at', "=", $current_date->month)->where('status', 'success')->get();
        $monthlyUtilitiesVolume = $monthlyUtilities->sum('amount');
        $monthlyUtilitiesCount = $monthlyUtilities->count();

        $monthlyVolume = $monthlyTransactionsVolume + $monthlyUtilitiesVolume;
        $monthlyCount = $monthlyTransactionsCount + $monthlyUtilitiesCount;

        //*unique yearly and nonthly users
        //? creating a collection and concatenating the data of two collections and grouping the data by the USER_ID
        $uniqueYearlyUsers = collect([])->concat($yearlyTransaction)->concat($yearlyUtilities)->groupBy('user_id')->count();
        $uniqueMonthlyUsers = collect([])->concat($monthlyTransactions)->concat($monthlyUtilities)->groupBy('user_id')->count();

        /**
         * @param mixed $name
         * ? work on the churn rate here
         */


        //*Average Annual Purchase Value
        $AAPV =  ($yearlyCount < 1) ? 0 : $yearlyVolume / $yearlyCount;

        //*Average Monthly Purchase Value
        $AMPV = ($monthlyCount < 1) ? 0 : $monthlyVolume / $monthlyCount;

        //*Average Annual Purchase Frequency
        $AAPF = ($uniqueYearlyUsers < 1) ?  0 : $yearlyCount / $uniqueYearlyUsers;

        //*Average Monthly Purchase Frequency 
        $AMPF = ($uniqueMonthlyUsers < 1) ? 0 : $monthlyCount / $uniqueMonthlyUsers;

        //*Customer Value Annually
        $CVA = $AAPV * $AAPF;

        //*Customer Value Monthly 
        $CVM = ($AMPF < 1) ? 0 : $AMPV / $AMPF;

        //*Average Customer LifeSpan
        // TODO: find how to calculate churnRate
        $ACL = 1;

        //*Average Customer Lifetime Value Annually
        $ACLV = $CVA * $ACL;

        $export_data = array(
            'AverageAnnualPurchaseValue' => $AAPV,
            'AverageMonthlyPurchaseValue' => $AMPV,
            'AverageAnnualPurchaseFrequency' => $AAPF,
            'AverageMonthlyPurchaseFrequency' => $AMPF,
            'CustomerValueAnnually' => $CVA,
            'CustomerValueMOnthly' => $CVM,
            'AverageCustomerLifespan' => $ACL,
            'AverageCustomerLifetimeValueAnnually' => $ACLV,
            'MonthlyTransactions' => $uniqueMonthlyUsers,
        );

        return $export_data;
    }
}
