<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PayBridgeTransactions implements FromView
{
    public function __construct($collection)
    {
        $this->collection = $collection;
    }
    public function view(): View
    {
        return view('admin.export_PayBridgeTransactions',[
            'transactions' => $this->collection,
        ]);
    }
}
