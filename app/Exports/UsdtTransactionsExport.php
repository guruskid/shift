<?php

namespace App\Exports;

use App\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class UsdtTransactionsExport implements FromView
{
    use Exportable;
    /**
    * @return \Illuminate\Support\query
    */

    public function __construct(int $card_id, $startDate, $endDate)
    {
        $this->card_id = $card_id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $transactions = Transaction::query()->with('user')->orderBY('id','DESC')->where('card_id', $this->card_id);
        if($this->startDate != null)
        {
            $transactions = $transactions->where('created_at','>=',$this->startDate." 00:00:00");
        }

        if($this->endDate != null)
        {
            $transactions = $transactions->where('created_at','<=',$this->endDate." 23:59:59");
        }

        return view('admin.export_usdtTransactions',[
            'transactions' => $transactions->get()
        ]);
    }
}
