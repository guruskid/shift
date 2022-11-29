<?php

namespace App\Http\Controllers\Admin;

use App\CallCategory;
use App\CallLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NewUsersSalesController extends Controller
{
    public function index($type = NULL){
        $salesCategory = 'new';
        $call_categories = CallCategory::all();
        $activeUsersCount =  UserTracking::where('Current_Cycle','Active')->count();

        $userTrackingByCustodianID = UserTracking::with('transactions','user')
        ->where('custodian_id',Auth::user()->id)
        ->orderBy('created_at','DESC')
        ->get();
        $userTrackingBySalesID = UserTracking::with('transactions','user')
        ->where('sales_id',Auth::user()->id)
        ->orderBy('created_at','DESC')
        ->get();

        // new inactive users
        $newInactiveUsers = $userTrackingByCustodianID
        ->where('Current_Cycle','NewInActiveUser');

        $newInactiveUsersCount = $newInactiveUsers->count();

        // new called users
        $newCalledUsers = $userTrackingBySalesID
        ->where('Current_Cycle','NewCalledUser');

        $newCalledUsersCount = $newCalledUsers->count();

        //unresponsive
        $newUnresponsiveUsers =  $userTrackingBySalesID
        ->where('Current_Cycle','NewUnresponsiveUser');

        $newUnresponsiveUsersCount = $newUnresponsiveUsers->count();

        //quarterly inactive 
        // quarterly inactive users are users that has not traded for 3 months
        $QuarterlyInactiveUsers = $userTrackingByCustodianID
        ->where('Current_Cycle','QuarterlyInactive');

        $QuarterlyInactiveUsersCount = $QuarterlyInactiveUsers->count();

        // Called User
        // Called Users are users that have been called by a sales personnel
        $CalledUsers =  UserTracking::with('transactions','user')
        ->where('called_date','>=',Carbon::today())
        ->whereIn('Current_Cycle',['Called','Responded','Recalcitrant'])
        ->where('sales_id',Auth::user()->id)
        ->latest('updated_at')->get();

        $calledUsersCount = $CalledUsers->count();

        // No Response
        // No Response are Users that did not pick a call from the sales personnel
        $NoResponse = $userTrackingBySalesID
        ->where('Current_Cycle','NoResponse');

        $NoResponseCount = $NoResponse->count();

        //Responded Users
        //Responded Users are Users who have traded after being called
        $RespondedUsers =  $userTrackingBySalesID
        ->where('Current_Cycle','Responded');

        $RespondedUsersCount = $RespondedUsers->count();

        //Recalcitrant Users
        //Recalcitrant Users are Users who have not traded after being called
        $RecalcitrantUsers = $userTrackingBySalesID
        ->where('Current_Cycle','Recalcitrant');

        $RecalcitrantUsersCount = $RecalcitrantUsers->count();

        switch ($type) {
            case 'newInactiveUser':
                $data_table = $newInactiveUsers->take(20);
                break;
            case 'calledNewUsers':
                $data_table = $newCalledUsers->take(20);
                break;
            case 'newUnresponsiveUser':
                $data_table = $newUnresponsiveUsers->take(20);
                break;
            
            default:
                return redirect()->back()->with(['error' => 'Category Does Not Exist']);
                break;
        }

        foreach ($data_table as $userData) {
            $user_transactions = $userData->transactions;
            $userData->last_transaction_date = 'No Transactions';
            if($user_transactions->count() > 0){
                $last_transaction = $user_transactions->sortByDesc('updated_at')->first();
                $userData->last_transaction_date = $last_transaction->updated_at->format('d M y, h:ia');
            }
            $userData->signup = $userData->user->created_at->format('d M y, h:ia');
            $daysInSystem = $userData->user->created_at->diffInDays(now());
            $userData->daysLeft = (90 - $daysInSystem).' days left';
            $cycleDate = Carbon::parse($userData->current_cycle_count_date);
            $userData->daysLeftInCycle = $cycleDate->diffInDays(now()).' days';

            if($userData->called_date){
                $calledDate = Carbon::parse($userData->called_date);
                $userData->called_date = $calledDate->format('d M y, h:ia');
            }
        }

        return view('admin.business_developer.index',
            compact([
                'data_table','QuarterlyInactiveUsersCount','type','call_categories','calledUsersCount','RespondedUsersCount','RecalcitrantUsersCount',
                'NoResponseCount','salesCategory','newInactiveUsersCount','newCalledUsersCount','newUnresponsiveUsersCount','activeUsersCount'
            ])
        );

    }

    public function creatingCalledLog(Request $request){
        $user_tracking = UserTracking::where('user_id',$request->id)->first();
        if(!($request->start AND $request->end)) {
            return redirect()->back()->with(['error' => 'Invalid Request User Number Not Viewed']);
        }

        if($user_tracking->Current_Cycle == 'NewInActiveUser'){
            $startTIme = Carbon::parse($request->start);
            $endTime = Carbon::parse($request->end);
            $timeDifference = $startTIme->diffInSeconds($endTime);

            $call_log = new CallLog();
            $call_log->user_id = $request->id;
            $call_log->call_response = $request->feedback;
            $call_log->call_category_id = $request->category_name;
            $call_log->sales_id = Auth::user()->id;
            $call_log->type = 'new';
            $call_log->save();


            UserTracking::where('user_id',$request->id)
            ->update([
                'call_log_id' => $call_log->id,
                'Previous_Cycle' =>$user_tracking->Current_Cycle,
                'Current_Cycle' => "NewCalledUser",
                'current_cycle_count_date' => now(),
                'call_duration' => $timeDifference,
                'call_duration_timestamp' => now(),
                'sales_id' => Auth::user()->id,
                'called_date'=> now()
            ]);

            return redirect()->back()->with(['success' => 'Call Log Added']);
        } else {
            return redirect()->back()->with(['success' => 'User Already Called']);
        }
    }

    public function viewNewCategory($type = null, Request $request){
        $call_categories = CallCategory::all();
        $salesCategory = 'new';

        $userTrackingByCustodianID = UserTracking::with('transactions','user')
        ->where('custodian_id',Auth::user()->id)
        ->orderBy('created_at','DESC');

        $userTrackingBySalesID = UserTracking::with('transactions','user')
        ->where('sales_id',Auth::user()->id)
        ->orderBy('created_at','DESC');

        switch ($type) {
            case 'newInactiveUser':
                $newInactiveUsers = $userTrackingByCustodianID->where('Current_Cycle','NewInActiveUser');
                $data_table = $newInactiveUsers;
                $segment = 'New Inactive Users';
                break;
            case 'calledNewUsers':
                $newCalledUsers = $userTrackingBySalesID->where('Current_Cycle','NewCalledUser');
                $data_table = $newCalledUsers;
                if($request->start){
                    $data_table = $data_table->whereDate('called_date','>=',$request->start);
                    if($request->end){
                        $data_table = $data_table->whereDate('called_date','<=',$request->end);
                    }
                }
                $segment = 'New Called Users';
                break;
            case 'newUnresponsiveUser':
                $newUnresponsiveUsers =  $userTrackingBySalesID->where('Current_Cycle','NewUnresponsiveUser');
                $data_table = $newUnresponsiveUsers;
                $segment = 'New Unresponsive Users';
                break;
            
            default:
                return redirect()->back()->with(['error' => 'Category Does Not Exist']);
                break;
        }

        if($request->start AND $type != "calledNewUsers"){
            $data_table = $data_table->whereDate('current_cycle_count_date','>=',$request->start);
            if($request->end){
                $data_table = $data_table->whereDate('current_cycle_count_date','<=',$request->end);
            }
        }

        if($request->search){
            $search = $request->search;
            $data_table = $data_table->whereHas('user',function($query) use($search){
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $count = $data_table->count();
        $data_table = $data_table->paginate(100);

        foreach ($data_table as $userData) {
            $user_transactions = $userData->transactions;
            $userData->last_transaction_date = 'No Transactions';
            if($user_transactions->count() > 0){
                $last_transaction = $user_transactions->sortByDesc('updated_at')->first();
                $userData->last_transaction_date = $last_transaction->updated_at->format('d M y, h:ia');
            }
            $userData->signup = $userData->user->created_at->format('d M y, h:ia');
            $daysInSystem = $userData->user->created_at->diffInDays(now());
            $userData->daysLeft = (90 - $daysInSystem).' days left';
            $cycleDate = Carbon::parse($userData->current_cycle_count_date);
            $userData->daysLeftInCycle = $cycleDate->diffInDays(now()).' days';

            if($userData->called_date){
                $calledDate = Carbon::parse($userData->called_date);
                $userData->called_date = $calledDate->format('d M y, h:ia');
            }
        }
        return view(
            'admin.business_developer.users',
            compact([
                'data_table','type','segment','call_categories','count','salesCategory'
            ])
        );

    }

    public function newUsersCallLog(Request $request){
        $salesCategory = 'new';
        $data_table = CallLog::with('user','call_category')
        ->where('type',$salesCategory)
        ->latest('updated_at');
        $segment = "New Users Call Log";

        $type = "callLog";
        $call_categories = CallCategory::all();
        if($request->start){
            $data_table = $data_table->whereDate('created_at','>=',$request->start);
            if($request->end){
            $data_table = $data_table->whereDate('created_at','<=',$request->end);
            }
        }
        
        if($request->status) {
            $data_table = $data_table->where('call_category_id', $request->status);
        }

        $data_table = $data_table->where('sales_id',Auth::user()->id)->paginate(100);
        foreach ($data_table as $userData ) {
            $user_transactions = $userData->user->transactions;
            $userData->last_transaction_date = 'No Transactions';
            if($user_transactions->count() > 0){
                $last_transaction = $user_transactions->sortByDesc('updated_at')->first();
                $userData->last_transaction_date = $last_transaction->updated_at->format('d M y, h:ia');
            }
            $userData->signup = $userData->user->created_at->format('d M y, h:ia');
        }
        return view(
            'admin.business_developer.users',
            compact([
                'data_table','segment','call_categories','type','salesCategory'
            ])
        );
    }

    public static function checkingCalledForUnresponsive(){
        $calledUsers = UserTracking::with('transactions','user')
        ->where('Current_Cycle',"NewCalledUser")
        ->get();

        foreach($calledUsers as $userData){
            $daysInSystem = $userData->user->created_at->diffInDays(now());
            $daysLeft = (90 - $daysInSystem);

            if($daysLeft <= 0){
                $userData->update([
                    'Current_Cycle' => 'QuarterlyInactive',
                    'Previous_Cycle' => $userData->Current_Cycle,
                    'current_cycle_count_date' => now(),
                ]);
            } else {
                $userTransactions = $userData->transactions;
                $recentUserTransactions = $userTransactions->where('created_at' >= $userData->called_date);
                $recentUserTransactionsCount = $recentUserTransactions->count();
                
                if($recentUserTransactionsCount > 0){
                    $userData->update([
                        'Current_Cycle' => 'NewActiveUser',
                        'Previous_Cycle' => $userData->Current_Cycle,
                        'current_cycle_count_date' => now(),
                    ]);
                } else {
                    $cycleDate = Carbon::parse($userData->current_cycle_count_date);
                    $daysLeftInCycle = $cycleDate->diffInDays(now());
                    if($daysLeftInCycle >= 14 ){
                        $userData->update([
                            'Current_Cycle' => 'NewUnresponsiveUser',
                            'Previous_Cycle' => $userData->Current_Cycle,
                            'current_cycle_count_date' => now(),
                        ]);
                    }
                }
            }
        }
    }

    public static function unresponsiveForNewInactive(){
        $unresponsiveUsers = UserTracking::with('transactions','user')
        ->where('Current_Cycle',"NewUnresponsiveUser")
        ->get();

        foreach($unresponsiveUsers as $userData){
            $daysInSystem = $userData->user->created_at->diffInDays(now());
            $daysLeft = (90 - $daysInSystem);

            if($daysLeft <= 0){
                $userData->update([
                    'Current_Cycle' => 'QuarterlyInactive',
                    'Previous_Cycle' => $userData->Current_Cycle,
                    'current_cycle_count_date' => now(),
                ]);
            } else {
                $userTransactions = $userData->transactions;
                $recentUserTransactions = $userTransactions->where('created_at' >= $userData->current_cycle_count_date);
                $recentUserTransactionsCount = $recentUserTransactions->count();
                
                if($recentUserTransactionsCount > 0){
                    $userData->update([
                        'Current_Cycle' => 'NewActiveUser',
                        'Previous_Cycle' => $userData->Current_Cycle,
                        'current_cycle_count_date' => now(),
                    ]);
                } 
            }
        }
    }

    public static function newActiveForUnresponsive(){
        $activeNewUsers = UserTracking::with('transactions','user')
        ->where('Current_Cycle',"NewActiveUser")
        ->get();

        foreach($activeNewUsers as $userData){
            $daysInSystem = $userData->user->created_at->diffInDays(now());
            $daysLeft = (90 - $daysInSystem);

            if($daysLeft <= 0){
                $userData->update([
                    'Current_Cycle' => 'Active',
                    'Previous_Cycle' => $userData->Current_Cycle,
                    'current_cycle_count_date' => now(),
                ]);
            } else {
                $userTransactions = $userData->transactions;
                $recentUserTransactions = $userTransactions->where('created_at' >= $userData->current_cycle_count_date);
                $recentUserTransactionsCount = $recentUserTransactions->count();

                $cycleDate = Carbon::parse($userData->current_cycle_count_date);
                $daysLeftInCycle = $cycleDate->diffInDays(now());
                if($daysLeftInCycle >= 14){
                    if($recentUserTransactionsCount > 0){
                        $userData->update([
                            'current_cycle_count_date' => now(),
                        ]);
                    } else {
                        $userData->update([
                            'Current_Cycle' => 'NewUnresponsiveUser',
                            'Previous_Cycle' => $userData->Current_Cycle,
                            'current_cycle_count_date' => now(),
                        ]);
                    }

                }
            }
        }
    }
}
