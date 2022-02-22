<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TicketCategory;
use Illuminate\Support\Facades\Validator;

class TicketCategoryController extends Controller
{
    public function addCategory(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $cat_id =  TicketCategory::where('name',$r->name)->count();
        if($cat_id > 0)
        {
            return response()->json([
                "success" => false,
                "status" => "Category Already Stored"
            ], 401);
        }
        TicketCategory::create([
            'name'=>$r->name,
            'ticket_category_id' => null
        ]);
        return response()->json([
            "success" => true,
            "message" => "Category created"
        ], 200);

    }
    public function addSubCategory(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'name' => 'required',
            'id' => 'required|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $cat_id =  TicketCategory::where('id',$r->id)->get();
        if(count($cat_id) == 0){
            return response()->json([
                "success" => false,
                'message' => "Category does not exist",
            ], 401);
        }
        $tc =TicketCategory::create([
            'name'=> $r->name,
            'ticket_category_id' => $cat_id[0]->id
        ]);
        return response()->json([
            "success" => true,
            "message" => "SubCategory created"
        ], 200);

    }

    public function listofCategories()
    {
        $ticketcategory = TicketCategory::with('subcategories')->where('ticket_category_id', null)->get();
        if(empty($ticketcategory))
        {
            return response()->json([
                "success" => false,
                "message" => "No Categories Available",
            ], 404);
        }
        return response()->json([
            "success" => true,
            "ticketcartegory" => $ticketcategory,
        ], 200);
    }
}
