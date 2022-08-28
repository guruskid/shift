<?php

namespace App\Http\Controllers\Admin;

use App\BlockfillOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlockfillOrderController extends Controller
{
    public function index()
    {
        $orders = BlockfillOrder::latest()->paginate(100);

        return view('admin.blockfill.orders', compact('orders'));
    }
}
