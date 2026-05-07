<?php

namespace App\Http\Controllers;

use App\Models\Bonous;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BonousController extends Controller
{
    public function bonouscreate($id)
    {
        $employee = Employee::find($id);

        return view('bonous.create', compact('employee'));
    }

    public function store(Request $request)
    {

        $bonous = new Bonous;
        $bonous->employee_id = $request->employee_id;
        $bonous->title = $request->title;
        $bonous->amount = $request->amount;
        $bonous->created_by = Auth::user()->creatorId();
        $bonous->save();

        return redirect()->back()->with('success', 'Bonous Successfuly created');

    }

    public function edit($bonous)
    {
        $bonous = Bonous::where('id', $bonous)->first();

        return view('bonous.edit', compact('bonous'));
    }


    public function update(Request $request)
    {
        $bonous = Bonous::firstwhere('id', $request->id);

        $bonous->id = $request->id;
        $bonous->title = $request->title;
        $bonous->amount = $request->amount;
        $bonous->save();

        return redirect()->back()->with('success', 'Bonous Successfuly updated');
    }

    public function destroy($id)
    {
        Bonous::where('id', $id)->delete();
        return redirect()->back()->with('success', __('Bonous successfully deleted.'));
    }
}
