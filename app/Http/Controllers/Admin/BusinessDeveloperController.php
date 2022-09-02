<?php

namespace App\Http\Controllers\Admin;

use App\AccountantTimeStamp;
use App\CallCategory;
use App\CallLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\NewUsersTracking;
use App\Transaction;
use App\User;
use App\UserTracking;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BusinessDeveloperController extends Controller
{
    public static function oldUsersArtisanCalls()
    {
        // Artisan::call('check:active');
        // Artisan::call('check:called');
        // Artisan::call('check:Responded');
        // Artisan::call('check:Recalcitrant');
        // Artisan::call('noResponse:check');
    }

    public function index($type = null){ 
        // BusinessDeveloperController::oldUsersArtisanCalls();

        $QuarterlyInactiveUsers =  UserTracking::where('Current_Cycle','QuarterlyInactive')->count();
        $CalledUsers =  UserTracking::where('Current_Cycle','Called')->count();
        $NoResponse = UserTracking::where('Current_Cycle','NoResponse')->count();
        $RespondedUsers =  UserTracking::where('Current_Cycle','Responded')->count();
        $RecalcitrantUsers =  UserTracking::where('Current_Cycle','Recalcitrant')->count();
        $call_categories = CallCategory::all();
        if($type == null){
            $type = "Quarterly_Inactive";
        }
        if($type == "NoResponse"){
            $data_table = UserTracking::with('transactions','utilityTransaction','depositTransactions','user')
            ->where('Current_Cycle','NoResponse')->latest('updated_at')->get()->take(20);
        }
        if($type == "Quarterly_Inactive")
        {
            $data_table = UserTracking::with('transactions','utilityTransaction','depositTransactions','user')
            ->where('Current_Cycle','QuarterlyInactive')->latest('updated_at')->get()->take(20);
        }
        if($type == "Called_Users")
        {
            $data_table = UserTracking::with('transactions','utilityTransaction','depositTransactions','user')
            ->where('Current_Cycle','Called')->latest('updated_at')->get()->take(20);
        }
        if($type == "Responded_Users")
        {
            $data_table = UserTracking::with('transactions','utilityTransaction','depositTransactions','user')
            ->where('Current_Cycle','Responded')->latest('updated_at')->get()->take(20);
        }
        if($type == "Recalcitrant_Users")
        {
            $data_table = UserTracking::with('transactions','utilityTransaction','depositTransactions','user')
            ->where('Current_Cycle','Recalcitrant')->latest('updated_at')->get()->take(20);
        }

        foreach ($data_table as $td ) {
            $allTranx = collect()->concat($td['transactions'])->concat($td['depositTransactions'])->concat($td['utilityTransaction']);
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
            $data_table = UserTracking::where('Current_Cycle','QuarterlyInactive')->latest('updated_at');
            $segment = "Quarterly Inactive";
        }
        if($type == "Called_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Called')->latest('updated_at');
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
        if($request->start)
        {
            $data_table = $data_table->whereDate('created_at','>=',$request->start);
        }
        if($request->end)
        {
            $data_table = $data_table->whereDate('created_at','<=',$request->end);
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
        $count = $data_table->count();
        $data_table = $data_table->with('transactions','utilityTransaction','depositTransactions','user')->paginate(100);

       foreach ($data_table as $td ) {
            $allTranx = collect()->concat($td['transactions'])->concat($td['depositTransactions'])->concat($td['utilityTransaction']);
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

    public function createCallLog(Request $request)
    {
        $user_tracking = UserTracking::where('user_id',$request->id)->first();
        if($request->status != "NoResponse"){
            if(empty($request->id) OR empty($request->feedback) OR empty($request->status)){
                return redirect()->back()->with(['error' => 'Missing Fields']);
            }
        }
        
        if($request->status == "NoResponse")
        {
            $streak = $user_tracking->noResponse_streak;
            if($user_tracking->Current_Cycle == "NoResponse")
            {
                ++$streak;
            }
            UserTracking::where('user_id',$request->id)
            ->update([
                'Previous_Cycle' =>$user_tracking->Current_Cycle,
                'current_cycle_count_date' => now(),
                'Current_Cycle' => "NoResponse",
                'sales_id' => Auth::user()->id,
                'called_date'=> now(),
                'noResponse_streak'=>$streak,
            ]);
            return redirect()->back()->with(['success' => 'success']);
        }

        if($request->phoneNumber)
        {
            $call_log = CallLog::create([
                'user_id'=>$request->id,
                'call_response' =>$request->feedback,
                'call_category_id' => $request->status
            ]);
            

            $time = now();
            $openingPhoneTime = Carbon::parse($request->phoneNumber)->subSeconds(18);
            $timeDifference = $openingPhoneTime->diffInSeconds($time);
            UserTracking::where('user_id',$request->id)
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
            return redirect()->back()->with(['success' => 'Call Log Added']);
        }
        else{
            return redirect()->back()->with(['error' => 'Error Adding Call Log']);
        }

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
        UserTracking::where('user_id',$request->id)
        ->update([
            'call_log_id' => $call_log->id
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
        $data_table = $data_table->paginate(100);
        foreach ($data_table as $u ) {
            $user_tnx = Transaction::where('user_id',$u->user_id)->where('status','success')->latest('updated_at')->get();

            if($user_tnx->count() == 0)
            {
                $u->last_transaction_date = 'No Transactions';
            }
            else{
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

    

    public static function checkActiveUsers(){
        $active_users = UserTracking::where('Current_Cycle','Active')->with('transactions','utilityTransaction','depositTransactions','user')->get();
        foreach ($active_users as $au) {
            $user_tnx = collect()->concat($au['transactions'])->concat($au['utilityTransaction'])->concat($au['depositTransactions']);
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
        $quarterlyInactive = UserTracking::where('Current_Cycle','QuarterlyInactive')->with('transactions','utilityTransaction','depositTransactions','user')->get();
        foreach ($quarterlyInactive as $qi) {
            $userTranx = collect()->concat($qi['transactions'])->concat($qi['utilityTransaction'])->concat($qi['depositTransactions']);

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
        $called_users = UserTracking::where('Current_Cycle','Called')->with('transactions','utilityTransaction','depositTransactions','user')->get();
        foreach ($called_users as $cu ) {
            $cu->called_date = Carbon::parse($cu->called_date);
 
            $userTranx = $cu['transactions']->where('created_at','>=',$cu->called_date);
            $userUtil = $cu['utilityTransaction']->where('created_at','>=',$cu->called_date);

            $userDeposit = $cu['depositTransactions']->where('created_at','>=',$cu->called_date);
            $allTranx = collect()->concat($userTranx)->concat($userUtil)->concat($userDeposit);

            if($allTranx->count() >= 1)
            {
                UserTracking::find($cu->id)->update([
                    'Current_Cycle'=>"Responded",
                    'Previous_Cycle' => "Called",
                    'current_cycle_count_date' => now()
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
        $recalcitrant_users = UserTracking::where('Current_Cycle','Recalcitrant')->with('transactions','utilityTransaction','depositTransactions','user')->get();
        foreach ($recalcitrant_users as $ru) {
            $ru->current_cycle_count_date = Carbon::parse($ru->current_cycle_count_date);

            $userTranx = $ru['transactions']->where('created_at','>=',$ru->current_cycle_count_date);
            $userUtil = $ru['utilityTransaction']->where('created_at','>=',$ru->current_cycle_count_date);

            $userDeposit = $ru['depositTransactions']->where('created_at','>=',$ru->current_cycle_count_date);
            $allTranx = collect()->concat($userTranx)->concat($userUtil)->concat($userDeposit);

            if($allTranx->count() > 0)
            {

                UserTracking::find($ru->id)->update([
                    'Current_Cycle'=>"Responded",
                    'Previous_Cycle' => "Recalcitrant",
                    'current_cycle_count_date' => now()
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
                        'Responded_streak' => 0
                    ]);
                }
            }
        }
    }

    public static function CheckRespondedUsersForQualityInactive()
    {
        $responded_users = UserTracking::where('Current_Cycle','Responded')->with('transactions','utilityTransaction','depositTransactions','user')->get();
        foreach ($responded_users as $ru) {
            $ru->current_cycle_count_date = Carbon::parse($ru->current_cycle_count_date);

            $userTranx = $ru['transactions']->where('created_at','>=',$ru->current_cycle_count_date);
            $userUtil = $ru['utilityTransaction']->where('created_at','>=',$ru->current_cycle_count_date);

            $userDeposit = $ru['depositTransactions']->where('created_at','>=',$ru->current_cycle_count_date);
            $allTranx = collect()->concat($userTranx)->concat($userUtil)->concat($userDeposit);

            $monthDiff = $ru->current_cycle_count_date->diffInMonths(now());
            
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
                        'Recalcitrant_streak' => 0
                    ]); 
                }
            }
            else{
                $lastTranxDate = $allTranx->sortBy('created_at')->first()->created_at;
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


}
