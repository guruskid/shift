<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\Engage;

class testController extends Controller
{
    public function engage()
    {
        $engage = new \Engage\EngageClient($_SERVER['01ce2904a334879cddb2ae7e62d019a3'], $_SERVER['4595ad59cdc1a7df2af3e4a06ce1a4da']);
        $rd = $engage->users->identify([
            'id' => '$user->id',
            'email' => '$user->email',
            'created_at' => '$user->created_at'
          ]);

          dd($rd);
    }
}
