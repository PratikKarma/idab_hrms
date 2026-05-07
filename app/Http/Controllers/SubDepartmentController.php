<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\SubDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubDepartmentController extends Controller
{
    public function index()
    {
        $subdepartments = SubDepartment::select('sub_departments.id','sub_departments.name', 'sub_departments.department','departments.name as department')->leftjoin('departments','departments.id','=', 'sub_departments.department')->where('sub_departments.created_by', Auth::user()->creatorId())->get();
        return view('subdepartment.index', compact('subdepartments'));

    }
    public function create()
    {
        $department = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $branch = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        return view('subdepartment.create', compact('department','branch'));
    }
    public function store(Request $request){
        $request->validate([
            'department' => 'required',
            'name' => 'required',
        ]);

        $subdepartments = new SubDepartment();
        $subdepartments->department = $request->department;
        $subdepartments->name = $request->name;
        $subdepartments->created_by = Auth::user()->creatorId();
        $subdepartments->save();

        return redirect()->route('subdepartment')->with('success', __('SubDepartment  successfully created.'));
    }

     public function edit($id){
        $subdepartment = SubDepartment::where('id',$id)->first();
        $branch = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $department = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');

        return view('subdepartment.edit', compact('subdepartment','department','branch'));
    }

    public function delete($id){

        SubDepartment::where('id',$id)->delete();
        return redirect()->back()->with('success', __('Sub Department successfully deleted.'));
    }

    public function update(Request $request, SubDepartment $department)
    {

        $request->validate([
            'department' => 'required',
            'name' => 'required',
        ]);

        $subdepartments = SubDepartment::where(['id'=> $request->id,])->first();
        $subdepartments->department = $request->department;
        $subdepartments->name = $request->name;
        $subdepartments->save();

        return redirect()->route('subdepartment')->with('success', __('Sub Department successfully updated.'));
    }
}
