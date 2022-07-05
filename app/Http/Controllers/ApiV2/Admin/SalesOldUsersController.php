<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalesTimestamp;
use App\Transaction;
use App\User;
use App\UserTracking;
use Carbon\Carbon;
use Carbon\CarbonInterval;
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

        $CalledUsers = UserTracking::where('called_date','>=',$start_date)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse'])->get();
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = UserTracking::where('called_date','>=',$start_date)->where('Current_Cycle','Responded')->get();
        $noOfRespondedUsers = $RespondedUsers->count();

        $respondedTransactions = $this->RespondedUnique($RespondedUsers);

        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($this->RespondedUnique($RespondedUsers)->count()/$noOfCalledUsers)*100;
        $respondedTranxNo = $respondedTransactions->count();
        $respondedTranxVolume = $respondedTransactions->sum('amount');
        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,null);

        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();
        if($category == 'calledUsersNo' OR $category == null)
        {
            $table_data = $CalledUsers;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','called_date','called_time','callDuration','remark','dp'])->all();
            $table_data = collect($table_data);
        }
        if($category == 'respondedUsersNo'){
            $table_data = $RespondedUsers;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','username','signUpDate','Responded_Cycle','Recalcitrant_Cycle','lastTranxDate','lastTranxVolume','dp'])->all();
            $table_data = collect($table_data);
        }
        $table_data = $table_data->sortByDesc('updated_at');

        return response()->json([
            'success' => true,
            'dropdown' => $salesOldUsers,
            'calledUsersNo' => $noOfCalledUsers,
            'respondedUsersNo' => $noOfRespondedUsers,
            'respondedUsersTrades' => $respondedTranxNo,
            'callPercentageEffectiveness' => $callPercentageEffectiveness,
            'respondedUsersVolume' => $respondedTranxVolume,
            'averageCallDuration' => $averageCallDuration,
            'totalCallDuration' => $totalCallDuration,
            'averageTimeBetweenCalls' => $averageTimeBetweenCalls,
            'data' => $table_data,
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
                return response()->json([
                    'success' => false,
                    'message' => "start date field is empty"
                ],401);
            }
            if(empty($data['end_date']) )
            {
                return response()->json([
                    'success' => false,
                    'message' => "end date field is empty"
                ],401);
            }

            $start_date = Carbon::parse($data['start_date']." 00:00:00");
            $end_date = Carbon::parse($data['end_date']." 23:59:59");
        }
        if($data['sorting_type'] == 'days')
        {
            if(empty($data['days']))
            {
                return response()->json([
                    'success' => false,
                    'message' => "days field is empty"
                ],401);
            }

            $start_date = now()->subDays($data['days']);
            $end_date = now();
        }
        if($data['sorting_type'] == 'month')
        {
            if(empty($data['month']))
            {
                return response()->json([
                    'success' => false,
                    'message' => "month field is empty"
                ],401);
            }
            if(empty($data['year']))
            {
                return response()->json([
                    'success' => false,
                    'message' => "year field is empty"
                ],401);
            }
            $start_date = Carbon::createFromDate($data['year'],$data['month'],1);
            $end_date = Carbon::createFromDate($data['year'],$data['month'],1)->endOfMonth();
        }
        if($data['sorting_type'] == 'quarterly')
        {
            if(empty($data['month']))
            {
                return response()->json([
                    'success' => false,
                    'message' => "month field is empty"
                ],401);
            }
            if(empty($data['year']))
            {
                return response()->json([
                    'success' => false,
                    'message' => "year field is empty"
                ],401);
            }
            $start_date = Carbon::createFromDate($data['year'],$data['month'],1)->subMonths(3);
            $end_date = Carbon::createFromDate($data['year'],$data['month'],1);
        }

        if($data['sorting_type'] == 'yearly')
        {
            if(empty($data['year']))
            {
                return response()->json([
                    'success' => false,
                    'message' => "year field is empty"
                ],401);
            }
            $start_date = Carbon::createFromDate($data['year'],1,1);
            $end_date = Carbon::createFromDate($data['year'],1,1)->endOfYear();
        }
        return [$start_date,$end_date];
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
        
        $CalledUsers = UserTracking::where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse']);
        if($request->sales_id != null){
            $CalledUsers = $CalledUsers->where('sales_id',$request->sales_id)->get();            
        }else{
            $CalledUsers = $CalledUsers->get();
        }
        
        $noOfCalledUsers = $CalledUsers->count();
        $RespondedUsers = UserTracking::where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','Responded');
        
        if($request->sales_id != null){
            $RespondedUsers = $RespondedUsers->where('sales_id',$request->sales_id)->get();
        }else{
            $RespondedUsers = $RespondedUsers->get();
        }
        $noOfRespondedUsers = $RespondedUsers->count();

        if($request->conversionType == "unique")
        {
            $respondedTransactions = $this->RespondedUnique($RespondedUsers);
        }
        else{
            $respondedTransactions = $this->RespondedTotal($RespondedUsers);
        }
        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($this->RespondedUnique($RespondedUsers)->count()/$noOfCalledUsers)*100;
        $respondedTranxNo = $respondedTransactions->count();
        $respondedTranxVolume = $respondedTransactions->sum('amount');

        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,$request->sales_id);
        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();

        if($request->category == 'calledUsersNo' OR $request->category == null)
        {
            $table_data = $CalledUsers;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','called_date','called_time','callDuration','remark','dp'])->all();
            $table_data = collect($table_data);
        }
        if($request->category == 'respondedUsersNo'){
            $table_data = $RespondedUsers;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','username','signUpDate','Responded_Cycle','Recalcitrant_Cycle','lastTranxDate','lastTranxVolume','dp'])->all();
            $table_data = collect($table_data);
        }
        $table_data = $table_data->sortByDesc('updated_at');

        return response()->json([
            'success' => true,
            'dropdown' => $salesOldUsers,
            'calledUsersNo' => $noOfCalledUsers,
            'respondedUsersNo' => $noOfRespondedUsers,
            'respondedUsersTrades' => $respondedTranxNo,
            'callPercentageEffectiveness' => $callPercentageEffectiveness,
            'respondedUsersVolume' => $respondedTranxVolume,
            'averageCallDuration' => $averageCallDuration,
            'totalCallDuration' => $totalCallDuration,
            'averageTimeBetweenCalls' => $averageTimeBetweenCalls,
            'data' => $table_data,
        ], 200);

    }

    public function CallDuration($table_data){
        foreach ($table_data as $td) {
            $called_timeStamp = $td->called_date;
            $td->callDuration = CarbonInterval::seconds($td->call_duration)->cascade()->forHumans();
            $td->called_date = Carbon::parse($called_timeStamp)->format('d M Y');
            $td->called_time = Carbon::parse($called_timeStamp)->format('h:ia');
            $td->remark = ($td->call_log) ? $td->call_log->call_response : null;

            if($td->user){
                $td->name = $td->user->first_name." ".$td->user->last_name;
                $td->username = $td->user->username;
                $td->dp = $td->user->dp;
                $td->signUpDate = $td->user->created_at->format('d M Y');
            }

            $data = Transaction::where('user_id',$td->user_id)->where('status','success')->orderBy('id','desc')->first();
            if($data)
            {
                $td->lastTranxDate = $data->created_at->format('d M Y');
                $td->lastTranxVolume = $data->amount;
            }
        }
    }
    public function RespondedUnique($data)
    {
        $uniqueData = [];
        foreach($data as $d)
        {
            $User_tnx = Transaction::where('user_id',$d->user_id)->where('updated_at','>=',$d->current_cycle_count_date)->where('status','success')->first();
            if($User_tnx != null)
            {
                $uniqueData[] = $User_tnx;
            }
        }
        $uniqueData = collect($uniqueData)->sortByDesc('updated_at');
        return $uniqueData;

    }

    public function RespondedTotal($data)
    {
        $totalData = collect([]);
        foreach($data as $d)
        {
            $User_tnx = Transaction::where('user_id',$d->user_id)->where('updated_at','>=',$d->current_cycle_count_date)->where('status','success')->get();
            if($User_tnx != null)
            {
                $totalData = $totalData->concat($User_tnx);
            }
        }
        $totalData = $totalData->sortByDesc('updated_at');
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
                ->where('called_date','<=',$st->inactiveTime)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse'])->get();
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
}
