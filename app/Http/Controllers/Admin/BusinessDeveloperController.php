<?php

namespace App\Http\Controllers\Admin;

use App\CallCategory;
use App\CallLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\UserTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BusinessDeveloperController extends Controller
{
    public function index($type = null){ 
        $total_users = User::count();
        $QuarterlyInactiveUsers =  UserTracking::where('Current_Cycle','QuarterlyInactive')->count();
        $CalledUsers =  UserTracking::where('Current_Cycle','Called')->count();
        $RespondedUsers =  UserTracking::where('Current_Cycle','Responded')->count();
        $RecalcitrantUsers =  UserTracking::where('Current_Cycle','Recalcitrant')->count();
        $data_table = UserTracking::orderBy('id','desc')->get()->take(10);
        $call_categories = CallCategory::all();
        if($type == "Quarterly_Inactive")
        {
            $data_table = UserTracking::where('Current_Cycle','QuarterlyInactive')->latest('updated_at')->get()->take(10);
        }
        if($type == "Called_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Called')->latest('updated_at')->get()->take(10);
        }
        if($type == "Responded_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Responded')->latest('updated_at')->get()->take(10);
        }
        if($type == "Recalcitrant_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Recalcitrant')->latest('updated_at')->get()->take(10);
        }
        foreach ($data_table as $u ) {
            $user_tnx = Transaction::where('user_id',$u->user_id)->latest('updated_at')->get();
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
                'data_table','total_users','QuarterlyInactiveUsers','type','call_categories','CalledUsers','RespondedUsers','RecalcitrantUsers'
            ])
        );
    }

    public function viewCategory($type = null)
    {
        $call_categories = CallCategory::all();
        if($type == null || $type == "all_Users"){
            $data_table = UserTracking::latest('updated_at')->paginate(100);
            $segment = "All Users";
        }
        if($type == "Quarterly_Inactive")
        {
            $data_table = UserTracking::where('Current_Cycle','QuarterlyInactive')->latest('updated_at')->paginate(100);
            $segment = "Quarterly Inactive";
        }
        if($type == "Called_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Called')->latest('updated_at')->paginate(100);
            $segment = "Called Users";
        }
        if($type == "Responded_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Responded')->latest('updated_at')->paginate(100);
            $segment = "Responded Users";
        }
        if($type == "Recalcitrant_Users")
        {
            $data_table = UserTracking::where('Current_Cycle','Recalcitrant')->latest('updated_at')->paginate(100);
            $segment = "Recalcitrant Users";
        }
        foreach ($data_table as $u ) {
            $user_tnx = Transaction::where('user_id',$u->user_id)->latest('updated_at')->get();
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
                'data_table','type','segment','call_categories'
            ])
        );
    }

    public function createCallLog(Request $request)
    {
        if(empty($request->id) || empty($request->feedback) || empty($request->status)){
            return redirect()->back()->with(['error' => 'Error Adding Call Log']);
        }

        $call_log = CallLog::create([
            'user_id'=>$request->id,
            'call_response' =>$request->feedback,
            'call_category_id' => $request->status
        ]);
        $user_tracking = UserTracking::where('user_id',$request->id)->first();
        UserTracking::where('user_id',$request->id)
        ->update([
            'call_log_id' => $call_log->id,
            'Previous_Cycle' =>$user_tracking->Current_Cycle,
            'Current_Cycle' => "Called",
            'current_cycle_count_date' => Carbon::now()
        ]);
        return redirect()->back()->with(['success' => 'Call Log Added']);

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
        // dd($request->status);
        $data_table = CallLog::latest('updated_at');
        $segment = "Call Log";
        $type = "Responded_Users";
        $call_categories = CallCategory::all();

        if($request->status)
        {
            $data_table = $data_table->where('call_category_id', $request->status);
        }
        $data_table = $data_table->paginate(1000);
        foreach ($data_table as $u ) {
            $user_tnx = Transaction::where('user_id',$u->user_id)->latest('updated_at')->get();
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



    public function UserProfile()
    {
        $users = User::orderBy('id','desc')->paginate(100);
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

    public function deleteCallCategory($id)
    {   
        CallCategory::find($id)->delete();
        return back()->with(['success'=>'Call Category deleted']);
    }

    

    public static function checkActiveUsers(){
        $active_users = UserTracking::where('Current_Cycle','Active')->orderBy('id','desc')->get();
        foreach ($active_users as $au) {
            $User_tnx = Transaction::where('user_id',$au->user_id)->count();
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
                $last_user_transaction_date = Transaction::where('user_id',$au->user_id)->latest('updated_at')->first()->updated_at;
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

    public static function checkCalledUsersForRespondedAndRecalcitrant()
    {
        $called_users = UserTracking::where('Current_Cycle','Called')->orderBy('id','desc')->get();
        foreach ($called_users as $cu ) {
            $cu->current_cycle_count_date = Carbon::parse($cu->current_cycle_count_date);
            $User_tnx = Transaction::where('user_id',$cu->user_id)->where('updated_at','>=',$cu->current_cycle_count_date)->count();
            $diff_in_months = $cu->current_cycle_count_date->diffInMonths(Carbon::now());
            if($diff_in_months >= 1)
            {
                if($User_tnx >= 1)
                {
                    UserTracking::find($cu->id)->update([
                        'Current_Cycle'=>"Responded",
                        'Previous_Cycle' => "Called",
                        'current_cycle_count_date' => Carbon::now()
                    ]);
                }
                else{
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
            $User_tnx = Transaction::where('user_id',$ru->user_id)->where('updated_at','>=',$ru->current_cycle_count_date)->count();
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
            $User_tnx = Transaction::where('user_id',$ru->user_id)->where('updated_at','>',$ru->current_cycle_count_date)->count();
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
                    $last_transaction_date = Transaction::where('user_id',$ru->user_id)->where('updated_at','>=',$ru->current_cycle_count_date)->latest('updated_at')->first()->updated_at;
                    UserTracking::find($ru->id)->update([
                        'current_cycle_count_date' => $last_transaction_date
                    ]);
                }
            }
        }
    }



    public function QuarterlyInactiveFromOldUsersDB() {
        if(!Auth::user()->role == 999 ){
            abort(404);
        }
        $all_users = User::all();
        UserTracking::truncate();
        foreach ($all_users as $u) {
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
        return redirect()->back()->with("success", "Database Populated");
    }


}
