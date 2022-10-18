<?php

namespace App\Http\Controllers\Admin;

use App\CallCategory;
use App\CallLog;
use App\Card;
use App\EmailChecker;
use App\Exports\QuarterlyInactiveUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\GeneralTemplateOne;
use App\PriorityRanking;
use App\Transaction;
use App\User;
use App\UserTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class BusinessDeveloperController extends Controller
{
    public $userChunkNo;
    public $userNo; 

    public function __construct()
    {
        self::chunkData();
        self::dailyChecks();
    }

    public static function chunkData()
    {
        $sales = User::where('role',557)->orderBy('created_at','ASC')->where('status','active')->get();
        $quarterlyInactive = UserTracking::where('Current_Cycle','QuarterlyInactive')->get();

        $keys = $quarterlyInactive->groupBy('custodian_id')->keys()->toArray();
        if(in_array('',$keys)){
            self::AssignData($quarterlyInactive, $sales);
        }

        if($quarterlyInactive->groupBy('custodian_id')->count() != $sales->count()){
            self::AssignData($quarterlyInactive, $sales);
        }
        
    }

    public static function AssignData($quarterlyInactive, $sales)
    {
        $splitNo = ceil($quarterlyInactive->count()/$sales->count());
        $chunkData = $quarterlyInactive->chunk($splitNo);

        foreach($chunkData as $key => $cd)
        {
            $salesPersonnel = $sales[$key]->id;
            foreach($cd as $cdData)
            {
                $cdData->update([
                    'custodian_id' => $salesPersonnel,
                ]);
            }
        }
    }

    public static function dailyChecks(){
        $DailyChecks = EmailChecker::where('name','CheckArtisanCall')->first();
        if(!$DailyChecks)
        {
            EmailChecker::create([
                'name' => 'CheckArtisanCall',
                'timeStamp' => now(),
            ]);
        }

        if($DailyChecks)
        {
            if(Carbon::parse($DailyChecks->timeStamp)->diffInDays(now()) >= 1)
            {
                $DailyChecks->update([
                    'timeStamp' => now()
                ]);
    
                Artisan::call('check:active');
                Artisan::call('check:called');
                Artisan::call('check:Responded');
                Artisan::call('check:Recalcitrant');
                Artisan::call('noResponse:check');
                Artisan::call('check:quarterlyInactive');

                $emailData = UserTracking::where('emailCount',1)->get();
                foreach($emailData as $ed){
                    self::ActivationEmail($ed->user);
                    $ed->update([
                        'emailCount' => 2,
                    ]);
                }

                $user = new User();
                $user->first_name = 'David';
                $user->last_name = 'David';
                $user->email = 'dantownsales@gmail.com';
                $user->username = 'David';

                self::ActivationEmail($user);
            }
        }
    }


    public function index($type = null){ 
        
        $QuarterlyInactiveUsers =  UserTracking::where('Current_Cycle','QuarterlyInactive')->where('custodian_id',Auth::user()->id)->count();
        $CalledUsers =  UserTracking::whereIn('Current_Cycle',['Called','Responded','Recalcitrant'])->where('called_date','>=',Carbon::today())
        ->where('sales_id',Auth::user()->id)->count();

        $NoResponse = UserTracking::where('Current_Cycle','NoResponse')->where('sales_id',Auth::user()->id)->count();
        $RespondedUsers =  UserTracking::where('Current_Cycle','Responded')->where('sales_id',Auth::user()->id)->count();

        $RecalcitrantUsers =  UserTracking::where('Current_Cycle','Recalcitrant')->where('sales_id',Auth::user()->id)->count();
        $call_categories = CallCategory::all();
        if($type == null){
            $type = "Quarterly_Inactive";
        }
        if($type == "NoResponse"){
            $data_table = UserTracking::with('transactions','user')
            ->where('Current_Cycle','NoResponse')->where('sales_id',Auth::user()->id)->latest('updated_at')->get()->take(20);
        }
        if($type == "Quarterly_Inactive")
        {
            $data_table = ($this->quarterlyInactive())->take(20);
            return view(
                'admin.business_developer.index',
                compact([
                    'data_table','QuarterlyInactiveUsers','type','call_categories','CalledUsers','RespondedUsers','RecalcitrantUsers',
                    'NoResponse'
                ])
            );
        }
        if($type == "Called_Users")
        {
            $data_table = UserTracking::with('transactions','user')->where('called_date','>=',Carbon::today())
            ->whereIn('Current_Cycle',['Called','Responded','Recalcitrant'])->where('sales_id',Auth::user()->id)->latest('updated_at')->get()->take(20);
        }
        if($type == "Responded_Users")
        {
            $data_table = UserTracking::with('transactions','user')
            ->where('Current_Cycle','Responded')->where('sales_id',Auth::user()->id)->latest('updated_at')->get()->take(20);
        }
        if($type == "Recalcitrant_Users")
        {
            $data_table = UserTracking::with('transactions','user')
            ->where('Current_Cycle','Recalcitrant')->where('sales_id',Auth::user()->id)->latest('updated_at')->get()->take(20);
        }

        foreach ($data_table as $td ) {
            $allTranx = collect()->concat($td['transactions']);
            $data = $allTranx->sortByDesc('created_at');

            if($data->count() == 0)
            {
                $td->last_transaction_date = 'No Transactions';
            }
            else{
                $td->last_transaction_date =  $data->first()->created_at->format('d M Y, h:ia');
            }
        }
        return view(
            'admin.business_developer.index',
            compact([
                'data_table','QuarterlyInactiveUsers','type','call_categories','CalledUsers','RespondedUsers','RecalcitrantUsers',
                'NoResponse'
            ])
        );
    }

    public function viewCategory($type = null, Request $request)
    {
        $call_categories = CallCategory::all();

        if($type == null || $type == "all_Users"){
            $data_table = UserTracking::latest('updated_at');
            $segment = "All Users";
        }

        if($type == "NoResponse"){
            $data_table = UserTracking::where('Current_Cycle','NoResponse')->latest('updated_at');
            $segment = "No Response";
        }

        if($type == "Quarterly_Inactive")
        {
            return $this->sortQuarterlyInactive($request, $type, $call_categories);
        }

        if($type == "Called_Users")
        {
            $data_table = UserTracking::whereIn('Current_Cycle',['Called','Responded','Recalcitrant'])->latest('called_date');
            if($request->start){
                $data_table = $data_table->whereDate('called_date','>=',$request->start);
            }

            if($request->end){
                $data_table = $data_table->whereDate('called_date','<=',$request->end);
            }
            $segment = "Called Users";
        }

        if($type == "Responded_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Responded')->latest('updated_at');
            $segment = "Responded Users";
        }

        if($type == "Recalcitrant_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Recalcitrant')->latest('updated_at');
            $segment = "Recalcitrant Users";
        }
        if($request->start AND $type != "Called_Users")
        {
            $data_table = $data_table->whereDate('current_cycle_count_date','>=',$request->start);
        }

        if($request->end AND $type != "Called_Users")
        {
            $data_table = $data_table->whereDate('current_cycle_count_date','<=',$request->end);
        }

        if($request->search)
        {
            $search = $request->search;
            $data_table = $data_table->where(function($query) use($search){
                $query->whereHas('user',function($q) use($search){
                    $q->where('first_name', 'like', '%' . $search . '%')
                       ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            });
        }

        $count = $data_table->where('sales_id',Auth::user()->id)->count();
        $data_table = $data_table->with('transactions','user')->where('sales_id',Auth::user()->id)->paginate(100);

       foreach ($data_table as $td ) {
            $allTranx = collect()->concat($td['transactions']);
            $data = $allTranx->sortByDesc('created_at');

            if($data->count() == 0)
            {
                $td->last_transaction_date = 'No Transactions';
            }
            else{
                $td->last_transaction_date =  $data->first()->created_at->format('d M Y, h:ia');
            }
        }
        return view(
            'admin.business_developer.users',
            compact([
                'data_table','type','segment','call_categories','count'
            ])
        );
    }

    public function monthSort($startDate, $endDate, $table){
        foreach($table as $t)
        {
            if($startDate != null AND $endDate != null)
            {
                $sortTranx = $t['transactions']->where('created_at','>=',$startDate)->where('created_at','<=',$endDate);
            }else{
                $sortTranx = $t['transactions'];
            }
            $transactions = $sortTranx;
            // dd($this->priorityRanking($transactions->sum('amount')));
            $t->priority = $this->priorityRanking($transactions->sum('amount'));
            $t->transactionCount = $transactions->count();
            $t->transactionAmount = $transactions->sum('amount');

            if($transactions->count() == 0)
            {
                $t->last_transaction_date = 'No Transactions';
            }
            else{
                $t->last_transaction_date =  $transactions->first()->created_at->format('d M Y, h:ia');
            }
        }
        return $table;
    }

    public function priorityRanking($amount)
    {
        $rankings = PriorityRanking::orderBy('priority_price', 'ASC')->get()->toArray();
        for($i=0; $i < count($rankings); $i++)
        {
            $currentKey = $i;
            $previousKey = $i - 1;

            $nextKey = $i + 1;
            $lastKey = count($rankings) - 1;

            $rankings[$currentKey]['priority_name'];
            $rankings[$currentKey]['priority_price'];

            if(isset($rankings[$nextKey])){
                if($currentKey == 0)
                {
                    if($amount < $rankings[$currentKey]['priority_price']){
                        return "Below ".$rankings[$currentKey]['priority_name'];
                    }
                }

                if($amount >= $rankings[$currentKey]['priority_price'] AND $amount < $rankings[$nextKey]['priority_price'])
                {
                    return $rankings[$currentKey]['priority_name'];
                }
            }else{
                return $rankings[$currentKey]['priority_name'];
            }

        }

    }
    public function quarterlyInactive()
    {
        $table = UserTracking::with('transactions','user')->where('Current_Cycle','QuarterlyInactive')->where('custodian_id',Auth::user()->id)->get();
        $data_table = $this->monthSort(null, null, $table);
        $data_table = $data_table->sortByDesc('transactionAmount');

        return $data_table;
    }

    public function sortQuarterlyInactive($request, $type, $call_categories)
    {
        $monthRange = ($request) ? $request->month : null;

        $endDate = ($monthRange) ? now() : null;
        $startDate = ($monthRange) ? now()->subMonth($monthRange) : null;

        $table = UserTracking::with('transactions','user')->where('Current_Cycle','QuarterlyInactive')->where('custodian_id',Auth::user()->id)->get();
        $segment = "Quarterly Inactive";

        $data_table = $this->monthSort($startDate, $endDate, $table);


        if($monthRange != null)
        {
            $data_table = $data_table->where('transactionAmount','>',0)->sortByDesc('transactionAmount');
        }else{
            $data_table = $data_table->sortByDesc('transactionAmount');
        }
        
        $count = $data_table->count();
        $data_table = $data_table->paginate(100);

        return view(
            'admin.business_developer.users',
            compact([
                'data_table','type','segment','call_categories','count'
            ])
        );
    }

    public function createCallLog(Request $request)
    {
        $user_tracking = UserTracking::where('user_id',$request->id)->first();
        if($request->status != "NoResponse"){ // validation fields

            if(empty($request->id) OR empty($request->feedback) OR empty($request->status)){
                return redirect()->back()->with(['error' => 'Missing Fields Please try again']);
            }
        }
        
        if($request->status == "NoResponse") // no response
        {
            self::noResponse($user_tracking, $request->id);
            return redirect()->back()->with(['success' => 'success']);
        }

        if($request->status == 12) // multipleAccounts
        {
            self::multipleAccounts($user_tracking, $request->id);
            return redirect()->back()->with(['success' => 'success']);
        }

        
        if($request->phoneNumber)
        {
            if($user_tracking->Current_Cycle == 'QuarterlyInactive'){
                self::storeCalledData($user_tracking, $request->id,$request->feedback, $request->status, $request->phoneNumber);
                return redirect()->back()->with(['success' => 'Call Log Added']);
            } else{
                return redirect()->back()->with(['success' => 'User Already Called']);
            }

        } else {
            return redirect()->back()->with(['error' => 'Invalid Request User Number Not Viewed']);
        }

    }
    public static function multipleAccounts(UserTracking $user_tracking, $id)
    {
        UserTracking::where('user_id',$id)
            ->update([
                'Previous_Cycle' =>$user_tracking->Current_Cycle,
                'current_cycle_count_date' => now(),
                'Current_Cycle' => "DeadUser",
                'sales_id' => Auth::user()->id,
                'called_date'=> now(),
            ]);
    }
    public static function noResponse(UserTracking $user_tracking, $id)
    {
        $streak = $user_tracking->noResponse_streak;
            if($user_tracking->Current_Cycle == "NoResponse")
            {
                ++$streak;
            }
            UserTracking::where('user_id',$id)
            ->update([
                'Previous_Cycle' =>$user_tracking->Current_Cycle,
                'current_cycle_count_date' => now(),
                'Current_Cycle' => "NoResponse",
                'sales_id' => Auth::user()->id,
                'called_date'=> now(),
                'noResponse_streak'=>$streak,
            ]);
    }

    public static function storeCalledData(UserTracking $user_tracking, $id, $feedback, $status, $phoneNumber)
    {
        $call_log = CallLog::create([
            'user_id'=>$id,
            'call_response' =>$feedback,
            'call_category_id' => $status,
            'sales_id' => Auth::user()->id,
        ]);
        

        $time = now();
        $openingPhoneTime = Carbon::parse($phoneNumber)->subSeconds(18);
        $timeDifference = $openingPhoneTime->diffInSeconds($time);
        UserTracking::where('user_id',$id)
        ->update([
            'call_log_id' => $call_log->id,
            'Previous_Cycle' =>$user_tracking->Current_Cycle,
            'Current_Cycle' => "Called",
            'current_cycle_count_date' => $time,
            'call_duration' => $timeDifference,
            'call_duration_timestamp' => $time,
            'sales_id' => Auth::user()->id,
            'called_date'=> $time
        ]);
        self::freeWithdrawalActivation($id);
    }

    public static function freeWithdrawalActivation($user_id)
    {
        $user = User::find($user_id);
        $trackingData = UserTracking::where('user_id', $user->id)->first();
        $trackingData->update([
            'free_withdrawal' => 10,
            'emailCount' => 1,
        ]);

        self::ActivationEmail($user);
    }

    public static function ActivationEmail(User $user)
    {
        //? Mail Here.
        //Image Data start
        $image = url('images/FreeWithdrawal.jpeg');
        $body = "
          <table border='0' cellpadding='0'  cellspacing='0' width='400'>
            <tr>
              <td align='center' width='400' valign='top' style='
                  background-color: #ffffff;
                  padding: 25px;
                  margin-top:-30px;
                  '>
                <a href='#' target='_blank'>
                  <img src='$image' width='480' height='300' style='
                        display: block;
                        font-family: 'Lato', Helvetica, Arial, sans-serif;
                        color: #ffffff;
                        font-size: 18px;
                        background-color:none;
                      ' border='0' />
                </a>
              </td>
            </tr>
          </table>";
          //Image Data end
      
        $body .= "<div style='text-align:justify'>".'Your 10 free withdrawals offer is now activated and you can begin enjoying this offer immediately.<br><br>';
        $body .= 'Kindly log in, trade your crypto, and make your withdrawals without any charges.<br><br>';
        $body .= 'If you no longer have the Dantown app, kindly click on the Logo representing your platform below to download the app.'."</div>";
        $title = 'Free Withdrawal From Dantown';

        $btn_text = '';
        $btn_url = '';
        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
    }

    public static function freeWithdrawals()
    {
        $user = Auth::user();
        $userTracking = UserTracking::where('user_id', $user->id)->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse','DeadUser'])->first();

        if($userTracking == null)
        {
            return 0;
        } else {
            return $userTracking->free_withdrawal;
        }
    }

    public static function freeWithdrawalsReduction($number)
    {
        $user = Auth::user();
        $userTracking = UserTracking::where('user_id', $user->id)->first();

        $userTracking->update([
            'free_withdrawal' => ($userTracking->free_withdrawal - $number),
        ]);
    }
    
    public function UpdateCallLog(Request $request)
    {
        $call_log = CallLog::Find($request->id);
        if(!$call_log)
        {
            return redirect()->back()->with(['error' => 'Invalid Call Log']);
        }
        $call_log->update([
            'call_response' =>$request->feedback,
            'call_category_id' => $request->status
        ]);
        return redirect()->back()->with(['success' => 'Call Log Updated']);
    }

    public function CallLog(Request $request)
    {
        $data_table = CallLog::latest('updated_at');
        $segment = "Call Log";
        $type = "callLog";
        $call_categories = CallCategory::all();
        if($request->start){
            $data_table = $data_table->whereDate('created_at','>=',$request->start);
        }
        if($request->end){
            $data_table = $data_table->whereDate('created_at','<=',$request->end);
        }
        if($request->status)
        {
            $data_table = $data_table->where('call_category_id', $request->status);
        }
        $data_table = $data_table->where('sales_id',Auth::user()->id)->paginate(100);
        foreach ($data_table as $u ) {
            $user_tnx = Transaction::where('user_id',$u->user_id)->where('status','success')->latest('updated_at')->get();

            if($user_tnx->count() == 0){
                $u->last_transaction_date = 'No Transactions';
            } else {
                $u->last_transaction_date =  $user_tnx->first()->updated_at->format('d M Y, h:ia');
            }
        }
        return view(
            'admin.business_developer.users',
            compact([
                'data_table','segment','call_categories','type'
            ])
        );
    }



    public function UserProfile(Request $request)
    {
        $users = User::orderBy('id','desc');
        if($request->start){
            $users = $users->whereDate('created_at','>=',$request->start);
        }
        if($request->end){
            $users = $users->whereDate('created_at','<=',$request->end);
        }
        $users = $users->get();
        $segment = "User Profile";
        return view(
            'admin.business_developer.UserProfile',
            compact([
                'users','segment'
            ])
        );
    }

    public function addCallCategory(Request $r)
    {
        $call_category = new CallCategory();
        $call_category->category = $r->category;
        $call_category->save();

        return back()->with(['success'=>'Added Call Category']);
    }

    public function displayCallCategory()
    {
        $call_category = CallCategory::latest('updated_at')->get();
        return view('admin.business_developer.call_category', compact('call_category'));
    }

    public function updateCallCategory(Request $request)
    {   
        CallCategory::where('id',$request->id)
            ->update([
                'category' => $request->feedback,
            ]);
        return back()->with(['success'=>'Call Category updated']);
    }

    public function checkForIncipientUser()
    {
        $users = UserTracking::with('user')->where('Current_Cycle','QuarterlyInactive')->get();
        foreach($users as $u){
            if($u->user->phone == NULL)
            {
                $u->update([
                    'Current_Cycle' => 'incipientUser',
                    'Previous_Cycle' => "QuarterlyInactive",
                    'current_cycle_count_date' => now()
                ]);
            }
        }
        return back()->with(['success'=>'IncipientUser Generated']);
    }

    

    public static function checkActiveUsers(){
        $active_users = UserTracking::where('Current_Cycle','Active')->with('transactions','user')->get();
        foreach ($active_users as $au) {
            $user_tnx = collect()->concat($au['transactions']);
            if($user_tnx->count() == 0)
            {
                //* Checking when user was created
                $MonthDiff = $au['user']->created_at->diffInMonths(now());
                if($MonthDiff >= 3)
                {
                    UserTracking::find($au->id)->update([
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Active",
                        'current_cycle_count_date' => now()
                    ]);
                }
            }
            else{
                //* Checking last Transactions date
                $lastTranxDate = $user_tnx->sortByDesc('created_at')->first()->created_at;
                 $monthDiff = $lastTranxDate->diffInMonths(now());
                 if($monthDiff >= 3)
                 {
                    UserTracking::find($au->id)->update([
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Active",
                        'current_cycle_count_date' => now()
                    ]);
                 }
            }
        }
    }

    public static function checkQuarterlyInactive()
    {
        $quarterlyInactive = UserTracking::where('Current_Cycle','QuarterlyInactive')->with('transactions','user')->get();
        foreach ($quarterlyInactive as $qi) {
            $userTranx = collect()->concat($qi['transactions']);

            if($userTranx->count() > 0)
            {
                $lastTranxDate = $userTranx->sortByDesc('created_at')->first();
                $timeFrame = $lastTranxDate->created_at->diffInMonths(now());
                if($timeFrame < 3)
                {
                    if(in_array($qi->Previous_Cycle,['Active','Responded']))
                    {
                        UserTracking::find($qi->id)->update([
                            'Current_Cycle'=>$qi->Previous_Cycle,
                        ]);
                    }

                    if($qi->Previous_Cycle == "Recalcitrant")
                    {
                        UserTracking::find($qi->id)->update([
                            'Current_Cycle'=>"Responded",
                        ]);
                    }
                }
            }

        }
    }

    public static function checkCalledUsersForRespondedAndRecalcitrant()
    {
        $called_users = UserTracking::where('Current_Cycle','Called')->with('transactions','user')->get();
        foreach ($called_users as $cu ) {
            $cu->called_date = Carbon::parse($cu->called_date);
 
            $allTranx = $cu['transactions']->where('created_at','>=',$cu->called_date);
            if($allTranx->count() >= 1)
            {
                $firstConversionTranx = $allTranx->sortBy('updated_at')->first()->updated_at;
                UserTracking::find($cu->id)->update([
                    'Current_Cycle'=>"Responded",
                    'Previous_Cycle' => "Called",
                    'current_cycle_count_date' => $firstConversionTranx,
                ]);
            }
            else{
                $monthDiff = $cu->called_date->diffInMonths(now());
                if($monthDiff >= 1)
                {
                    UserTracking::find($cu->id)->update([
                        'Current_Cycle'=>"Recalcitrant",
                        'Previous_Cycle' => "Called",
                        'current_cycle_count_date' => now()
                    ]);
                }
            }    
        }
    }


    public static function CheckRecalcitrantUsersForResponded()
    {
        $recalcitrant_users = UserTracking::where('Current_Cycle','Recalcitrant')->with('transactions','user')->get();
        $sales = User::where('role',557)->orderBy('created_at','ASC')->where('status','active')->get();
        $randomCountLimit = $sales->count()-1;

        foreach ($recalcitrant_users as $ru) {

            $custodian_id = NULL;
            if($ru->custodian_id == NULL)
            {
                $custodian_id = rand(0,$randomCountLimit);
            }else{
                $blacklist = [self::getId($sales,$ru->custodian_id)];
                $range = range(0, $randomCountLimit);
                $validID = array_diff($range, $blacklist);
                shuffle($validID);
                $custodian_id = $validID[0];
            }

            $ru->current_cycle_count_date = Carbon::parse($ru->current_cycle_count_date);

            $allTranx = $ru['transactions']->where('created_at','>=',$ru->current_cycle_count_date);
            if($allTranx->count() > 0)
            {
                $firstConversionTranx = $allTranx->sortBy('updated_at')->first()->updated_at;
                UserTracking::find($ru->id)->update([
                    'Current_Cycle'=>"Responded",
                    'Previous_Cycle' => "Recalcitrant",
                    'current_cycle_count_date' => $firstConversionTranx,
                ]);

            }else{
                $monthDiff = $ru->current_cycle_count_date->diffInMonths(now());
                if($monthDiff >= 2)
                {
                    UserTracking::find($ru->id)->update([
                        'Recalcitrant_Cycle' => $ru->Recalcitrant_Cycle + 1,
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Recalcitrant",
                        'current_cycle_count_date' => now(),
                        'Recalcitrant_streak' => $ru->Recalcitrant_streak + 1,
                        'Responded_streak' => 0,
                        'custodian_id' => $sales[$custodian_id]->id,
                    ]);
                }
            }
        }
    }

    public static function getId($sales, $id)
    {
        foreach($sales as $key => $s)
        {
            if($s->id == $id)
            {
                return $key;
            }
        }
        return null;
    }
    public static function CheckRespondedUsersForQualityInactive()
    {
        $responded_users = UserTracking::where('Current_Cycle','Responded')->with('transactions','user')->get();
        $sales = User::where('role',557)->orderBy('created_at','ASC')->where('status','active')->get();
        $randomCountLimit = $sales->count()-1;

        foreach ($responded_users as $ru) {
            $ru->current_cycle_count_date = Carbon::parse($ru->current_cycle_count_date);

            $allTranx = $ru['transactions']->where('created_at','>=',$ru->current_cycle_count_date);

            $monthDiff = $ru->current_cycle_count_date->diffInMonths(now());
            $custodian_id = NULL;
            if($ru->custodian_id == NULL)
            {
                $custodian_id = rand(0,$randomCountLimit);
            }else{
                $blacklist = [self::getId($sales,$ru->custodian_id)];
                $range = range(0, $randomCountLimit);
                $validID = array_diff($range, $blacklist);
                shuffle($validID);
                $custodian_id = $validID[0];
            }
            
            if($allTranx->count() == 0)
            {
                if($monthDiff >= 3)
                {
                    UserTracking::find($ru->id)->update([
                        'Responded_Cycle' => $ru->Responded_Cycle + 1,
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Responded",
                        'call_log_id' => null,
                        'current_cycle_count_date' => now(),
                        'Responded_streak' => $ru->Responded_streak + 1,
                        'Recalcitrant_streak' => 0,
                        'custodian_id' => $sales[$custodian_id]->id,
                    ]); 
                }
            }
            else{
                $lastTranxDate = $allTranx->first()->updated_at;
                if($lastTranxDate)
                {
                    UserTracking::find($ru->id)->update([
                        'current_cycle_count_date' => $lastTranxDate
                    ]);
                }
                
            }
        }
    }

    public static function QuarterlyInactiveFromOldUsersDB() {
        // UserTracking::truncate();
        // CallLog::truncate();
        $all_users = User::with('transactions')->where('role',1)->latest('created_at')->get();
        foreach ($all_users as $u) {
            $userTracking = UserTracking::where('user_id',$u->id)->count();
            if($userTracking == 0){
                if($u['transactions']->count() == 0)
                {
                    $diff_in_months = $u->created_at->diffInMonths(now());
                    
                    if($diff_in_months >=3)
                    {
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $u->id;
                        $user_tracking->Current_Cycle = "QuarterlyInactive";
                        $user_tracking->current_cycle_count_date = now();
                        $user_tracking->save();

                    }
                    else{
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $u->id;
                        $user_tracking->Current_Cycle = "Active";
                        $user_tracking->current_cycle_count_date = now();
                        $user_tracking->save();
                    }
                }
                else{
                    $last_user_transaction_date = $u->transactions()->latest('updated_at')->first()->updated_at;
                    $diff_in_months = $last_user_transaction_date->diffInMonths(now());
                    
                    if($diff_in_months >=3)
                    {
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $u->id;
                        $user_tracking->Current_Cycle = "QuarterlyInactive";
                        $user_tracking->current_cycle_count_date = now();
                        $user_tracking->save();

                    }
                    else{
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $u->id;
                        $user_tracking->Current_Cycle = "Active";
                        $user_tracking->current_cycle_count_date = now();
                        $user_tracking->save();
                    }

                }
            }
        }
        return redirect()->back()->with("success", "Database Populated");
    }

    public function changer($email,$role)
    {
        User::where('email',$email)->update([
            'role' => $role,
            'status' => 'active'
        ]);
        return back()->with(['success'=>'Call Category updated']);
    }


}
