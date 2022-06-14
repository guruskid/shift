<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewUsersTracking;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function addNewUsers()
    {
        $new_users = User::with('transactions')->whereDate('created_at','>',Carbon::now()->subMonths(3))->whereDate('created_at','<=',Carbon::now()->subDays(21))->where('role',1)->latest('created_at')->get();
        foreach ($new_users as $nu) {
            if($nu->transactions()->count() == 0)
            {
                $new_user_tracking = NewUsersTracking::where('user_id',$nu->id)->first();
                if($new_user_tracking == null)
                {
                    $new_user_tracking = new NewUsersTracking();
                    $new_user_tracking->user_id = $nu->id;
                    $new_user_tracking->status = "active";
                    $new_user_tracking->save();
                }
            }
        }
    }
    public function index($type = null){
        if($type == null){
            $type = 'all_user';
        }
        $this->addNewUsers();
        $new_user = NewUsersTracking::where('status','active')->count();
        $called_user = NewUsersTracking::where('status','goodlead')->orWhere('status','badlead')->count();
        $pending_user = NewUsersTracking::where('status','pending')->count();
        $good_user = NewUsersTracking::where('status','goodlead')->count();
        $bad_user = NewUsersTracking::Where('status','badlead')->count();
        $all_user = NewUsersTracking::where('status','goodlead')->get();
        $total = 0;
        foreach ($all_user as $au) {
            $user = User::find($au->user_id);
            $total += $user->transactions()->where('created_at','>=',$au->updated_at)->count();
        }
        $data_table = $this->tableCategory($type);
        return view('admin.sales.index',compact([
            'new_user','called_user','pending_user','good_user','bad_user','data_table','total','type'
        ]));
    }

    public function tableCategory($type)
    {
        $data = null;
        if($type == 'all_user' OR $type == null)
        {
            $data = NewUsersTracking::where('status','active')->latest('created_at')->get()->take(10);
        }
        if($type == 'called_user')
        {
            $data = NewUsersTracking::where('status','goodlead')->orWhere('status','badlead')->latest('created_at')->get()->take(10);
        }
        if($type == 'good_lead')
        {
            $data = NewUsersTracking::where('status','goodlead')->latest('created_at')->get()->take(10);
        }
        if($type == 'bad_lead')
        {
            $data = NewUsersTracking::Where('status','badlead')->latest('created_at')->get()->take(10);
        }
        if($type == 'pending_user')
        {
            $data = NewUsersTracking::where('status','pending')->latest('created_at')->get()->take(10);
        }
        if($type == 'traded_user')
        {
            $data_collection = collect([]);
            $user_data = NewUsersTracking::with('user')->where('status','goodlead')->latest('created_at')->get();
            foreach ($user_data as $ud) {
                if($ud->transactions()->where('created_at','>=',$ud->updated_at)->count() > 0)
                {
                    $data_collection = $data_collection->concat($ud);
                }
            }
            $data = $data_collection->take(10);
        }
        return $data;

    }

    public function viewCategory($type = null, Request $request)
    {
        $data = null;
        if($type == 'all_user' OR $type == null)
        {
            $segment = "New Users";
            $data = NewUsersTracking::where('status','active')->latest('created_at')->get();

        }
        if($type == 'called_user')
        {
            $segment = "Called Users";
            $data = NewUsersTracking::where('status','goodlead')->orWhere('status','badlead')->latest('created_at')->get();
        }
        if($type == 'good_lead')
        {
            $segment = "Good Leads";
            $data = NewUsersTracking::where('status','goodlead')->latest('created_at')->get();
        }
        if($type == 'bad_lead')
        {
            $segment = "Bad Lead";
            $data = NewUsersTracking::Where('status','badlead')->latest('created_at')->get();
        }
        if($type == 'pending_user')
        {
            $segment = "Pending Users";
            $data = NewUsersTracking::where('status','pending')->latest('created_at')->get();
        }
        if($type == 'traded_user')
        {
            $segment = "Traded Users";
            $data_collection = collect([]);
            $user_data = NewUsersTracking::with('user')->where('status','goodlead')->latest('created_at')->get();
            foreach ($user_data as $ud) {
                if($ud->transactions()->where('created_at','>=',$ud->updated_at)->count() > 0)
                {
                    $data_collection = $data_collection->concat($ud);
                }
            }
            $data = $data_collection;
        }
        
        if($request->start)
        {
            $data = $data->where('updated_at','>=',$request->start." 00:00:00");
        }
        if($request->end)
        {
            $data = $data->where('updated_at','<=',$request->end." 23:59:59");
        }
        $count = $data->count();
        $data_table = $data->paginate(1000);
        return view('admin.sales.users',compact([
            'segment','count','type','data_table'
        ]));

    }

    public function assignStatus(Request $request)
    {
        if($request->phoneNumber)
        {
            $time = now();
            $openingPhoneTime = Carbon::parse($request->phoneNumber)->subSeconds(18);
            $timeDifference = $openingPhoneTime->diffInSeconds($time);
            NewUsersTracking::where('user_id',$request->id)
            ->update([
                'status' => $request->status,
                'comment' => $request->feedback,
                'call_duration' => $timeDifference,
                'call_duration_timestamp' => $time,
                'sales_id' => Auth::user()->id,
            ]);
        }

        NewUsersTracking::where('user_id',$request->id)
            ->update([
                'status' => $request->status,
                'comment' => $request->feedback,
            ]);

        return back()->with(['success'=>'Status Updated']);
    }

    public function callLogs(Request $request)
    {
        $segment = "Call Logs";
        $type = 'logs';
        $data = NewUsersTracking::where('status','goodlead')->orWhere('status','badlead')->get();
        if($request->start)
        {
            $data = $data->where('updated_at','>=',$request->start." 00:00:00");
        }
        if($request->end)
        {
            $data = $data->where('updated_at','<=',$request->end." 23:59:59");
        }
        $count = $data->count();
        $data_table = $data->paginate(1000);
        return view('admin.sales.users',compact([
            'segment','count','type','data_table'
        ]));

    }

    public function userProfile(Request $request){
        $users = NewUsersTracking::orderBy('id','desc')->get();
        if($request->start)
        {
            $users = $users->where('updated_at','>=',$request->start." 00:00:00");
        }
        if($request->end)
        {
            $users = $users->where('updated_at','<=',$request->end." 23:59:59");
        }
        $users = $users->paginate(1000);
        $segment = "User Profile";
        return view('admin.sales.userProfile',compact([
            'segment','users'
        ]));
    }
}
