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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BusinessDeveloperController extends Controller
{
    public function index($type = null){ 
        $total_users = User::count();
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
            $data_table = UserTracking::where('Current_Cycle','NoResponse')->latest('updated_at')->get()->take(20);
        }
        if($type == "Quarterly_Inactive")
        {
            $data_table = UserTracking::where('Current_Cycle','QuarterlyInactive')->latest('updated_at')->get()->take(20);
        }
        if($type == "Called_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Called')->latest('updated_at')->get()->take(20);
        }
        if($type == "Responded_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Responded')->latest('updated_at')->get()->take(20);
        }
        if($type == "Recalcitrant_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Recalcitrant')->latest('updated_at')->get()->take(20);
        }
        
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
            'admin.business_developer.index',
            compact([
                'data_table','total_users','QuarterlyInactiveUsers','type','call_categories','CalledUsers','RespondedUsers','RecalcitrantUsers',
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
        $active_users = UserTracking::where('Current_Cycle','Active')->get();
        foreach ($active_users as $au) {
            $User_tnx = Transaction::where('user_id',$au->user_id)->where('status','success')->count();
            if($User_tnx == 0)
            {
                $diff_in_months = $au->user->created_at->diffInMonths(Carbon::now());
                if($diff_in_months >=3)
                {
                    UserTracking::find($au->id)->update([
                        'Current_Cycle'=>"QuarterlyInactive",
                        'current_cycle_count_date' => Carbon::now()
                    ]);
                }
            }
            else{
                $last_user_transaction_date = Transaction::where('user_id',$au->user_id)->where('status','success')->latest('updated_at')->first()->updated_at;
                $diff_in_months = $last_user_transaction_date->diffInMonths(Carbon::now());
                if($diff_in_months >=3)
                {
                    UserTracking::find($au->id)->update([
                        'Current_Cycle'=>"QuarterlyInactive",
                        'current_cycle_count_date' => Carbon::now()
                    ]);
                }

            }
        }
    }

    //*if the transaction has been done the do a daily check 
    //*if the transaction is 0 leave until Three months.
    public static function checkCalledUsersForRespondedAndRecalcitrant()
    {
        $called_users = UserTracking::where('Current_Cycle','Called')->orderBy('current_cycle_count_date','asc')->get();

        $transactions = Transaction::where('created_at','>=',$called_users[0]->current_cycle_count_date)->where('status','success')->get();
        $utility = UtilityTransaction::where('created_at','>=',$called_users[0]->current_cycle_count_date)->where('status','success')->get();
        $payBridgeDeposit = NairaTransaction::where('transaction_type_id',1)->where('created_at','>=',$called_users[0]->current_cycle_count_date)->where('status','success')->get();

        foreach ($called_users as $cu ) {
            $cu->current_cycle_count_date = Carbon::parse($cu->current_cycle_count_date);
            $User_tnx = $transactions->where('user_id',$cu->user_id)->where('created_at','>=',$cu->current_cycle_count_date)->count();
            
            $user_util = $utility->where('user_id',$cu->user_id)->where('created_at','>=',$cu->current_cycle_count_date)->count();
            $user_deposit = $payBridgeDeposit->where('user_id',$cu->user_id)->where('created_at','>=',$cu->current_cycle_count_date)->count();

            $tranxCount = $User_tnx + $user_util + $user_util;
            if($tranxCount >= 1)
            {
                UserTracking::find($cu->id)->update([
                    'Current_Cycle'=>"Responded",
                    'Previous_Cycle' => "Called",
                    'current_cycle_count_date' => Carbon::now()
                ]);
            }
            else{
                $diff_in_months = $cu->current_cycle_count_date->diffInMonths(Carbon::now());
                dd($cu->current_cycle_count_date);
                if($diff_in_months >= 1){
                    UserTracking::find($cu->id)->update([
                        'Current_Cycle'=>"Recalcitrant",
                        'Previous_Cycle' => "Called",
                        'current_cycle_count_date' => Carbon::now()
                    ]);
                }
            }    
        }
    }


    public static function CheckRecalcitrantUsersForResponded()
    {
        $recalcitrant_users = UserTracking::where('Current_Cycle','Recalcitrant')->orderBy('id','desc')->get();
        foreach ($recalcitrant_users as $ru) {
            $ru->current_cycle_count_date = Carbon::parse($ru->current_cycle_count_date);
            $User_tnx = Transaction::where('user_id',$ru->user_id)->where('updated_at','>=',$ru->current_cycle_count_date)->where('status','success')->count();
            $diff_in_months = $ru->current_cycle_count_date->diffInMonths(Carbon::now());
            if($diff_in_months >= 2){
                if($User_tnx == 0)
                {
                    UserTracking::find($ru->id)->update([
                        'Recalcitrant_Cycle' => $ru->Recalcitrant_Cycle + 1,
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Recalcitrant",
                        'call_log_id' => null,
                        'current_cycle_count_date' => Carbon::now(),
                        'Recalcitrant_streak' => $ru->Recalcitrant_streak + 1,
                        'Responded_streak' => 0
                    ]);


                }
                else{
                    UserTracking::find($ru->id)->update([
                        'Current_Cycle'=>"Responded",
                        'Previous_Cycle' => "Recalcitrant",
                        'current_cycle_count_date' => Carbon::now()
                    ]);
                }
            }
        }
    }

    public static function CheckRespondedUsersForQualityInactive()
    {
        $responded_users = UserTracking::where('Current_Cycle','Responded')->orderBy('id','desc')->get();
        foreach ($responded_users as $ru) {
            $ru->current_cycle_count_date = Carbon::parse($ru->current_cycle_count_date);
            $User_tnx = Transaction::where('user_id',$ru->user_id)->where('updated_at','>',$ru->current_cycle_count_date)->where('status','success')->count();
            $diff_in_months = $ru->current_cycle_count_date->diffInMonths(Carbon::now());
            if($diff_in_months >=2)
            {
                if($User_tnx == 0)
                {
                    UserTracking::find($ru->id)->update([
                        'Responded_Cycle' => $ru->Responded_Cycle + 1,
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Responded",
                        'call_log_id' => null,
                        'current_cycle_count_date' => Carbon::now(),
                        'Responded_streak' => $ru->Responded_streak + 1,
                        'Recalcitrant_streak' => 0
                   ]);
                }else{
                    $last_transaction_date = Transaction::where('user_id',$ru->user_id)->where('updated_at','>=',$ru->current_cycle_count_date)->where('status','success')->latest('updated_at')->first()->updated_at;
                    UserTracking::find($ru->id)->update([
                        'current_cycle_count_date' => $last_transaction_date
                    ]);
                }
            }
        }
    }

    public static function QuarterlyInactiveFromOldUsersDB() {
        UserTracking::truncate();
        CallLog::truncate();
        $all_users = User::where('role',1)->latest('created_at')->get();
        foreach ($all_users as $u) {
            $userTracking = UserTracking::where('user_id',$u->id)->count();
            if($userTracking == 0){
                if($u->transactions()->count() == 0)
                {
                    $diff_in_months = $u->created_at->diffInMonths(Carbon::now());
                    
                    if($diff_in_months >=3)
                    {
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $u->id;
                        $user_tracking->Current_Cycle = "QuarterlyInactive";
                        $user_tracking->current_cycle_count_date = Carbon::now();
                        $user_tracking->save();

                    }
                    else{
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $u->id;
                        $user_tracking->Current_Cycle = "Active";
                        $user_tracking->current_cycle_count_date = Carbon::now();
                        $user_tracking->save();
                    }
                }
                else{
                    $last_user_transaction_date = $u->transactions()->latest('updated_at')->first()->updated_at;
                    $diff_in_months = $last_user_transaction_date->diffInMonths(Carbon::now());
                    
                    if($diff_in_months >=3)
                    {
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $u->id;
                        $user_tracking->Current_Cycle = "QuarterlyInactive";
                        $user_tracking->current_cycle_count_date = Carbon::now();
                        $user_tracking->save();

                    }
                    else{
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $u->id;
                        $user_tracking->Current_Cycle = "Active";
                        $user_tracking->current_cycle_count_date = Carbon::now();
                        $user_tracking->save();
                    }

                }
            }
        }
        return redirect()->back()->with("success", "Database Populated");
    }


}
