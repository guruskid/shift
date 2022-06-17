<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\UserTracking;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class OldUsersSalesAnalytics extends Controller
{
    public function index($type = null)
    {
        $conversionType = "unique";
        $start_date =  null;
        
        $type = ($type != null) ? $type : "calledUsers";
        return $this->loadData($start_date,$conversionType,$type);
    }

    public function sortingAnalytics(Request $request, $type = null)
    {
        $type = ($type != null) ? $type : "calledUsers";
        if(!empty($request->all())){
            $request->session()->put('SortingKeys',$request->all());
        }
        $request_data = $request->session()->get('SortingKeys');
        if(!empty($request_data)){
            $conversionType = "unique";

            $start_date = ($request_data['start']) ? $request_data['start'] : null;
            $last_date = ($request_data['end']) ? $request_data['end'] : null;

            if(isset($request_data['unique']) AND $request_data['unique'] =="on"){
                $conversionType = "unique";
            }
            if(isset($request_data['total']) AND $request_data['total'] =="on"){
                $conversionType = "total";
            }
            $type = ($type != null) ? $type : "calledUsers";
            return $this->SortingLoadData($start_date,$last_date,$conversionType,$type,$request_data['sales']);
        }
    }

    public function LoadData($start_date,$conversionType,$type)
    {
        $salesOldUsers = User::where('role',557)->get();
        $start_date = now()->format('Y-m-d');
        $start_date = Carbon::parse($start_date." 00:00:00");
        
        $end_date = now()->format('Y-m-d');
        $end_date = Carbon::parse($end_date." 23:59:59");

        $CalledUsers = UserTracking::where('called_date','>=',$start_date)->where('Current_Cycle','!=','QuarterlyInactive')->get();
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = UserTracking::where('called_date','>=',$start_date)->where('Current_Cycle','Responded')->get();
        $noOfRespondedUsers = $RespondedUsers->count();

        if($conversionType == "unique")
        {
            $respondedTransactions = $this->RespondedUnique($RespondedUsers);
            $unique = 1;
            $total = 0;
        }
        else{
            $respondedTransactions = $this->RespondedTotal($RespondedUsers);
            $unique = 0;
            $total = 1;
        }
        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($this->RespondedUnique($RespondedUsers)->count()/$noOfCalledUsers)*100;
        $respondedTranxNo = $respondedTransactions->count();
        $respondedTranxVolume = $respondedTransactions->sum('amount');
        $averageTimeBetweenCalls = $this->AverageTimeBetweenCalls($CalledUsers);

        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();
        $show_data = false;
        $table_data = $CalledUsers;
        if($type == 'calledUsers'){
            $table_data = $CalledUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'respondedUsers'){
            $table_data = $RespondedUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        $table_data = $table_data->sortByDesc('updated_at')->take(10);
        foreach($table_data as $td)
        {
            $data = Transaction::where('user_id',$td->user_id)->where('status','success')->orderBy('id','desc')->first();
            $td->lastTranxDate = $data->created_at;
            $td->lastTranxVolume = $data->amount;
        }
        $segment = " Sales Old Users Analytics";
        return view('admin.oldUsersSalesAnalytics.index',compact([
            'show_data','segment','noOfCalledUsers','averageTimeBetweenCalls','respondedTranxVolume','respondedTranxNo','noOfRespondedUsers'
            ,'totalCallDuration','averageCallDuration','type','table_data','callPercentageEffectiveness','unique','total','salesOldUsers'
        ]));

    }

    public function SortingLoadData($start_date ,$end_date,$conversionType,$type,$sales_id)
    {
        $segment = null;
        $salesUser = User::find($sales_id); 
        if($salesUser){
            $segment .= $salesUser->first_name." ".$salesUser->last_name." ";
        }
        $salesOldUsers = User::where('role',557)->get();
        if($start_date == null){
            $start_date = now()->format('Y-m-d');
        }
        $start_date = Carbon::parse($start_date." 00:00:00");
        $segment .= $start_date->format('d M Y');
        if($end_date == null){
            $end_date = now()->format('Y-m-d');
        }
        $end_date = Carbon::parse($end_date." 23:59:59");
        $segment .= " to ".$end_date->format('d M Y');

        if(isset($request_data['unique']) AND $request_data['unique'] =="on"){
            $conversionType = "unique";
        }
        if(isset($request_data['total']) AND $request_data['total'] =="on"){
            $conversionType = "total";
        }

        $CalledUsers = UserTracking::where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','!=','QuarterlyInactive')->get();
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = UserTracking::where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','Responded')->get();
        $noOfRespondedUsers = $RespondedUsers->count();
        if($conversionType == "unique")
        {
            $respondedTransactions = $this->RespondedUnique($RespondedUsers);
            $unique = 1;
            $total = 0;
        }
        else{
            $respondedTransactions = $this->RespondedTotal($RespondedUsers);
            $unique = 0;
            $total = 1;
        }
        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($this->RespondedUnique($RespondedUsers)->count()/$noOfCalledUsers)*100;
        $respondedTranxNo = $respondedTransactions->count();
        $respondedTranxVolume = $respondedTransactions->sum('amount');

        $averageTimeBetweenCalls = $this->AverageTimeBetweenCalls($CalledUsers);
        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();
        $show_data = false;
        $table_data = $CalledUsers;
        if($type == 'calledUsers'){
            $table_data = $CalledUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'respondedUsers'){
            $table_data = $RespondedUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        $table_data = $table_data->sortByDesc('updated_at')->paginate(1000);
        foreach($table_data as $td)
        {
            $data = Transaction::where('user_id',$td->user_id)->where('status','success')->orderBy('id','desc')->first();
            $td->lastTranxDate = $data->created_at;
            $td->lastTranxVolume = $data->amount;
        }
        return view('admin.oldUsersSalesAnalytics.sort',compact([
            'show_data','segment','noOfCalledUsers','averageTimeBetweenCalls','respondedTranxVolume','respondedTranxNo','noOfRespondedUsers'
            ,'totalCallDuration','averageCallDuration','type','table_data','callPercentageEffectiveness','unique','total','salesOldUsers'
        ]));
    }

    public function showAllData(Request $request, $type = null)
    {
        $request_data = $request->session()->get('SortingKeys');
        $start_date = (isset($request_data['start'])) ? $request_data['start'] : null;
        $end_date = (isset($request_data['end'])) ? $request_data['end'] : null;
        $sales_id = (isset($request_data['sales'])) ? $request_data['sales'] : 0;

        $conversionType = "unique";
        if(isset($request_data['unique']) AND $request_data['unique'] =="on"){
            $conversionType = "unique";
        }
        if(isset($request_data['total']) AND $request_data['total'] =="on"){
            $conversionType = "total";
        }
        $segment = null;
        $salesUser = User::find($sales_id); 
        if($salesUser){
            $segment .= $salesUser->first_name." ".$salesUser->last_name." ";
        }
        $salesOldUsers = User::where('role',557)->get();
        if($start_date == null){
            $start_date = now()->format('Y-m-d');
        }
        $start_date = Carbon::parse($start_date." 00:00:00");
        $segment .= $start_date->format('d M Y');
        if($end_date == null){
            $end_date = now()->format('Y-m-d');
        }
        $end_date = Carbon::parse($end_date." 23:59:59");
        $segment .= " to ".$end_date->format('d M Y');

        $CalledUsers = UserTracking::where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','!=','QuarterlyInactive')->get();
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = UserTracking::where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','Responded')->get();
        $noOfRespondedUsers = $RespondedUsers->count();

        if($conversionType == "unique")
        {
            $respondedTransactions = $this->RespondedUnique($RespondedUsers);
            $unique = 1;
            $total = 0;
        }
        else{
            $respondedTransactions = $this->RespondedTotal($RespondedUsers);
            $unique = 0;
            $total = 1;
        }
        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($this->RespondedUnique($RespondedUsers)->count()/$noOfCalledUsers)*100;
        $respondedTranxNo = $respondedTransactions->count();
        $respondedTranxVolume = $respondedTransactions->sum('amount');
        $averageTimeBetweenCalls = $this->AverageTimeBetweenCalls($CalledUsers);

        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();
        $show_data = false;
        $table_data = $CalledUsers;
        if($type == 'calledUsers'){
            $table_data = $CalledUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'respondedUsers'){
            $table_data = $RespondedUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        $table_data = $table_data->sortByDesc('updated_at')->paginate(1000);
        foreach($table_data as $td)
        {
            $data = Transaction::where('user_id',$td->user_id)->where('status','success')->orderBy('id','desc')->first();
            $td->lastTranxDate = $data->created_at;
            $td->lastTranxVolume = $data->amount;
        }
        return view('admin.oldUsersSalesAnalytics.show',compact([
            'show_data','segment','type','table_data'
        ]));

    }

    public function CallDuration($table_data){
        foreach ($table_data as $td) {
            $td->callDuration = CarbonInterval::seconds($td->call_duration)->cascade()->forHumans();
            $td->called_date = Carbon::parse($td->called_date);
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

    public function averageTimeBetweenCalls($calledUsers)
    {
        $previousDatetime = null;
        foreach ($calledUsers as $cu) {
            $cu->previous_call_duration_timestamp = $previousDatetime;
            $previousDatetime = $cu->call_duration_timestamp;
        }
        $timeDiff = 0;
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
        if($calledUsers->count() == 0){
            return 0;
        }else{
            return CarbonInterval::seconds($averageTimeBetweenCalls)->cascade()->forHumans();
        }
    }
}
