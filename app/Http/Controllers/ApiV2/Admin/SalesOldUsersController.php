<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Card;
use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiV2\Admin\CalledUserResource;
use App\Http\Resources\ApiV2\Admin\RespondedTransactionResource;
use App\Http\Resources\ApiV2\Admin\RespondedUserResource;
use App\SalesTimestamp;
use App\TargetSettings;
use App\Transaction;
use App\User;
use App\UserTracking;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

class SalesOldUsersController extends Controller
{
    public function loadOldUsers($category = null)
    {
        $salesOldUsers = User::where('role',557)->get(['id','first_name','last_name','username']);

        $start_date = now()->format('Y-m-d');
        $start_date = Carbon::parse($start_date." 00:00:00");
        
        $end_date = now()->format('Y-m-d');
        $end_date = Carbon::parse($end_date." 23:59:59");

        $CalledUsers = UserTracking::with('transactions','utilityTransaction','depositTransactions','user','call_log')->where('called_date','>=',$start_date)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse','DeadUser'])->get();
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = UserTracking::with('transactions','utilityTransaction','depositTransactions','user','call_log')->where('called_date','>=',$start_date)->where('Current_Cycle','Responded')->get();
        $noOfRespondedUsers = $RespondedUsers->count();

        $respondedTransactions = $this->RespondedUnique($RespondedUsers);

        $respondedTranxNo = $respondedTransactions->count();
        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($noOfRespondedUsers/$noOfCalledUsers)*100;

        $RespondedUsersTotal = UserTracking::with('transactions','utilityTransaction','depositTransactions','user','call_log')->where('called_date','>=',$start_date)->where('Current_Cycle','Responded')->get();
        $totalRespondedUserTotal = $this->RespondedTotal($RespondedUsersTotal);

        $respondedTranxVolume = $totalRespondedUserTotal->sum('amount');

        $targetRespondedUsers = UserTracking::with('transactions','utilityTransaction','depositTransactions','user','call_log')
        ->whereMonth('called_date',now()->month)->where('Current_Cycle','Responded')->get();
        $targetTranxVolume = $this->RespondedTotal($targetRespondedUsers)->sum('amount');

        $targetCovered = $this->targetCovered($targetTranxVolume,null);

        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,null);

        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();

        $quarterlyInactiveUsersNo = UserTracking::where('Current_Cycle','QuarterlyInactive')->count();

        if($category == 'calledUsersNo' OR $category == null)
        {
            $table_data = CalledUserResource::collection($CalledUsers);
            $table_data = collect($table_data);
        }
        if($category == 'respondedUsersNo'){
            $table_data = RespondedUserResource::collection($RespondedUsers);
            $table_data = collect($table_data);
        }
        if($category == 'respondedUsersTrades')
        {
            $giftCardKeys = Card::where('is_crypto',0)->get();
            $giftCardKeys = $giftCardKeys->pluck('id')->toArray();

            $table_data = RespondedTransactionResource::customCollection($respondedTransactions, $giftCardKeys);
            $table_data = collect($table_data);

        }
        $table_data = $table_data->sortByDesc('updated_at');
        $tranx = array();

        foreach($table_data as $td)
        {
            $tranx[] = $td; 
        }

        return response()->json([
            'success' => true,
            'dropdown' => $salesOldUsers,
            'calledUsersNo' => $noOfCalledUsers,
            'respondedUsersNo' => $noOfRespondedUsers,
            'respondedUsersTrades' => $respondedTranxNo,
            'callPercentageEffectiveness' => number_format($callPercentageEffectiveness,2,".",","),
            'respondedUsersVolume' => number_format($respondedTranxVolume,2,".",","),
            'averageCallDuration' => $averageCallDuration,
            'totalCallDuration' => $totalCallDuration,
            'averageTimeBetweenCalls' => $averageTimeBetweenCalls,
            'quarterlyInactiveUsers' => number_format($quarterlyInactiveUsersNo),
            'targetCovered' => number_format($targetCovered,2,".",","),
            'data' => $tranx,
        ], 200);
    }

    public function sortingType($data)
    {
        $start_date = null;
        $end_date = null;
        if($data['sorting_type'] == 'period')
        {
            if(empty($data['start_date']))
            {
                return "start date field is empty";
            }
            if(empty($data['end_date']) )
            {
                return "end date field is empty";
            }

            $start_date = Carbon::parse($data['start_date']." 00:00:00");
            $end_date = Carbon::parse($data['end_date']." 23:59:59");
        }
        if($data['sorting_type'] == 'days')
        {
            if(empty($data['days']))
            {
                return "days field is empty";
            }

            $start_date = now()->subDays($data['days']);
            $end_date = now();
        }
        if($data['sorting_type'] == 'month')
        {
            if(empty($data['month']))
            {
                return "month field is empty";
            }
            if(empty($data['year']))
            {
                return "year field is empty";
            }
            $start_date = Carbon::createFromDate($data['year'],$data['month'],1);
            $end_date = Carbon::createFromDate($data['year'],$data['month'],1)->endOfMonth();
        }
        if($data['sorting_type'] == 'quarterly')
        {
            if(empty($data['month']))
            {
                return "month field is empty";
            }
            if(empty($data['year']))
            {
                return "year field is empty";
            }
            $start_date = Carbon::createFromDate($data['year'],$data['month'],1)->subMonths(3);
            $end_date = Carbon::createFromDate($data['year'],$data['month'],1);
        }

        if($data['sorting_type'] == 'yearly')
        {
            if(empty($data['year']))
            {
                return "year field is empty";
            }
            $start_date = Carbon::createFromDate($data['year'],1,1);
            $end_date = Carbon::createFromDate($data['year'],1,1)->endOfYear();
        }
        return [$start_date,$end_date];
    }

    public function SortingBySalesID($start_date, $end_date, $sales_id, $category)
    {
        if($category == 'called'):
            $data = UserTracking::where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse','DeadUser']);
        endif;

        if($category == 'responded'):
            $data = UserTracking::where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','Responded');
        endif;

        if($sales_id != null){
            $data = $data->where('sales_id',$sales_id)->get();            
        }else{
            $data = $data->get();
        }

        return $data;
    }

    public function sortOldUsers(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'sorting_type' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $time_date = $this->sortingType($request->all());
        $salesOldUsers = User::where('role',557)->get(['id','first_name','last_name','username']);
        
        if((is_string($time_date)) == true)
        {
            return response()->json([
                'success' => false,
                'message' =>$time_date
            ],401);
        }

        $start_date = $time_date[0];
        if($start_date == null){
            $start_date = now()->format('Y-m-d');
            $start_date = Carbon::parse($start_date." 00:00:00");
        }

        $end_date = $time_date[1];
        if($end_date == null){
            $end_date = now()->format('Y-m-d');
            $end_date = Carbon::parse($end_date." 23:59:59");
        }

        $CalledUsers = $this->SortingBySalesID($start_date,$end_date, $request->sales_id, 'called');
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = $this->SortingBySalesID($start_date,$end_date, $request->sales_id, 'responded');
        $noOfRespondedUsers = $RespondedUsers->count();

        if($request->conversionType == "unique")
        {
            $respondedTransactions = $this->RespondedUnique($RespondedUsers);
        }
        else{
            $respondedTransactions = $this->RespondedTotal($RespondedUsers);
        }

        $respondedTranxNo = $respondedTransactions->count();
        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($noOfRespondedUsers/$noOfCalledUsers)*100;

        $RespondedUsersTotal = $RespondedUsers = $this->SortingBySalesID($start_date,$end_date, $request->sales_id, 'responded');
        $totalRespondedUserTotal = $this->RespondedTotal($RespondedUsersTotal);
        $respondedTranxVolume = $totalRespondedUserTotal->sum('amount');

        $targetCovered = $this->targetCovered($respondedTranxVolume,$request->sales_id);

        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,$request->sales_id);
        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();

        $quarterlyInactiveUsersNo = UserTracking::where('Current_Cycle','QuarterlyInactive')
        ->where('current_cycle_count_date','>=',$start_date)->where('current_cycle_count_date','<=',$end_date)->count();

        if($request->category == 'calledUsersNo' OR $request->category == null)
        {
            $table_data = CalledUserResource::collection($CalledUsers);
            $table_data = collect($table_data);
        }
        if($request->category == 'respondedUsersNo'){
            $table_data = RespondedUserResource::collection($RespondedUsers);
            $table_data = collect($table_data);
        }

        if($request->category == 'respondedUsersTrades')
        {
            $giftCardKeys = Card::where('is_crypto',0)->get();
            $giftCardKeys = $giftCardKeys->pluck('id')->toArray();

            $table_data = RespondedTransactionResource::customCollection($respondedTransactions, $giftCardKeys);
            $table_data = collect($table_data);

        }
        $table_data = $table_data->sortByDesc('updated_at');
        $tranx = array();

        foreach($table_data as $td)
        {
            $tranx[] = $td; 
        }

        return response()->json([
            'success' => true,
            'dropdown' => $salesOldUsers,
            'calledUsersNo' => $noOfCalledUsers,
            'respondedUsersNo' => $noOfRespondedUsers,
            'respondedUsersTrades' => $respondedTranxNo,
            'callPercentageEffectiveness' => number_format($callPercentageEffectiveness,2,".",","),
            'respondedUsersVolume' => number_format($respondedTranxVolume,2,".",","),
            'averageCallDuration' => $averageCallDuration,
            'totalCallDuration' => $totalCallDuration,
            'averageTimeBetweenCalls' => $averageTimeBetweenCalls,
            'quarterlyInactiveUsers' => number_format($quarterlyInactiveUsersNo),
            'targetCovered' => number_format($targetCovered,2,".",","),
            'data' => $tranx,
        ], 200);

    }

    public function RespondedUnique($data)
    {
        //?do a performance check on this (to many foreach statements check execution time)

        $usdRate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        $uniqueData = [];
        foreach($data as $d)
        {
            if($d['utilityTransaction']->count() > 0)
            {
                foreach($d['utilityTransaction']->where('created_at','>=',$d->called_date) as $util)
                {
                    $util->amount = $util->amount/$usdRate;
                    $util->tranxCard = $util->type." Utility";
                }
            }

            if($d['depositTransactions']->count() > 0)
            {
                foreach($d['depositTransactions']->where('created_at','>=',$d->called_date) as $deposit)
                {
                    $deposit->amount = $deposit->amount/$usdRate;
                    $deposit->tranxCard = "PayBridge ".ucfirst($deposit->type);
                }
            }
        
            $allTranx = collect()->concat($d['transactions'])->concat($d['depositTransactions'])->concat($d['utilityTransaction']);

            $userTranx = $allTranx->where('created_at','>=',$d->called_date)->sortByDesc('created_at')->first();
            if($userTranx != null)
            {
                $uniqueData[] = $userTranx;
            }
        }
        $uniqueData = collect($uniqueData)->sortByDesc('created_at');
        return $uniqueData;

    }

    public function RespondedTotal($data)
    {
        $totalData = collect([]);
        $usdRate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        foreach($data as $d)
        {
            if($d['utilityTransaction']->count() > 0)
            {
                foreach($d['utilityTransaction']->where('created_at','>=',$d->called_date) as $util)
                {
                    $util->amount = $util->amount/$usdRate;
                    $util->tranxCard = $util->type." Utility";
                }
            }

            if($d['depositTransactions']->count() > 0)
            {
                foreach($d['depositTransactions']->where('created_at','>=',$d->called_date) as $deposit)
                {
                    $deposit->amount = $deposit->amount/$usdRate;
                    $deposit->tranxCard = "PayBridge ".ucfirst($deposit->type);
                }
            }
            $allTranx = collect()->concat($d['transactions'])->concat($d['depositTransactions'])->concat($d['utilityTransaction'])->where('created_at','>=',$d->called_date);
            if($allTranx != null)
            {
                $totalData = $totalData->concat($allTranx);
            }
        }
        $totalData = $totalData->sortByDesc('created_at');
        return $totalData;
    }

    public function sortAverageTimeBetweenCalls($start_date,$end_date,$sales_id)
    { 
        //* step 1 find the time stamp within the time frame where user is a Sales Old user
        $sales_timestamp = SalesTimestamp::whereHas('user', function ($query) {
            $query->where('role', 557);
            })->whereDate('activeTime','>=',$start_date)->whereDate('activeTime','<=',$end_date);
        if($sales_id){ 
            $sales_timestamp = $sales_timestamp->where('user_id',$sales_id);
        }
        $sales_timestamp = $sales_timestamp->get();
        $avgTotalDiff = 0;
        if($sales_timestamp){
            foreach ($sales_timestamp as $st) {
                if($st->inactiveTime == null){
                    $st->inactiveTime = now();
                }
                //* for each timestamp check the called users 
                $calledUsers = UserTracking::where('called_date','>=',$st->activeTime)
                ->where('called_date','<=',$st->inactiveTime)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse','DeadUser'])->get();
                $previousDatetime = null;
                //* allocate the previous call duration timestamp to be able to calculate time difference
                foreach ($calledUsers as $cu) {
                    $cu->previous_call_duration_timestamp = $previousDatetime;
                    $previousDatetime = $cu->call_duration_timestamp;
                }
                $timeDiff = 0;
                //* now checking for the time difference and converting it into seconds 
                foreach ($calledUsers as $cu) {
                    if($cu->previous_call_duration_timestamp != null)
                    {
                        $timeDiff += Carbon::parse($cu->call_duration_timestamp)->diffInSeconds($cu->previous_call_duration_timestamp);
                    }
                }
                $noOfCalledUsers = $calledUsers->count();
                $totalRestTime = $noOfCalledUsers * 60;
                $timeAfterRest = $timeDiff - $totalRestTime;
                $averageTimeBetweenCalls = ($noOfCalledUsers == 0) ? 0 : ($timeAfterRest/$noOfCalledUsers);
                $avgTotalDiff += $averageTimeBetweenCalls;
            }
            
        }
        //*after getting the summation of the average difference divide by total active time
        //*and convert readable time for humans.
        $totalTimeValue = (count($sales_timestamp) == 0) ? 0 : $avgTotalDiff/count($sales_timestamp);
        return (count($sales_timestamp) == 0) ? 0 : CarbonInterval::seconds($totalTimeValue)->cascade()->forHumans();
    }

    public function targetCovered($amount,$id)
    {
        $targetData = TargetSettings::query();
        if($id != null):
            $targetData = $targetData->where('user_id',$id)->target;
            return (($amount/$targetData)*100);
        endif;

        $targetData = $targetData->sum('target');
        return (($amount/$targetData)*100);
    }
}
