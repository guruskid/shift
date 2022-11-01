<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class QuarterlyInactiveUsers implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function view(): View
    {
        return view('admin.export_quarterlyInactiveUsers',[
            'quarterlyInactive' => $this->collection,
        ]);
    }
}
