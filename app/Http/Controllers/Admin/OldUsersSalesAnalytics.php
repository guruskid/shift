<?php

namespace App\Http\Controllers\Admin;

use App\Card;
use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalesTimestamp;
use App\TargetSettings;
use App\Transaction;
use App\User;
use App\UserTracking;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class OldUsersSalesAnalytics extends Controller
{ 
     public function monthAndYear()
     {
        $list_of_years = Transaction::select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        $years = $list_of_years->pluck('year');

        $month = [
            ['month'=>'january','number'=>1],
            ['month'=>'february','number'=>2],
            ['month'=>'march','number'=>3],
            ['month'=>'april','number'=>4],
            ['month'=>'may','number'=>5],
            ['month'=>'june','number'=>6],
            ['month'=>'july','number'=>7],
            ['month'=>'august','number'=>8],
            ['month'=>'september','number'=>9],
            ['month'=>'october','number'=>10],
            ['month'=>'november','number'=>11],
            ['month'=>'december','number'=>12],
        ];
        return [$years , $month];
     }

     public function sortingType($sortingType){
        $start_date  = null;
        $end_date = null;
        
        if($sortingType['sortingType'] == 'period')
        {
            //*checking if the start end end date is available
            if(empty($sortingType['start']) || empty($sortingType['end']))
            {
                return 'Missing Date Fields';
            }
            $start_date = Carbon::parse($sortingType['start']." 00:00:00");
            $end_date = Carbon::parse($sortingType['end']." 23:59:59");
        }
        if($sortingType['sortingType'] == 'days')
        {
            //*checking if dates field is empty
            if(empty($sortingType['days']) ){
                return 'Days field Empty';
            }
            $start_date = now()->subDays($sortingType['days']);
            $end_date = now();
        }
        //*checking month and year fields
        if($sortingType['sortingType'] == 'monthly')
        {
            if(empty($sortingType['month']) || empty($sortingType['Year']))
            {
                return 'Missing Month or Year Field';
            }   
            $start_date = Carbon::createFromDate($sortingType['Year'],$sortingType['month'],1);
            $end_date = Carbon::createFromDate($sortingType['Year'],$sortingType['month'],1)->endOfMonth();
        }
        if($sortingType['sortingType'] == 'quarterly')
        {
            if(empty($sortingType['month']) || empty($sortingType['Year']))
            {
                return 'Missing Month or Year Field';
            }   
            $start_date = Carbon::createFromDate($sortingType['Year'],$sortingType['month'],1)->subMonths(3);
            $end_date = Carbon::createFromDate($sortingType['Year'],$sortingType['month'],1)->endOfMonth();
        }
        if($sortingType['sortingType'] == 'yearly')
        {
            if(empty($sortingType['Year']))
            {
                return 'Missing Year Field';
            }  
            $start_date = Carbon::createFromDate($sortingType['Year'],1,1);
            $end_date = Carbon::createFromDate($sortingType['Year'],1,1)->endOfYear();
        }

        return [$start_date ,$end_date];
     }

    public function index($type = null, Request $request)
    {
        $request->session()->forget(['SortingKeys','startKey','endKey']);
        $conversionType = "unique";
        
        $type = ($type != null) ? $type : "calledUsers";
        $salesOldUsers = User::where('role',557)->get();

        $start_date = now()->format('Y-m-d');
        $start_date = Carbon::parse($start_date." 00:00:00");
        
        $end_date = now()->format('Y-m-d');
        $end_date = Carbon::parse($end_date." 23:59:59");

        $CalledUsers = UserTracking::with('transactions','user')->where('called_date','>=',$start_date)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse','DeadUser'])->get();
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = UserTracking::with('transactions','user')->where('called_date','>=',$start_date)
        ->where('Current_Cycle','Responded')->get();
        $noOfRespondedUsers = $RespondedUsers->count();

        //? on base loading it should just show unique transactions
        $respondedTransactions = $this->RespondedUnique($RespondedUsers);
        $unique = 1;
        $total = 0;

        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($noOfRespondedUsers/$noOfCalledUsers)*100;

        $totalRespondedUserTotal = $this->RespondedTotal($RespondedUsers);
        $respondedTranxVolume = $totalRespondedUserTotal->sum('amount');

        $targetRespondedUsers = UserTracking::with('transactions','user','call_log')
        ->whereMonth('called_date',now()->month)->where('Current_Cycle','Responded')->get();
        $targetTranxVolume = $this->RespondedTotal($targetRespondedUsers)->sum('amount');

        $targetCovered = $this->targetCovered($targetTranxVolume,null);

        $respondedTranxNo = $respondedTransactions->count();
        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,null);

        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();

        $quarterlyInactiveUsersNo = UserTracking::where('Current_Cycle','QuarterlyInactive')->count();

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
        if($type == 'TradesRespondedUsers')
        {
            $table_data = $respondedTransactions;
            $show_data = true;
        }
        $table_data = $table_data->sortByDesc('updated_at')->paginate(1000);

        if(in_array($type,['calledUsers','respondedUsers'])){
            foreach($table_data as $td)
            {
                $allTranx = collect()->concat($td['transactions']);
                $data = $allTranx->where('created_at','>=',$td->called_date)->sortByDesc('created_at')->first();

                if($data)
                {
                    $td->lastTranxDate = $data->created_at;
                    $td->lastTranxVolume = $data->amount;
                }
                
            }
        }

        $giftCardKeys = null;
        if(in_array($type,['TradesRespondedUsers']))
        {
            $giftCardKeys = Card::where('is_crypto',0)->get();
            $giftCardKeys = $giftCardKeys->pluck('id')->toArray();   
        }
        $monthAndYear = $this->monthAndYear();
        $years = $monthAndYear[0];
        $month = $monthAndYear[1];
        $segment = " Sales Old Users Analytics";
        return view('admin.oldUsersSalesAnalytics.index',compact([
            'show_data','segment','noOfCalledUsers','averageTimeBetweenCalls','respondedTranxVolume','respondedTranxNo','noOfRespondedUsers'
            ,'totalCallDuration','averageCallDuration','type','table_data','callPercentageEffectiveness','unique','total','salesOldUsers','years','month',
            'respondedTranxVolume','giftCardKeys','quarterlyInactiveUsersNo','targetCovered'
        ]));
    }

    public function sortingAnalytics(Request $request, $type = null)
    {
         if(!empty($request->all())){
            $request->session()->put('SortingKeys', $request->all());
         }

         $request_data = $request->session()->get('SortingKeys');
         if(!empty($request_data))
         {
            //*by the sorting type depends the start and end date
            if((is_string($this->sortingType($request_data))) == true)
            {
                return redirect()->back()->with(['error' => $this->sortingType($request_data)]);
            }
            else{
                $time_data = $this->sortingType($request_data);
                $start_date = $time_data[0];
                $end_date = $time_data[1];
                $request->session()->put('startKey', $start_date);
                $request->session()->put('endKey', $end_date);
            }
            $conversionType = "unique";
            if(isset($request_data['unique']) AND $request_data['unique'] =="on"){
                $conversionType = "unique";
            }
            if(isset($request_data['total']) AND $request_data['total'] =="on"){
                $conversionType = "total";
            }
            $type = ($type != null) ? $type : "calledUsers";
            return $this->SortingLoadData($start_date,$end_date,$conversionType,$type,$request_data['sales']);
         }
         return redirect()->route('sales.oldUsers.salesAnalytics');
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
            $start_date = Carbon::parse($start_date." 00:00:00");
        }
        
        $segment .= $start_date->format('d M Y');
        if($end_date == null){
            $end_date = now()->format('Y-m-d');
            $end_date = Carbon::parse($end_date." 23:59:59");
        }
        
        $segment .= " to ".$end_date->format('d M Y');

        if(isset($request_data['unique']) AND $request_data['unique'] =="on"){
            $conversionType = "unique";
        }
        if(isset($request_data['total']) AND $request_data['total'] =="on"){
            $conversionType = "total";
        }

        $CalledUsers = UserTracking::with('transactions','user')->where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse','DeadUser'])->get();
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = UserTracking::with('transactions','user')->where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','Responded')->get();
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
        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($noOfRespondedUsers/$noOfCalledUsers)*100;

        $RespondedUsersTotal = UserTracking::with('transactions','user')->where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','Responded')->get();
        $totalRespondedUserTotal = $this->RespondedTotal($RespondedUsersTotal);
        $respondedTranxVolume = $totalRespondedUserTotal->sum('amount');

        $targetCovered = ($sales_id != null) ? $this->targetCovered($respondedTranxVolume,$sales_id) : $this->targetCovered($respondedTranxVolume,null);

        $respondedTranxNo = $respondedTransactions->count();

        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,$sales_id);
        $totalCallDuration = $CalledUsers->sum('call_duration');
        $averageCallDuration = ($noOfCalledUsers == 0) ? 0 : ($totalCallDuration/$noOfCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();

        $quarterlyInactiveUsersNo = UserTracking::where('Current_Cycle','QuarterlyInactive')
        ->where('current_cycle_count_date','>=',$start_date)->where('current_cycle_count_date','<=',$end_date)->count();
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
        if($type == 'TradesRespondedUsers')
        {
            $table_data = $respondedTransactions;
            $show_data = true;
        }
        $table_data = $table_data->sortByDesc('updated_at')->paginate(1000);

        if(in_array($type,['calledUsers','respondedUsers'])){
            foreach($table_data as $td)
            {
                $allTranx = collect()->concat($td['transactions']);
                $data = $allTranx->where('created_at','>=',$td->called_date)->sortByDesc('created_at')->first();

                if($data)
                {
                    $td->lastTranxDate = $data->created_at;
                    $td->lastTranxVolume = $data->amount;
                }
                
            }
        }

        $giftCardKeys = null;
        if(in_array($type,['TradesRespondedUsers']))
        {
            $giftCardKeys = Card::where('is_crypto',0)->get();
            $giftCardKeys = $giftCardKeys->pluck('id')->toArray();   
        }

        $monthAndYear = $this->monthAndYear();
        $years = $monthAndYear[0];
        $month = $monthAndYear[1];
        return view('admin.oldUsersSalesAnalytics.sort',compact([
            'show_data','segment','noOfCalledUsers','averageTimeBetweenCalls','respondedTranxVolume','respondedTranxNo','noOfRespondedUsers'
            ,'totalCallDuration','averageCallDuration','type','table_data','callPercentageEffectiveness','unique','total','salesOldUsers','years','month',
            'respondedTranxVolume','giftCardKeys','quarterlyInactiveUsersNo','targetCovered'
        ]));
    }

    public function showAllData(Request $request, $type = null)
    {
        //TODO working on the start date and en date of the sort
        $request_data = $request->session()->get('SortingKeys');
        $start_date = $request->session()->get('startKey');
        $end_date = $request->session()->get('endKey');

        //*start date and end date session
        $start_date = (isset($start_date)) ? $start_date : null;
        $end_date = (isset($end_date)) ? $end_date : null;
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
            $start_date = Carbon::parse($start_date." 00:00:00");
        }
        $start_date = Carbon::parse($start_date);
        
        $segment .= $start_date->format('d M Y');
        if($end_date == null){
            $end_date = now()->format('Y-m-d');
            $end_date = Carbon::parse($end_date." 23:59:59");
        }
        $end_date = Carbon::parse($end_date);
        
        $segment .= " to ".$end_date->format('d M Y');

        $CalledUsers = UserTracking::with('transactions','user')->where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse','DeadUser'])->get();
        $noOfCalledUsers = $CalledUsers->count();

        $RespondedUsers = UserTracking::with('transactions','user')->where('called_date','>=',$start_date)->where('called_date','<=',$end_date)->where('Current_Cycle','Responded')->get();
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
        $callPercentageEffectiveness = ($noOfCalledUsers == 0) ? 0 : ($noOfRespondedUsers/$noOfCalledUsers)*100;

        $totalRespondedUserTotal = $this->RespondedTotal($RespondedUsers);
        $respondedTranxVolume = $totalRespondedUserTotal->sum('amount');

        $respondedTranxNo = $respondedTransactions->count();
        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,$sales_id);

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
        if($type == 'TradesRespondedUsers')
        {
            $table_data = $respondedTransactions;
            $show_data = true;
        }
        $table_data = $table_data->sortByDesc('updated_at')->paginate(1000);

        if(in_array($type,['calledUsers','respondedUsers'])){
            foreach($table_data as $td)
            {
                $allTranx = collect()->concat($td['transactions']);
                $data = $allTranx->where('created_at','>=',$td->called_date)->sortByDesc('created_at')->first();

                if($data)
                {
                    $td->lastTranxDate = $data->created_at;
                    $td->lastTranxVolume = $data->amount;
                }
                
            }
        }

        $giftCardKeys = null;
        if(in_array($type,['TradesRespondedUsers']))
        {
            $giftCardKeys = Card::where('is_crypto',0)->get();
            $giftCardKeys = $giftCardKeys->pluck('id')->toArray();   
        }
        return view('admin.oldUsersSalesAnalytics.show',compact([
            'show_data','segment','type','table_data','giftCardKeys'
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
        //?do a performance check on this (to many foreach statements check execution time

        $uniqueData = [];
        foreach($data as $d)
        {
            $allTranx = collect()->concat($d['transactions']);

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
        foreach($data as $d)
        {
            $allTranx = collect()->concat($d['transactions'])->where('created_at','>=',$d->called_date);
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
            $targetData = $targetData->where('user_id',$id)->first()->target;
            return (($amount/$targetData)*100);
        endif;

        $targetData = $targetData->sum('target');
        return (($amount/$targetData)*100);
    }
}
