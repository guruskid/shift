<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewUsersTracking;
use App\SalesTimestamp;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsController extends Controller
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
            $end_date = Carbon::createFromDate($sortingType['Year'],$sortingType['month'],1);
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
        $type = ($type != null) ? $type : "calledUsers";
        return $this->loadData($type);
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
         return redirect()->route('sales.newUsers.salesAnalytics');
    }

    public function loadData($type)
    {
        $salesNewUsers = User::where('role',556)->get();
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
        $unique = 1;
        $total = 0;
        
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
        $show_data = false;
        
        $table_data = $calledUsers;

        if($type == 'calledUsers'){
            $table_data = $calledUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'GoodLead'){
            $table_data = $goodLeads;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'BadLead')
        {
            $table_data = $badLeads;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'GLConversion' ){
            $table_data = $goodLeadTransactions;
            $show_data = true;
        }
        if($type == 'BLConversion'){
            $table_data = $badLeadTransactions;
            $show_data = true;
        }
        $table_data = $table_data->take(10);

        $segment = " Sales New Users Analytics";
        $monthAndYear = $this->monthAndYear();
        $years = $monthAndYear[0];
        $month = $monthAndYear[1];
        return view('admin.sales_analytics.index',compact([
            'show_data','segment','noCalledUsers','noGoodLeads','goodLeadConversionRate',
            'noBadLeads','averageTimeBetweenCalls','badLeadConversionRate','totalConversionRate',
            'totalConversionVolume','totalCallDuration','averageCallDuration','type','table_data',
            'unique','total','salesNewUsers','month','years'
        ]));
    }

    public function SortingLoadData($start_date ,$end_date,$conversionType,$type,$sales_id)
    {
        $segment = null;
        $salesUser = User::find($sales_id); 
        if($salesUser){
            $segment .= $salesUser->first_name." ".$salesUser->last_name." ";
        }
        $salesNewUsers = User::where('role',556)->get();
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
    
        $goodLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->where('status','goodlead');
        if($sales_id != null){
            $goodLeads = $goodLeads->where('sales_id',$sales_id)->get();
        }else{
        $goodLeads = $goodLeads->get();
        }

        $noGoodLeads = $goodLeads->count();
        $badLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->Where('status','badlead');
        if($sales_id != null){
            $badLeads = $badLeads->where('sales_id',$sales_id)->get();
        }else{
        $badLeads = $badLeads->get();
        }
        $noBadLeads = $badLeads->count();

        $calledUsers = $this->allGoodAndBadLeads($goodLeads,$badLeads);
        $noCalledUsers = $calledUsers->count();

        if($conversionType == "unique")
        {
            $goodLeadData = $this->conversionRateUnique($goodLeads,$noCalledUsers);
            $badLeadData = $this->conversionRateUnique($badLeads,$noCalledUsers);
            $unique = 1;
            $total = 0;
        }
        else{
            $goodLeadData = $this->conversionRateTotal($goodLeads,$noCalledUsers);
            $badLeadData = $this->conversionRateTotal($badLeads,$noCalledUsers);
            $unique = 0;
            $total = 1;
        }
        
        $goodLeadTransactions = $goodLeadData[0]['transactions'];
        $goodLeadConversionRate = $goodLeadData[0]['ConversationRate'];
        $goodLeadConversionAmount = $goodLeadData[0]['ConversionAmount'];
        
        $badLeadTransactions = $badLeadData[0]['transactions'];
        $badLeadConversionRate = $badLeadData[0]['ConversationRate'];
        $badLeadConversionAmount = $badLeadData[0]['ConversionAmount'];
        
        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,$sales_id);

        $totalConversionGoodandBadLeads = $goodLeadTransactions->count() + $badLeadTransactions->count();
        $totalConversionRate = ($noCalledUsers == 0) ? 0 : ($totalConversionGoodandBadLeads/$noCalledUsers)*100;

        $totalConversionVolume = number_format($goodLeadConversionAmount + $badLeadConversionAmount);

        $totalCallDuration = $calledUsers->sum('call_duration');
        $averageCallDuration = ($noCalledUsers == 0) ? 0 : ($totalCallDuration/$noCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();
        $show_data = false;
        
        $type = $type;
        $table_data = $calledUsers;

        if($type == 'calledUsers'){
            $table_data = $calledUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'GoodLead'){
            $table_data = $goodLeads;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'BadLead')
        {
            $table_data = $badLeads;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'GLConversion' ){
            $table_data = $goodLeadTransactions;
            $show_data = true;
        }
        if($type == 'BLConversion'){
            $table_data = $badLeadTransactions;
            $show_data = true;
        }
        $table_data = $table_data->take(10);

        $segment .= " Sales New Users Analytics";
        
        return view("admin.sales_analytics.sortAnalytics",compact([
            'show_data','segment','noCalledUsers','noGoodLeads','goodLeadConversionRate',
            'noBadLeads','averageTimeBetweenCalls','badLeadConversionRate','totalConversionRate',
            'totalConversionVolume','totalCallDuration','averageCallDuration','type','table_data',
            'unique','total','salesNewUsers'
        ]));
    }

    public function viewAllTransaction(Request $request, $type = null)
    {
        $request_data = $request->session()->get('SortingKeys');
        $start_date = $request->session()->get('startKey');
        $end_date = $request->session()->get('endKey');

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
        $salesOldUsers = User::where('role',556)->get();
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

        $goodLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->where('status','goodlead');
        if($sales_id != null){
            $goodLeads = $goodLeads->where('sales_id',$sales_id)->get();
        }else{
        $goodLeads = $goodLeads->get();
        }

        $noGoodLeads = $goodLeads->count();
        $badLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->Where('status','badlead');
        if($sales_id != null){
            $badLeads = $badLeads->where('sales_id',$sales_id)->get();
        }else{
        $badLeads = $badLeads->get();
        }
        $noBadLeads = $badLeads->count();

        $calledUsers = $this->allGoodAndBadLeads($goodLeads,$badLeads);
        $noCalledUsers = $calledUsers->count();

        if($conversionType == "unique")
        {
            $goodLeadData = $this->conversionRateUnique($goodLeads,$noCalledUsers);
            $badLeadData = $this->conversionRateUnique($badLeads,$noCalledUsers);
            $unique = 1;
            $total = 0;
        }
        else{
            $goodLeadData = $this->conversionRateTotal($goodLeads,$noCalledUsers);
            $badLeadData = $this->conversionRateTotal($badLeads,$noCalledUsers);
            $unique = 0;
            $total = 1;
        }
        $goodLeadTransactions = $goodLeadData[0]['transactions'];
        $goodLeadConversionRate = $goodLeadData[0]['ConversationRate'];
        $goodLeadConversionAmount = $goodLeadData[0]['ConversionAmount'];
        
        $badLeadTransactions = $badLeadData[0]['transactions'];
        $badLeadConversionRate = $badLeadData[0]['ConversationRate'];
        $badLeadConversionAmount = $badLeadData[0]['ConversionAmount'];
        
        $averageTimeBetweenCalls = $this->sortAverageTimeBetweenCalls($start_date,$end_date,$sales_id);

        $totalConversionGoodandBadLeads = $goodLeadTransactions->count() + $badLeadTransactions->count();
        $totalConversionRate = ($noCalledUsers == 0) ? 0 : ($totalConversionGoodandBadLeads/$noCalledUsers)*100;

        $totalConversionVolume = number_format($goodLeadConversionAmount + $badLeadConversionAmount);

        $totalCallDuration = $calledUsers->sum('call_duration');
        $averageCallDuration = ($noCalledUsers == 0) ? 0 : ($totalCallDuration/$noCalledUsers);

        $totalCallDuration = ($totalCallDuration == 0) ? 0 : CarbonInterval::seconds($totalCallDuration)->cascade()->forHumans();
        $averageCallDuration = ($averageCallDuration == 0) ? 0 : CarbonInterval::seconds($averageCallDuration)->cascade()->forHumans();
        $show_data = false;
        
        $type = $type;
        $table_data = $calledUsers;

        if($type == 'calledUsers'){
            $table_data = $calledUsers;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'GoodLead'){
            $table_data = $goodLeads;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'BadLead')
        {
            $table_data = $badLeads;
            $this->CallDuration($table_data);
            $show_data = true;
        }
        if($type == 'GLConversion' ){
            $table_data = $goodLeadTransactions;
            $show_data = true;
        }
        if($type == 'BLConversion'){
            $table_data = $badLeadTransactions;
            $show_data = true;
        }
        $table_data = $table_data->paginate(1000);

        $segment .= " Sales New Users Analytics";

        return view('admin.sales_analytics.showAnalytics',compact([
            'show_data','segment','noCalledUsers','noGoodLeads','goodLeadConversionRate',
            'noBadLeads','averageTimeBetweenCalls','badLeadConversionRate','totalConversionRate',
            'totalConversionVolume','totalCallDuration','averageCallDuration','type','table_data',
            'unique','total','salesNewUsers'
        ]));
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
