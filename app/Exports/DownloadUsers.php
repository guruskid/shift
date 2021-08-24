<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\shouldAutoSize;
use Illuminate\Contracts\View\View;


class DownloadUsers implements FromView, shouldAutoSize
{


    public function headings():array
    {
        return[
            'first_name',
            'last_name',
            'email',
            'phone',
            'created_at'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        // return collect(User::getUsers());
        // return User::all();
        return view('admin.export_users', [
            'users' => User::all()
        ] );
    }
}
