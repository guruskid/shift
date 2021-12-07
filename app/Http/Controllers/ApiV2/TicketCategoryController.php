<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TicketCategory;

class TicketCategoryController extends Controller
{
    public function listofCategories()  
    {
        $ticketcategory = TicketCategory::where('ticket_category_id', null)->get();
        if(empty($ticketcategory))
        {
            return response()->json([
                "status" => "Error",
                "message" => "No Categories Available",
            ], 404);
        }
        return response()->json([
            "status" => "Success",
            "ticketcartegory" => $ticketcategory,
        ], 200);
    }

    public function listofsubcategories($id)
    {
        $subcategory = TicketCategory::find($id)->subcategories;
        if(empty($subcategory))
        {
            return response()->json([
                "status" => "Error",
                "message" => "No Sub-Categories Available",
            ], 404);
        }
        return response()->json([
            "status" => "Success",
            "subcategory" => $subcategory,
        ], 200);
    }
}
