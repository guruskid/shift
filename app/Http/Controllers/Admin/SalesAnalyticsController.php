<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewUsersTracking;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsController extends Controller
{
    public function index($type = null, Request $request)
    {
        $request->session()->forget('SortingKeys');
        $conversionType = "unique";
        $start_date =  null;
        $last_date = null;
        
        $type = ($type != null) ? $type : "calledUsers";

        return $this->loadData($start_date,$last_date,$conversionType,$type,null);
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

    public function loadData($start_date ,$end_date,$conversionType,$type,$sales_id)
    {
        $salesNewUsers = User::where('role',556)->get();
        if($start_date == null){
            $start_date = now()->format('Y-m-d');
        }
        $start_date = Carbon::parse($start_date." 00:00:00");
        if($end_date == null){
            $end_date = now()->format('Y-m-d');
        }
        $end_date = Carbon::parse($end_date." 23:59:59");
    
        $goodLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->where('status','goodlead')->get();

        $noGoodLeads = $goodLeads->count();
        $badLeads = NewUsersTracking::where('updated_at','>=',$start_date)->where('updated_at','<=',$end_date)
        ->Where('status','badlead')->get();
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
        
        $averageTimeBetweenCalls = $this->averageTimeBetweenCalls($calledUsers);

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

        $segment = " Sales New Users Analytics";
        return view('admin.sales_analytics.index',compact([
            'show_data','segment','noCalledUsers','noGoodLeads','goodLeadConversionRate',
            'noBadLeads','averageTimeBetweenCalls','badLeadConversionRate','totalConversionRate',
            'totalConversionVolume','totalCallDuration','averageCallDuration','type','table_data',
            'unique','total','salesNewUsers'
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
        
        $averageTimeBetweenCalls = $this->averageTimeBetweenCalls($calledUsers);

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
        $salesNewUsers = User::where('role',556)->get();
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
        
        $averageTimeBetweenCalls = $this->averageTimeBetweenCalls($calledUsers);

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
            $user_transactionsUnique = Transaction::where('user_id',$d->user_id)->first();
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
        $timeDiff = ($calledUsers->count() == 0) ? 0 : $timeDiff/($calledUsers->count());
        if($calledUsers->count() == 0){
            return 0;
        }else{
            return CarbonInterval::seconds($timeDiff)->cascade()->forHumans();
        }
    }
}
