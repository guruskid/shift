<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UtilityTransaction;

class UtilityTransactions extends Controller
{
    public function index(Request $request)
    {
        $transactions['transactions'] = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc');
        $type = UtilityTransaction::select('type')->distinct('type')->get();
        $status = UtilityTransaction::select('status')->distinct('status')->get();
        $data = $request->validate([
            'start' => 'date|string',
            'end' => 'date|string',
        ]);  
  
        if (!empty($data)) {
            $transactions['transactions'] = $transactions['transactions']
            ->where('created_at', '>=', $data['start'])
            ->where('created_at', '<=', $data['end']);

            if ($request->type != 'null') {
                $transactions['transactions'] = $transactions['transactions']
                ->where('type','=',$request->type);
            }
            if ($request->status != 'null') {
                $transactions['transactions'] = $transactions['transactions']
                ->where('status','=',$request->status);
            }

            $total = $transactions['transactions']->sum('amount');

        }
        $total = $transactions['transactions']->sum('amount');
        
        $transactions['transactions'] = $transactions['transactions']->paginate(200);
        return view('admin.utility-transactions',$transactions,compact(['type','status','total']));
    }
}
