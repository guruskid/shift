<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewUsersTracking;
use App\SalesTimestamp;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Validator;

class SalesNewUsersController extends Controller
{
    public function loadNewUsers($category = null)
    {
        
        $salesNewUsers = User::where('role',556)->get(['id','first_name','last_name', 'username']);

        $start_date = now()->format('Y-m-d');
        $start_date = Carbon::parse($start_date." 00:00:00");
        
        $end_date = now()->format('Y-m-d');
        $end_date = Carbon::parse($end_date." 23:59:59");

        $goodLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->where('status','goodlead')->get();

        $noGoodLeads = $goodLeads->count();
        $badLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->Where('status','badlead')->get();
        $noBadLeads = $badLeads->count();
        
        $calledUsers = $this->allGoodAndBadLeads($goodLeads,$badLeads);
        $noCalledUsers = $calledUsers->count();

        $goodLeadData = $this->conversionRateUnique($goodLeads,$noCalledUsers);
        $badLeadData = $this->conversionRateUnique($badLeads,$noCalledUsers);

        $goodLeadTransactions = $goodLeadData[0]['transactions'];
        $goodLeadConversionRate = $goodLeadData[0]['ConversationRate'];
        $goodLeadConversionAmount = $goodLeadData[0]['ConversionAmount'];
        
        $badLeadTransactions = $badLeadData[0]['transactions'];
        $badLeadConversionRate = $badLeadData[0]['ConversationRate'];
        $badLeadConversionAmount = $badLeadData[0]['ConversionAmount'];
        
        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,null);

        $totalConversionGoodandBadLeads = $goodLeadTransactions->count() + $badLeadTransactions->count();
        $totalConversionRate = ($noCalledUsers == 0) ? 0 : ($totalConversionGoodandBadLeads/$noCalledUsers)*100;

        $totalConversionVolume = number_format($goodLeadConversionAmount + $badLeadConversionAmount);
        
        $totalCallDuration = $calledUsers->sum('call_duration');
        $averageCallDuration = ($noCalledUsers == 0) ? 0 : ($totalCallDuration/$noCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();
        
        
        if($category == 'calledUsersNo' OR $category == null){
            $table_data = $calledUsers;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','username','called_date','called_time','callDuration','remark','dp'])->all();
            $table_data = collect($table_data);
        }
        if($category == 'goodLeadNo'){
            $table_data = $goodLeads;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','username','signUpDate','called_time','callDuration','remark','dp'])->all();
            $table_data = collect($table_data);
        }
        if($category == 'badLeadNo')
        {
            $table_data = $badLeads;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','username','signUpDate','callDuration','remark','dp'])->all();
            $table_data = collect($table_data);
        }
        if($category == 'goodLeadConversion' ){
            $table_data = $goodLeadTransactions;
            $table_data = $table_data->map->only(['id','name','username','signUpDate','TransDate','TransTime','TransVolume','dp'])->all();
            $table_data = collect($table_data);
        }
        if($category == 'badLeadConversion'){
            $table_data = $badLeadTransactions;
            $table_data = $table_data->map->only(['id','name','username','signUpDate','TransDate','TransTime','TransVolume','dp'])->all();
            $table_data = collect($table_data);
        }
        
        return response()->json([
            'success' => true,
            'dropdown' => $salesNewUsers,
            'calledUsersNo' => number_format($noCalledUsers),
            'goodLeadNo' => number_format($noGoodLeads),
            'goodLeadConversion'=>$goodLeadConversionRate,
            'badLeadNo' => number_format($noBadLeads),
            'averageTimeBetweenCalls' => $averageTimeBetweenCalls,
            'badLeadConversion'=>$badLeadConversionRate,
            'totalConversionRate' => $totalConversionRate,
            'totalConversionVolume' => $totalConversionVolume,
            'totalCallDuration' => $totalCallDuration,
            'averageCallDuration' => $averageCallDuration,
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

    public function sortNewUsers(Request $request)
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
        $salesNewUsers = User::where('role',556)->get(['id','first_name','last_name','username']);
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

        $goodLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->where('status','goodlead');
        if($request->sales_id != null){
            $goodLeads = $goodLeads->where('sales_id',$request->sales_id)->get();
        }else{
            $goodLeads = $goodLeads->get();
        }

        $noGoodLeads = $goodLeads->count();
        $badLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->Where('status','badlead');
        if($request->sales_id != null){
            $badLeads = $badLeads->where('sales_id',$request->sales_id)->get();
        }else{
            $badLeads = $badLeads->get();
        }
        $noBadLeads = $badLeads->count();

        $calledUsers = $this->allGoodAndBadLeads($goodLeads,$badLeads);
        $noCalledUsers = $calledUsers->count();

        if($request->conversionType == "unique")
        {
            $goodLeadData = $this->conversionRateUnique($goodLeads,$noCalledUsers);
            $badLeadData = $this->conversionRateUnique($badLeads,$noCalledUsers);
        }
        else{
            $goodLeadData = $this->conversionRateTotal($goodLeads,$noCalledUsers);
            $badLeadData = $this->conversionRateTotal($badLeads,$noCalledUsers);
        }

        $goodLeadTransactions = $goodLeadData[0]['transactions'];
        $goodLeadConversionRate = $goodLeadData[0]['ConversationRate'];
        $goodLeadConversionAmount = $goodLeadData[0]['ConversionAmount'];
        
        $badLeadTransactions = $badLeadData[0]['transactions'];
        $badLeadConversionRate = $badLeadData[0]['ConversationRate'];
        $badLeadConversionAmount = $badLeadData[0]['ConversionAmount'];

        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,$request->sales_id);

        $totalConversionGoodandBadLeads = $goodLeadTransactions->count() + $badLeadTransactions->count();
        $totalConversionRate = ($noCalledUsers == 0) ? 0 : ($totalConversionGoodandBadLeads/$noCalledUsers)*100;

        $totalConversionVolume = number_format($goodLeadConversionAmount + $badLeadConversionAmount);

        $totalCallDuration = $calledUsers->sum('call_duration');
        $averageCallDuration = ($noCalledUsers == 0) ? 0 : ($totalCallDuration/$noCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();

        
        if($request->category == 'calledUsersNo' OR $request->category == null){
            $table_data = $calledUsers;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','username','called_date','called_time','callDuration','remark','dp'])->all();
            $table_data = collect($table_data);
        }
        if($request->category == 'goodLeadNo'){
            $table_data = $goodLeads;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','username','signUpDate','called_time','callDuration','remark','dp'])->all();
            $table_data = collect($table_data);
        }
        if($request->category == 'badLeadNo')
        {
            $table_data = $badLeads;
            $this->CallDuration($table_data);
            $table_data = $table_data->map->only(['id','user_id','name','username','signUpDate','callDuration','remark','dp'])->all();
            $table_data = collect($table_data);
        }
        if($request->category == 'goodLeadConversion' ){
            $table_data = $goodLeadTransactions;
            $table_data = $table_data->map->only(['id','name','username','signUpDate','TransDate','TransTime','TransVolume','dp'])->all();
            $table_data = collect($table_data);
        }
        if($request->category == 'badLeadConversion'){
            $table_data = $badLeadTransactions;
            $table_data = $table_data->map->only(['id','name','username','signUpDate','TransDate','TransTime','TransVolume','dp'])->all();
            $table_data = collect($table_data);
        }
        

        return response()->json([
            'success' => true,
            'dropdown' => $salesNewUsers,
            'calledUsersNo' => number_format($noCalledUsers),
            'goodLeadNo' => number_format($noGoodLeads),
            'goodLeadConversion'=>$goodLeadConversionRate,
            'badLeadNo' => number_format($noBadLeads),
            'averageTimeBetweenCalls' => $averageTimeBetweenCalls,
            'badLeadConversion'=>$badLeadConversionRate,
            'totalConversionRate' => $totalConversionRate,
            'totalConversionVolume' => $totalConversionVolume,
            'totalCallDuration' => $totalCallDuration,
            'averageCallDuration' => $averageCallDuration,
            'data' => $table_data,
        ], 200);

    }
    public function allGoodAndBadLeads($goodLead, $badLead)
    {
        $calledUser =  collect([]);
        $calledUser = $calledUser->concat($goodLead);
        $calledUser = $calledUser->concat($badLead);
        return $calledUser->sortBy('id');
    }  

    public function CallDuration($table_data){
        foreach ($table_data as $td) {
            $td->callDuration = CarbonInterval::seconds($td->call_duration)->cascade()->forHumans();
            $called_timeStamp = $td->call_duration_timestamp;
            $td->called_date = Carbon::parse($called_timeStamp)->format('d M Y');
            $td->called_time = Carbon::parse($called_timeStamp)->format('h:ia');
            $td->remark = $td->comment;
            $td->username = ($td->user) ? $td->user->username : null;
            $td->name = ($td->user) ? $td->user->first_name." ".$td->user->last_name : null;
            $td->signUpDate = Carbon::parse($td->created_at)->format('d M Y');
        }
    }

    public function conversionRateUnique($data,$noCalledUsers)
    {
        $tdata = [];

        foreach ($data as $d) {
            $user_transactionsUnique = Transaction::where('user_id',$d->user_id)->where('status','success')->first();
            if($user_transactionsUnique != null)
            {
                $tdata[] = $user_transactionsUnique;
            }
        }
        $tdata = collect(($tdata))->sortByDesc('updated_at');
        foreach($tdata as $td)
        {
            $td->TransDate = Carbon::parse($td->created_at)->format('d M Y');
            $td->TransTime = Carbon::parse($td->created_at)->format('h:ia');
            $td->TransVolume = $td->amount;
            if($td->user)
            {
                $td->signUpDate = Carbon::parse($td->user->created_at)->format('d M Y');
                $td->name = $td->user->first_name." ".$td->user->last_name;
                $td->username = $td->user->username;
                $td->name = $td->user->first_name." ".$td->user->last_name;
            }
        }
        $ConversionUnique = ($noCalledUsers == 0) ? 0 : ($tdata->count()/$noCalledUsers)*100;
        
        $ConversionAmountUnique = $tdata->sum('amount');
        $returnData = array([
            'transactions' => $tdata,
            'ConversationRate' => $ConversionUnique,
            'ConversionAmount' => $ConversionAmountUnique
        ]);
        return $returnData;
    }

    public function conversionRateTotal($data,$noCalledUsers)
    {
        $TransactionsTotal = collect([]);
        
        foreach ($data as $d) {
            $user_transactionsTotal = Transaction::where('user_id',$d->user_id)->where('status','success')->get();
            if($user_transactionsTotal->count() > 0){
                $TransactionsTotal = $TransactionsTotal->concat($user_transactionsTotal);
            }

        }
        foreach($TransactionsTotal as $tt)
        {
            $tt->TransDate = Carbon::parse($tt->created_at)->format('d M Y');
            $tt->TransTime = Carbon::parse($tt->created_at)->format('h:ia');
            $tt->TransVolume = $tt->amount;
            if($tt->user)
            {
                $tt->signUpDate = Carbon::parse($tt->user->created_at)->format('d M Y');
                $tt->name = $tt->user->first_name." ".$tt->user->last_name;
                $tt->username = $tt->user->username;
                $tt->name = $tt->user->first_name." ".$tt->user->last_name;
            }
        }
        $ConversionTotal = ($noCalledUsers == 0) ? 0 : ($TransactionsTotal->count()/$noCalledUsers)*100;
        $ConversionAmountTotal = $TransactionsTotal->sum('amount');

        $returnData = array([
            'transactions' => $TransactionsTotal,
            'ConversationRate'=> $ConversionTotal,
            'ConversionAmount' => $ConversionAmountTotal

            
        ]);
        return $returnData;
    }

    public function sortAverageTimeBetweenCalls($start_date,$end_date,$sales_id)
    {
        //* step 1 find the time stamp within the time frame 
        $sales_timestamp = SalesTimestamp::whereHas('user', function ($query) {
            $query->where('role', 556);
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
                $calledUsers = NewUsersTracking::where('updated_at','>=',$st->activeTime)
                ->where('updated_at','<=',$st->inactiveTime)->get();
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
