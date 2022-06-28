<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalesTimestamp;
use App\TargetSettings;
use App\User;
use Carbon\Carbon;

class TargetController extends Controller
{
    public function loadSales(){
        $user = User::whereIn('role',[557,556])->get();
        return view('admin.target',compact(['user']));
    }

    public function addTarget(Request $request)
    {
        $target = TargetSettings::create([
            'user_id'=>$request->id,
            'target' =>$request->number,
        ]);
        if($target){
            return redirect()->back()->with(['success' => 'Target Added']);
        }
        return redirect()->back()->with(['error' => 'Error Adding Target']);
    }

    public function editTarget(Request $request)
    {
        $target = TargetSettings::where('user_id',$request->id)
        ->update([
            'target' => $request->number
        ]);

        if($target){
            return redirect()->back()->with(['success' => 'Target Updated']);
        }
        return redirect()->back()->with(['error' => 'Error Updating Target']);
    }

    public function activateSales($id, $action)
    {
        $user = User::find($id);
        $user->status = $action;
        $user->save();

        if($action == 'active')
        {
            SalesTimestamp::create([
                'user_id' => $id,
                'activeTime' => now()
            ]);
        }
        else{
            $sales_data = SalesTimestamp::where('user_id',$id)->whereNull('inactiveTime')->orderBy('id','DESC')->first();
            if($sales_data){
                $activeTime = $sales_data->activeTime;
                $duration = Carbon::parse($activeTime)->diffInMinutes(now());
                if($duration < 5){
                    $sales_data->delete();
                }
                else{
                    $sales_data->update([
                        'inactiveTime' => now()
                    ]);
                }
            }
        }

        return redirect()->back()->with(['success' => 'Status Changed']);
        
    }
}
