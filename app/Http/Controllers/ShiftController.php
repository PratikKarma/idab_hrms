<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function index()
    {


        $shifts = Shift::where('created_by', '=', Auth::user()->creatorId())->get();

        return view('shift.index', compact('shifts'));

    }

    public function create()
    {


        return view('shift.create');

    }

    public function store(Request $request)
    {


        $validator = Validator::make(
            $request->all(),
            [

                'name' => 'required|max:20',
                'company_start_time' => 'required',
                'company_end_time' => 'required'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $shift = new Shift();
        $shift->name = $request->name;
        $shift->company_start_time = $request->company_start_time;
        $shift->company_end_time = $request->company_end_time;
        $shift->created_by = Auth::user()->creatorId();

        $shift->save();

        return redirect()->route('shift.index')->with('success', __('Shift  successfully created.'));

    }

    public function show(Shift $shift)
    {
        return redirect()->route('shift.index');
    }

    public function edit(Shift $shift)
    {
        $departments = $shift->pluck('name', 'id');

            return view('shift.edit', compact('shift'));

    }

    public function update(Request $request, Shift $shift)
    {

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:20',
                    'company_start_time' => 'required',
                    'company_end_time' => 'required'
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $shift->name = $request->name;
            $shift->company_start_time = $request->company_start_time;
            $shift->company_end_time = $request->company_end_time;
            $shift->save();

            return redirect()->route('shift.index')->with('success', __('Shift  successfully updated.'));

    }

    public function destroy(Shift $shift)
    {

        if ($shift->created_by == Auth::user()->creatorId()) {
            $shift->delete();

            return redirect()->route('shift.index')->with('success', __('Shift successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }
}
