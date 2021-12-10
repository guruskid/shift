<?php

namespace App\Http\Controllers\ApiV2;

use App\ChatMessages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ticket;
use App\TicketCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function createTicket(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'description' => 'required',
            'subcategory_id' => 'required|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $ticket = Ticket::create([
            'ticketNo' => time(),
            'user_id' => Auth::user()->id,
            'description' => $r->description,
            'status' => 'open',
            'agent_id' => null,
            'subcategory_id' => $r->subcategory_id,
        ]);
        //* getting subcategory and parent category.

        $category = $this->getCategory($r->subcategory_id);
        $subcartegory = $this->getSubCategory($r->subcategory_id);

        $message = "Category: " . $category . "\n" .
            "SubCategory: " . $subcartegory . "\n" .
            "Description: " . $r->description;

            ChatMessages::create([
            'ticket_no' => $ticket->ticketNo,
            'user_id' => Auth::user()->id,
            'message' => $message,
            'is_agent' => 0,
        ]);

        if (empty($ticket)) {
            return response()->json([
                "success" => false,
                "message" => "Error creating a ticket",
            ], 500);
        }

        return response()->json([
            "success" => true,
            "ticketNumber" => $ticket->ticketNo,
            "chatmessage" => $message
        ], 200);
    }

    public function closeTicketList()
    {
        $ticketList = Ticket::where('user_id', '=', Auth::user()->id)->where('status', 'close')->latest()->get();

        return response()->json([
            "success" => true,
            'ticketlist' => $ticketList,
        ], 200);
    }

    public function openTicketList()
    {
        $ticketList = Ticket::where('user_id', '=', Auth::user()->id)->where('status', 'open')->latest()->get();
        return response()->json([
            "success" => true,
            'ticketlist' => $ticketList,
        ], 200);
    }

    public function getCategory($subcartegory_id)
    {
        $category_id = TicketCategory::find($subcartegory_id)->ticket_category_id;
        return TicketCategory::find($category_id)->name;
    }


    public function getSubCategory($subcartegory_id)
    {
        return TicketCategory::find($subcartegory_id)->name;
    }
}
