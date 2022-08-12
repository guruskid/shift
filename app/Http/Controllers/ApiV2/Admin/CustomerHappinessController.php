<?php

namespace App\Http\Controllers\ApiV2\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\Ticket;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

class CustomerHappinessController extends Controller
{
    public function overview()
    {
        $customerHappinessUser = User::select('id','first_name','last_name','email','phone','status')->where('role',555)->get();

        if($customerHappinessUser->count() <= 0)
        {
            return response()->json([
                'success' => true,
                'customerHappiness' => "Not Available",
                'chart' => "Not Available",
                'querySummary' => "Not Available",
            ]);
        }

        $activeCH = $customerHappinessUser->where('status','active');

        $chartData = $this->chartData(now(), $activeCH->first()->id);

        $querySummary1 = Ticket::with('user')->where('agent_id',$activeCH->first()->id)->Where('closed_by',$activeCH->first()->id)->get();
        $querySummary2 = Ticket::with('user')->where('agent_id',$activeCH->first()->id)->Where('closed_by',null)->get();

        $querySummary = collect()->concat($querySummary1)->concat($querySummary2);
        $queryData = array();

        foreach($querySummary as $value)
        {
            $queryData[] = [
                'name' =>$value->user->first_name." ".$value->user->last_name,
                'username' => $value->user->username,
                'signUpDate' => $value->user->created_at->format('Y/m/d H:i:s a'),
                'queryStatus' => $value->status,
                'QueryContent' => $value->description,
                'queryDate' => $value->created_at->format('Y/m/d H:i:s a'),
            ];
        }
        return response()->json([
            'success' => true,
            'chart' => $chartData,
            'querySummary' => collect($queryData)->sortByDesc('created_at')
        ]);
    }

    public function CustomerHappinessData()
    {
        $customerHappinessUser = User::select('id','first_name','last_name','email','phone','status')->where('role',555)->get();
        $active = User::where('status','active')->select('id','first_name','last_name','email','phone','status')->where('role',555)->get();
        $inactive = User::where('status','!=','active')->select('id','first_name','last_name','email','phone','status')->where('role',555)->get();
        
        if($active->count() <= 0 || $inactive->count() <= 0)
        {
            return response()->json([
                'success' => true,
                'active' => "Not Available",
                'inactive' => "Not Available",
            ]);
        }
        $ticketData = Ticket::all();

        $assigned_ticket = $ticketData->count();
        $closed_ticket = $ticketData->where('status','close')->count();

        $generalCloseRate = ($closed_ticket/$assigned_ticket)*100;

        foreach ($customerHappinessUser as $key => $value) {
            $assigned_ticket = $ticketData->where('agent_id',$value->id)->count();
            $closed_ticket = $ticketData->where('closed_by',$value->id)->where('status','close')->count();

            $AgentCloseRate = ($assigned_ticket == 0) ? 0 : ($closed_ticket/$assigned_ticket)*100;
            $value->agentAgentRate = round($AgentCloseRate,2);
            $value->generalCloseRate = round($generalCloseRate,2);
        }
    
        return response()->json([
            'success' => true,
            'active' => $active,
            'inactive' => $inactive
        ]);
    }

    public function chartData($date, $id)
    {
        //* Weekly 
        $startWeek = Carbon::parse($date)->subDays(7)->addHour()->format('Y-m-d');
        $endWeek = Carbon::parse($date)->format('Y-m-d');

        $weekly = [
            'General' => $this->dailyChart($startWeek, $endWeek),
            'Agent' => $this->dailyChart($startWeek, $endWeek, $id),
        ];

        //* Monthly
        $startMonthly = Carbon::parse($date)->subMonth()->addHour()->format('Y-m-d');
        $endMonthly = Carbon::parse($date)->format('Y-m-d');

        $monthly = [
            'General' => $this->dailyChart($startMonthly, $endMonthly),
            'Agent' => $this->dailyChart($startMonthly, $endMonthly, $id),
        ];

        //*Yearly
        $monthsBack = 11;
        $yearly = [
            'General' => $this->monthlyChart($monthsBack, $date),
            'Agent' => $this->monthlyChart($monthsBack, $date, $id),
        ];

        $exportData = array(
            'weekly' => $weekly,
            'monthly' => $monthly,
            'yearly' => $yearly,
        );

        return $exportData;
    }

    public function dailyChart($startDate, $endDate, $id = null)
    {
        /**
         * 
         * @param date $startDate
         * 
         * @param date $endDate
         * 
         * @param integer $usd_value
         * 
         * @return array $exportData
         * 
         */

        $ticket = Ticket::all();
        $loopCounter = Carbon::parse($startDate)->addHour()->diffInDays(Carbon::parse($endDate)->addHour());

        // return $durationTranx;
        $exportData = array();

        for($i = 0; $i <= $loopCounter; $i ++)
        {
            //*Total Time 
            $day = Carbon::parse($startDate)->addHour()->addDays($i)->format('Y-m-d');
            if($id == null){
                
                $opened_data = $ticket->where('status','open')->where('created_at','>=',"$day 00:00:00")->where('created_at','<=',"$day 23:59:59")->count();
                $closed_data = $ticket->where('status','close')->where('created_at','>=',"$day 00:00:00")->where('created_at','<=',"$day 23:59:59")->count();
            }else{
                $opened_data = $ticket->where('agent_id',$id)->where('status','open')->where('created_at','>=',"$day 00:00:00")->where('created_at','<=',"$day 23:59:59")->count();
                $closed_data = $ticket->where('closed_by',$id)->where('status','close')->where('created_at','>=',"$day 00:00:00")->where('created_at','<=',"$day 23:59:59")->count();
            }


            if ($loopCounter <= 7)
            {
                $exportData[] = array(
                    'opened' => $opened_data,
                    'closed' => $closed_data,
                    'date' => Carbon::parse($day)->addHour()->format("l")
                );

            }
            else{
                $exportData[] = array(
                    'opened' => $opened_data,
                    'closed' => $closed_data,
                    'date' => Carbon::parse($day)->addHour()->format("d F Y")
                );
            }
            
        }

        return $exportData;
    }

    public function monthlyChart($monthsBack, $time, $id = null)
    {
        /**
         * 
         * @param integer $monthsBack
         * 
         * @param date $time
         * 
         * @param integer $usd_value
         * 
         * @return array $exportData
         * 
         */
        $listOfMonths = array();
        do{
            $duration = Carbon::parse($time)->subMonths($monthsBack)->addHour();

            $startMonth = Carbon::parse($duration)->startOfMonth()->addHour();
            $endMonth = Carbon::parse($duration)->endOfMonth()->addHour();
            $listOfMonths[] = array(
                'start' => $startMonth,
                'end' => $endMonth,
            ); 
            $monthsBack -- ;
        }
        while($monthsBack >= 0);

        $endIndex = count($listOfMonths) - 1;

        $durationTranxCrypto = Ticket::where('created_at','>=',$listOfMonths[0]['start'])->where('created_at','<=',$listOfMonths[$endIndex]['end'])->get();
        $durationTranx = collect([])->concat($durationTranxCrypto);
        $exportData =  array();

        for($i = 0; $i <= $endIndex; $i ++)
        {
            if($id == null){
                
                $opened_data = $durationTranx->where('status','open')->where('created_at','>=',$listOfMonths[$i]['start'])->where('created_at','<=',$listOfMonths[$i]['end'])->count();
                $closed_data = $durationTranx->where('status','close')->where('created_at','>=',$listOfMonths[$i]['start'])->where('created_at','<=',$listOfMonths[$i]['end'])->count();
            }else{
                $opened_data = $durationTranx->where('agent_id',$id)->where('status','open')->where('created_at','>=',$listOfMonths[$i]['start'])->where('created_at','<=',$listOfMonths[$i]['end'])->count();
                $closed_data = $durationTranx->where('closed_by',$id)->where('status','close')->where('created_at','>=',$listOfMonths[$i]['start'])->where('created_at','<=',$listOfMonths[$i]['end'])->count();
            }

            $exportData[] = array(
                'opened' => $opened_data,
                'closed' => $closed_data,
                'date' => Carbon::parse($listOfMonths[$i]['start'])->addHour()->format("F Y")
            );
        }

        return $exportData; 


    }

    public function activateCustomerHappiness(Request $r)
    {
        $validator = Validator::make($r->all(),[
            'id' => 'required|integer',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $id = $r->id;
        $user = User::find($id);

        if(!in_array($user->role, [555] ))
        {
            return response()->json([
                'success' => false,
                'message' => "$user->first_name $user->last_name is not a customer happiness personnel"
            ],401);
        }

        if($user->status != 'active'):

            $user->status = 'active';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => "$user->first_name $user->last_name is activated"
            ],200);
        endif;

        return response()->json([
            'success' => true,
            'message' => "$user->first_name $user->last_name is already activated"
        ],200);
    }

    public function deactivateCustomerHappiness(Request $r)
    {

        $validator = Validator::make($r->all(),[
            'id' => 'required|integer',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $id = $r->id;
        $user = User::find($id);
        if(!in_array($user->role, [555] ))
        {
            return response()->json([
                'success' => false,
                'message' => "$user->first_name $user->last_name is not a customer happiness personnel"
            ],401);
        }

        if($user->status != 'waiting'):

            $user->status = 'waiting';
            $user->save();
            return response()->json([
                'success' => true,
                'message' => "$user->first_name $user->last_name is deactivated"
            ],200);
        endif;

        return response()->json([
            'success' => true,
            'message' => "$user->first_name $user->last_name is already deactivated"
        ],200);
    }
}
