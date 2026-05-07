<?php

namespace App\Http\Controllers;

use App\Models\Wage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WageController extends Controller
{
    protected $data;
    public function index(){

        $this->data['wages'] = Wage::where('created_by', '=', Auth::user()->creatorId())->get();
        return view('wages.index',$this->data);
    }

    public function create(){
        $wagesType = Wage::$Wages_type;
        return view('wages.create',compact('wagesType'));
    }

    public function save(Request $request){

        $wages             = new Wage();
        $wages->wages_type = $request->wages_type;
        $wages->minimum    = $request->minimum;
        $wages->da         = $request->da;
        $wages->created_by = Auth::user()->creatorId();
        $wages->save();

        return redirect()->route('wages.index')->with('success', __('Wages successfully created.'));
    }

    public function edit($id){
        $wages = Wage::where('id',$id)->first();
        $wagesType = Wage::$Wages_type;
        return view('wages.edit',compact('wages','wagesType'));
    }

    public function update(Request $request){

        $wages             = Wage::where('id',$request->id)->first();
        $wages->wages_type = $request->wages_type;
        $wages->minimum    = $request->minimum;
        $wages->da         = $request->da;
        $wages->created_by = Auth::user()->creatorId();
        $wages->save();

        return redirect()->route('wages.index')->with('success', __('Wages successfully updated.'));
    }

    public function delete($id){

        Wage::where('id',$id)->delete();
        return redirect()->back()->with('success', __('Wages successfully deleted.'));
    }
}
