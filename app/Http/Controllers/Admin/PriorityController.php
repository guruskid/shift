<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PriorityRanking;
use Illuminate\Validation\ValidationException;

class PriorityController extends Controller
{
    public function index()
    {
        $priority = PriorityRanking::orderBy('priority_price','ASC')->get();
        return view('admin.priority',compact(['priority']));
    }

    public function createPriorityData(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|alpha',
                'price' => 'required|numeric',
            ]);
            
            PriorityRanking::create([
                'priority_name' => $request->name,
                'priority_price' => $request->price,
            ]);

            return redirect()->back()->with(['success' => 'Successfully created']);
        }catch(ValidationException $th){
            return redirect()->back()->with(['error' => $th->validator->errors()->first()]);
        }catch(\Throwable $e){
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function editPriority(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|alpha',
                'price' => 'required|numeric',
            ]);
            
            PriorityRanking::where('id', $request->id)->update([
                'priority_name' => $request->name,
                'priority_price' => $request->price,
            ]);

            return redirect()->back()->with(['success' => 'Updated Successfully']);
        }catch(ValidationException $th){
            return redirect()->back()->with(['error' => $th->validator->errors()->first()]);
        }catch(\Throwable $e){
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function deletePriority($id)
    {
        try{
            PriorityRanking::where('id', $id)->delete();
            return redirect()->back()->with(['success' => 'Deleted Successfully']);
        }catch(\Throwable $e){
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        
    }
}
