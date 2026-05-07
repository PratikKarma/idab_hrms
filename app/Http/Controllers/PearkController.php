<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Peark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PearkController extends Controller
{
    public function pearkcreate($id){
        $employee = Employee::find($id);

        return view('peark.create',compact('employee'));
    }

    public function store(Request $request){
        
        $peark = new Peark;
        $peark->employee_id = $request->employee_id;
        $peark->title = $request->title;
        $peark->amount= $request->amount;
        $peark->peark_coupon  = $request->peark_coupon;
        $peark->created_by = Auth::user()->creatorId();
        $peark->save();

        return redirect()->back()->with('success','Peark  Successfuly created');
    }

    public function edit($peark)
    {
        $peark = Peark::where('id',$peark)->first();
        
        return view('peark.edit', compact('peark'));
    }

    public function update(Request $request, Peark $peark){
        $peark->title = $request->title;
        $peark->amount= $request->amount;
        $peark->peark_coupon  = $request->peark_coupon;
        $peark->created_by = Auth::user()->creatorId();
        $peark->save();

        return redirect()->back()->with('success','Peark Successfuly updated');
    }

    public function destroy(Peark $peark){
        $peark->delete();
        return redirect()->back()->with('success', __('Peark successfully deleted.'));
    }
}
