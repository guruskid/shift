<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewUsersTracking;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Validator;

class SalesNewUsersController extends Controller
{
    public function loadNewUsers($category = null)
    {
        
        $salesNewUsers = User::where('role',556)->get(['id','first_name','last_name']);;

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
        
        $averageTimeBetweenCalls = $this->averageTimeBetweenCalls($calledUsers);

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
        }
        if($category == 'goodLeadNo'){
            $table_data = $goodLeads;
            $this->CallDuration($table_data);
        }
        if($category == 'badLeadNo')
        {
            $table_data = $badLeads;
            $this->CallDuration($table_data);
        }
        if($category == 'goodLeadConversion' ){
            $table_data = $goodLeadTransactions;
        }
        if($category == 'badLeadConversion'){
            $table_data = $badLeadTransactions;
        }
        
        $table_data = $table_data->paginate(20);
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

    public function sortNewUsers(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'sales_id' => 'required|integer',
            'conversionType' => 'required',
            'category' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $salesNewUsers = User::where('role',556)->get(['id','first_name','last_name']);;
        $salesUser = User::find($request->sales_id); 

        if(!$salesUser){
            return response()->json([
                'success' => false,
                'message' => 'error finding sales personnel'
            ], 401);
        }

        if($request->start_date == null){
            $request->start_date = now()->format('Y-m-d');
        }
        $start_date = Carbon::parse($request->start_date." 00:00:00");

        if($request->end_date == null){
            $request->end_date = now()->format('Y-m-d');
        }
        $end_date = Carbon::parse($request->end_date." 23:59:59");

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

        $averageTimeBetweenCalls = $this->averageTimeBetweenCalls($calledUsers);

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
        }
        if($request->category == 'goodLeadNo'){
            $table_data = $goodLeads;
            $this->CallDuration($table_data);
        }
        if($request->category == 'badLeadNo')
        {
            $table_data = $badLeads;
            $this->CallDuration($table_data);
        }
        if($request->category == 'goodLeadConversion' ){
            $table_data = $goodLeadTransactions;
        }
        if($request->category == 'badLeadConversion'){
            $table_data = $badLeadTransactions;
        }
        
        $table_data = $table_data->paginate(20);

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
