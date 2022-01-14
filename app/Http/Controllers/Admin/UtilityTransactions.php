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

        $data = $request->validate([
            'start' => 'date|string',
            'end' => 'date|string',
        ]);

        if (!empty($data)) {
            $transactions['transactions'] = $transactions['transactions']->where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end']);   
        }

        $transactions['transactions'] = $transactions['transactions']->paginate(200);
        return view('admin.utility-transactions',$transactions);
    }
}
