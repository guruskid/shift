<?php

namespace App\Http\Controllers\Admin;

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
    public function index() {
        $users = User::whereIn('role', [555])->latest()->get();
        return view('admin.customer_happiness.customer-happiness-agents', compact('users'));
    }

    public function addAgent(Request $r) {
        $user = User::find($r->id);
        $user->role = 555;
        $user->status = 'waiting';
        $user->save();
        return back()->with(['success' => 'Happiness Agent added successfully']);
    }

    public function action($id,$action) {
        $user = User::find($id);
        switch ($action) {
            case 'remove':
                $user->role = 1;
                $user->status = 'active';
                break;
            case 'active':
                $user->status = 'active';
                break;
            default:
                $user->status = 'waiting';
                break;
        }
        $user->save();
        return back()->with(['success' => 'Done!']);
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
