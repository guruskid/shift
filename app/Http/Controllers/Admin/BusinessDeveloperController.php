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
    // please NB the current cycle names is in mixed characters please don't mix it up
    public function index($type = null){
        $salesCategory = 'old';
        /**IMPORTANT NOTE
        There is custodian ID and sales ID on the DB means different things 
        The Sales ID mean that this user was either called by a user
        Custodian ID refers to the fact that no matter where the user the user is in the system loop he/she belongs to a sales personnel*/

        //NEW USER DATA
        $newUserData = SalesHelperController::DataAnalytics();
        $activeUsersCount =  UserTracking::where('Current_Cycle','Active')->count();

        $newInactiveUsersCount = $newUserData['newInactiveUsers'];
        $newCalledUsersCount = $newUserData['newCalledUsers'];
        $newUnresponsiveUsersCount = $newUserData['newUnresponsiveUsers'];

        //quarterly inactive 
        // quarterly inactive users are users that has not traded for 3 months
        $QuarterlyInactiveUsersCount =  UserTracking::where('Current_Cycle','QuarterlyInactive')
        ->where('custodian_id',Auth::user()->id)
        ->count();

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
        $NoResponse = UserTracking::with('transactions','user')
        ->where('Current_Cycle','NoResponse')
        ->where('sales_id',Auth::user()->id)
        ->latest('updated_at')->get();

        $NoResponseCount = $NoResponse->count();

        //Responded Users
        //Responded Users are Users who have traded after being called
        $RespondedUsers =  UserTracking::with('transactions','user')
        ->where('Current_Cycle','Responded')
        ->where('sales_id',Auth::user()->id)
        ->latest('updated_at')->get();

        $RespondedUsersCount = $RespondedUsers->count();

        //Recalcitrant Users
        //Recalcitrant Users are Users who have not traded after being called
        $RecalcitrantUsers = UserTracking::with('transactions','user')
        ->where('Current_Cycle','Recalcitrant')
        ->where('sales_id',Auth::user()->id)
        ->latest('updated_at')
        ->get();

        $RecalcitrantUsersCount = $RecalcitrantUsers->count();

        $call_categories = CallCategory::all();

        if($type == null){
            $type = 'Quarterly_Inactive';
        }

        switch ($type) {
            case 'NoResponse':
                $data_table = $NoResponse->take(20);
                break;

            case 'Called_Users':
                $data_table = $CalledUsers->take(20);
                break;

            case 'Responded_Users':
                $data_table = $RespondedUsers->take(20);
                break;

            case 'Recalcitrant_Users':
                $data_table = $RecalcitrantUsers->take(20);
                break;
            
            case 'Quarterly_Inactive':
                $data_table = SalesHelperController::quarterlyInactive()->take(20);
                break;
            
            default:
                return redirect()->back()->with(['error' => 'Category Does Not Exist']);
                break;
        }

        if($type != 'Quarterly_Inactive'){
            // the quarterly inactive has checks for the transactions there is no need to run it through this iteration
            // checking UserData
            foreach($data_table as $userData){
                //getting the users successful transactions (NB: check the relationship in the model for more information)
                $userTransactions = $userData->transactions;
                $userData->called_date = Carbon::parse($userData->called_date)->format('d M Y, h:ia');

                if($userTransactions->count() == 0){
                    $userData->last_transaction_date = 'No Transactions';
                } else {
                    $userData->last_transaction_date = $userTransactions->first()->created_at->format('d M Y, h:ia');
                }
            }
        }
        return view(
            'admin.business_developer.index',
            compact([
                'data_table','QuarterlyInactiveUsersCount','type','call_categories','calledUsersCount','RespondedUsersCount','RecalcitrantUsersCount',
                'NoResponseCount','salesCategory','newInactiveUsersCount','newCalledUsersCount','newUnresponsiveUsersCount','activeUsersCount'
            ])
        );
    }

    public function viewCategory($type = null, Request $request)
    {
        $call_categories = CallCategory::all();
        $salesCategory = 'old';

        switch ($type) {
            case 'NoResponse':
                $data_table = UserTracking::where('Current_Cycle','NoResponse')->latest('updated_at');
                $segment = "No Response";
                break;
            case 'Quarterly_Inactive':
                return  SalesHelperController::sortQuarterlyInactive($request, $type, $call_categories);
                break;
            case 'Called_Users':
                $data_table = UserTracking::whereIn('Current_Cycle',['Called','Responded','Recalcitrant'])->latest('called_date');
                if($request->start){
                    $data_table = $data_table->whereDate('called_date','>=',$request->start);
                    if($request->end){
                        $data_table = $data_table->whereDate('called_date','<=',$request->end);
                    }
                }
                $segment = "Called Users";
                break;
            case 'Responded_Users':
                $data_table = UserTracking::where('Current_Cycle','Responded')->latest('updated_at');
                $segment = "Responded Users";
                break;
            case 'Recalcitrant_Users':
                $data_table = UserTracking::where('Current_Cycle','Recalcitrant')->latest('updated_at');
                $segment = "Recalcitrant Users";
                break;
            
            default:
                return redirect()->back()->with(['error' => 'Category Does Not Exist']);
                break;
        }
        
        if($request->start AND $type != "Called_Users"){
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

        $count = $data_table->where('sales_id',Auth::user()->id)->count();
        $data_table = $data_table->with('transactions','user')->where('sales_id',Auth::user()->id)->paginate(100);

        foreach($data_table as $userData){
            //getting the users successful transactions (NB: check the relationship in the model for more information)
            $userTransactions = $userData->transactions;
            $userData->called_date = Carbon::parse($userData->called_date)->format('d M Y, h:ia');

            if($userTransactions->count() == 0){
                $userData->last_transaction_date = 'No Transactions';
            } else {
                $userData->last_transaction_date = $userTransactions->first()->created_at->format('d M Y, h:ia');
            }
        }
        return view(
            'admin.business_developer.users',
            compact([
                'data_table','type','segment','call_categories','count','salesCategory'
            ])
        );
    }

    public function createCallLog(Request $request)
    {
        $user_tracking = UserTracking::where('user_id',$request->id)->first();

        if($request->category_name == "NoResponse") { // no response
            SalesHelperController::noResponseUpdate($user_tracking, $request->id);
            return redirect()->back()->with(['success' => 'success']);
        }

        if($request->category_name == 12){ // multipleAccounts
            SalesHelperController::DisableMultiAccount($user_tracking, $request->id);
            return redirect()->back()->with(['success' => 'success']);
        }


        if($request->start AND $request->end){ //if there is a phone number Viewed, this tracks 
            if($user_tracking->Current_Cycle == 'QuarterlyInactive'){
                $startTIme = Carbon::parse($request->start)->subSeconds(20);
                $endTime = Carbon::parse($request->end);
                $timeDifference = $startTIme->diffInSeconds($endTime);
                
                $call_log = new CallLog();
                $call_log->user_id = $request->id;
                $call_log->call_response = $request->feedback;
                $call_log->call_category_id = $request->category_name;
                $call_log->sales_id = Auth::user()->id;
                $call_log->type = 'old';
                $call_log->save();

                UserTracking::where('user_id',$request->id)
                ->update([
                    'call_log_id' => $call_log->id,
                    'Previous_Cycle' =>$user_tracking->Current_Cycle,
                    'Current_Cycle' => "Called",
                    'current_cycle_count_date' => now(),
                    'call_duration' => $timeDifference,
                    'call_duration_timestamp' => now(),
                    'sales_id' => Auth::user()->id,
                    'called_date'=> now(),
                ]);
                self::freeWithdrawalActivation($request->id);
                return redirect()->back()->with(['success' => 'Call Log Added']);
            } else {
                return redirect()->back()->with(['success' => 'User Already Called']);
            }

        } else {
            return redirect()->back()->with(['error' => 'Invalid Request User Number Not Viewed']);
        }

    }

    // public static function storeCalledData(UserTracking $user_tracking, $id, $feedback, $status, $phoneNumber)
    // {
    //     // saving a call log 
    //     $call_log = new CallLog();
    //     $call_log->user_id = $id;
    //     $call_log->call_response = $feedback;
    //     $call_log->call_category_id = $status;
    //     $call_log->sales_id = Auth::user()->id;
    //     $call_log->type = 'old';
    //     $call_log->save();

    //     $time = now();
    //     $openingPhoneTime = Carbon::parse($phoneNumber)->subSeconds(18);
    //     $timeDifference = $openingPhoneTime->diffInSeconds($time);

    //     UserTracking::where('user_id',$id)
    //     ->update([
    //         'call_log_id' => $call_log->id,
    //         'Previous_Cycle' =>$user_tracking->Current_Cycle,
    //         'Current_Cycle' => "Called",
    //         'current_cycle_count_date' => $time,
    //         'call_duration' => $timeDifference,
    //         'call_duration_timestamp' => $time,
    //         'sales_id' => Auth::user()->id,
    //         'called_date'=> $time
    //     ]);

        
    // }

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

    public static function freeWithdrawals(){
        $user = Auth::user();
        $userTracking = UserTracking::where('user_id', $user->id)
        ->whereNotIn('Current_Cycle',['QuarterlyInactive','NoResponse','DeadUser'])
        ->first();

        if($userTracking == null){
            return 0;
        } else {
            return $userTracking->free_withdrawal;
        }
    }

    public static function freeWithdrawalsReduction($number){
        $user = Auth::user();
        $userTracking = UserTracking::where('user_id', $user->id)->first();

        $userTracking->update([
            'free_withdrawal' => ($userTracking->free_withdrawal - $number),
        ]);
    }

    public function UpdateCallLog(Request $request){
        $call_log = CallLog::Find($request->id);
        if(!$call_log){
            return redirect()->back()->with(['error' => 'Invalid Call Log']);
        }
        $call_log->update([
            'call_response' =>$request->feedback,
            'call_category_id' => $request->status
        ]);
        return redirect()->back()->with(['success' => 'Call Log Updated']);
    }

    public function CallLog(Request $request){
        $salesCategory = 'old';
        $data_table = CallLog::with('user','call_category')
        ->where('type',$salesCategory)
        ->latest('updated_at');
        
        $segment = "Old Users Call Log";

        $type = "callLog";
        $call_categories = CallCategory::all();
        if($request->start){
            $data_table = $data_table->whereDate('created_at','>=',$request->start);
            if($request->end){
            $data_table = $data_table->whereDate('created_at','<=',$request->end);
            }
        }
        if($request->status)
        {
            $data_table = $data_table->where('call_category_id', $request->status);
        }
        $data_table = $data_table->where('sales_id',Auth::user()->id)->paginate(100);

        foreach ($data_table as $userData ) {
            $user_tnx = Transaction::where('user_id',$userData->user_id)->where('status','success')->latest('updated_at')->get();

            if($user_tnx->count() == 0){
                $userData->last_transaction_date = 'No Transactions';
            } else {
                $userData->last_transaction_date =  $user_tnx->first()->updated_at->format('d M Y, h:ia');
            }
        }
        return view(
            'admin.business_developer.users',
            compact([
                'data_table','segment','call_categories','type','salesCategory'
            ])
        );
    }



    public function UserProfile(Request $request){
        $users = User::orderBy('id','desc');
        if($request->start){
            $users = $users->whereDate('created_at','>=',$request->start);
            if($request->end){
                $users = $users->whereDate('created_at','<=',$request->end);
            }
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
        foreach($users as $userData){
            if($userData->user->phone == NULL) {
                $userData->update([
                    'Current_Cycle' => 'incipientUser',
                    'Previous_Cycle' => "QuarterlyInactive",
                    'current_cycle_count_date' => now()
                ]);
            }
        }
        return back()->with(['success'=>'IncipientUser Generated']);
    }



    public static function checkActiveUsers(){
        // checking if active users has made any trade
        $active_users = UserTracking::where('Current_Cycle','Active')->with('transactions','user')->get();

        foreach ($active_users as $au) {
            $user_tnx = $au->transactions;
            // various conditions for active users
            // 1. joined the system recently 
            // 2. last trade is less than two months 
            if($user_tnx->count() == 0){
                // Checking when user was created and the difference in months
                $MonthDiff = $au->user->created_at->diffInMonths(now());
                if($MonthDiff >= 3) {
                    //if the user since he joined has not traded make that user Quarterly inactive

                    UserTracking::find($au->id)->update([
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Active",
                        'current_cycle_count_date' => now()
                    ]);
                }
            } else {
                // if the user has traded check his last trade and if it is more than three months
                $lastTranxDate = $user_tnx->sortByDesc('created_at')->first()->created_at;
                 $monthDiff = $lastTranxDate->diffInMonths(now());
                 if($monthDiff >= 3) {
                    UserTracking::find($au->id)->update([
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Active",
                        'current_cycle_count_date' => now()
                    ]);
                 }
            }
        }
    }

    public static function checkQuarterlyInactive(){
        $quarterlyInactive = UserTracking::where('Current_Cycle','QuarterlyInactive')->with('transactions','user')->get();

        foreach($quarterlyInactive as $qiUser){
            // checking if the last transaction is less than three months since you entered a quarterly inactive
            $qiUserTransactions = $qiUser['transactions']
            ->where('created_at','>=',$qiUser->current_cycle_count_date)
            ->sortByDesc('id')->first();

            // count of the transactions
            if($qiUserTransactions){
                $lastQiUserTransaction = $qiUserTransactions->created_at;
                // time difference in months
                $timeFrame = $lastQiUserTransaction->diffInMonths(now());

                if($timeFrame < 3){
                    // if the last transaction was less than three months 
                    // this is done incase the user traded the before being called by the sales personnel

                    UserTracking::find($qiUser->id)->update([
                        'Current_Cycle'=>"Active",
                        'Previous_Cycle' => "QuarterlyInactive",
                        'current_cycle_count_date' => now()
                    ]);
            }
            }
            
        }
    }

    public static function checkCalledUsersForRespondedAndRecalcitrant() {
        // searching through called users to know if it's responded or recalcitrant
        $called_users = UserTracking::where('Current_Cycle','Called')->with('transactions','user')->get();
        foreach ($called_users as $cu ) {
            //cu refers to called users 
            $cu->called_date = Carbon::parse($cu->called_date);// parse that data as a timestamp
            $userTransactions = $cu['transactions']->where('created_at','>=',$cu->called_date);// checking for transactions that was done after the user was called

            if($userTransactions->count() >= 1){
                // getting the first transaction that was done after the user was called
                $firstConversionTransactionDate= $userTransactions->sortBy('updated_at')->first()->updated_at; 

                UserTracking::find($cu->id)->update([
                    'Current_Cycle'=>"Responded",
                    'Previous_Cycle' => "Called",
                    'current_cycle_count_date' => $firstConversionTransactionDate,
                ]);

            } else {
                // checking the month difference from when the user was called
                $monthDiff = $cu->called_date->diffInMonths(now());
                if($monthDiff >= 1){

                    UserTracking::find($cu->id)->update([
                        'Current_Cycle'=>"Recalcitrant",
                        'Previous_Cycle' => "Called",
                        'current_cycle_count_date' => now()
                    ]);

                }
            }
        }
    }


    public static function CheckRecalcitrantUsersForResponded(){
        // searching user that are responded 
        $recalcitrant_users = UserTracking::where('Current_Cycle','Recalcitrant')->with('transactions','user')->get();
        
        foreach ($recalcitrant_users as $ru) {
            $ru->current_cycle_count_date = Carbon::parse($ru->current_cycle_count_date);// parse that data as a timestamp

            $userTransactions = $ru['transactions']->where('created_at','>=',$ru->current_cycle_count_date);// checking for transactions that was done after the user became recalcitrant
            if($userTransactions->count() > 0){
                // checking transaction after user became recalcitrant
                $firstConversionTransaction = $userTransactions->sortBy('updated_at')->first()->updated_at; // getting the first transaction after the user became recalcitrant

                UserTracking::find($ru->id)->update([
                    'Current_Cycle'=>"Responded",
                    'Previous_Cycle' => "Recalcitrant",
                    'current_cycle_count_date' => $firstConversionTransaction,
                ]);

            }else{
                // if the transaction count is less than 1 after two months then mark the user as recalcitrant
                $monthDiff = $ru->current_cycle_count_date->diffInMonths(now()); 
                if($monthDiff >= 2){

                    UserTracking::find($ru->id)->update([
                        'Recalcitrant_Cycle' => $ru->Recalcitrant_Cycle + 1,
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Recalcitrant",
                        'current_cycle_count_date' => now(),
                        'Recalcitrant_streak' => $ru->Recalcitrant_streak + 1,
                        'Responded_streak' => 0,
                    ]);
                }
            }
        }
    }

    public static function CheckRespondedUsersForQualityInactive(){
        // responded users 
        $responded_users = UserTracking::where('Current_Cycle','Responded')->with('transactions','user')->get();
        foreach ($responded_users as $ru) {
            $ru->current_cycle_count_date = Carbon::parse($ru->current_cycle_count_date);
            $userTransactions = $ru['transactions']->where('created_at','>=',$ru->current_cycle_count_date);

            $monthDiff = $ru->current_cycle_count_date->diffInMonths(now());
            if($userTransactions->count() == 0){
                if($monthDiff >= 3){

                    UserTracking::find($ru->id)->update([
                        'Responded_Cycle' => $ru->Responded_Cycle + 1,
                        'Current_Cycle'=>"QuarterlyInactive",
                        'Previous_Cycle' => "Responded", 
                        'call_log_id' => null,
                        'current_cycle_count_date' => now(),
                        'Responded_streak' => $ru->Responded_streak + 1,
                        'Recalcitrant_streak' => 0,
                    ]);
                }
            }
            else{
                // getting the last transaction date
                $lastTranxDate = $userTransactions->first()->updated_at;
                if($lastTranxDate){
                    
                    UserTracking::find($ru->id)->update([
                        'current_cycle_count_date' => $lastTranxDate
                    ]);
                }

            }
        }
    }

    public static function QuarterlyInactiveFromOldUsersDB()
    {
        // UserTracking::truncate();
        // CallLog::truncate();
        $all_users = User::with('transactions')->where('role',1)->latest('created_at')->get();
        foreach ($all_users as $user) {
            $userTracking = UserTracking::where('user_id',$user->id)->count();
            if($userTracking == 0){
                if($user['transactions']->count() == 0) {
                    $diff_in_months = $user->created_at->diffInMonths(now());

                    if($diff_in_months >=3){
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $user->id;
                        $user_tracking->Current_Cycle = "QuarterlyInactive";
                        $user_tracking->current_cycle_count_date = now();
                        $user_tracking->save();

                    } else {
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $user->id;
                        $user_tracking->Current_Cycle = "Active";
                        $user_tracking->current_cycle_count_date = now();
                        $user_tracking->save();
                    }
                } else {
                    $last_user_transaction_date = $user->transactions()->latest('updated_at')->first()->updated_at;
                    $diff_in_months = $last_user_transaction_date->diffInMonths(now());

                    if($diff_in_months >=3){
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $user->id;
                        $user_tracking->Current_Cycle = "QuarterlyInactive";
                        $user_tracking->current_cycle_count_date = now();
                        $user_tracking->save();

                    } else {
                        $user_tracking = new UserTracking();
                        $user_tracking->user_id = $user->id;
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
