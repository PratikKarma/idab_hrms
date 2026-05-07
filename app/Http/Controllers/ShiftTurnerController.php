<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Shift;
use App\Models\ShiftTurner;
use App\Models\SubDepartment;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShiftTurnerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->can('Create Shift') || $user->can('Manage Shift')) {
            $employees = Employee::with('shift', 'upcomingShift')->where('created_by', Auth::user()->creatorId())->get();

            $department = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All', '');

            $sub_department = SubDepartment::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $sub_department->prepend('All', '');

            $turner_employee = ShiftTurner::select('employee')->where('created_by', Auth::user()->creatorId())->get();
            $emp_array_ser = $turner_employee->toArray();

            $check_emp_list = unserialize(!empty($emp_array_ser) && isset($emp_array_ser[0]['employee']) ? $emp_array_ser[0]['employee'] : '');

            $shifts = Shift::where('created_by', Auth::user()->creatorId())->get();

            return view('shiftturner.index', compact('employees', 'department', 'sub_department', 'check_emp_list', 'shifts'));

        } else {
            $employee = Employee::where('user_id', Auth::user()->id)
                ->with('shift', 'upcomingShift')
                ->first();

            return view('shiftturner.my_shifts', compact('employee'));
        }
    }

    public function getSubDepartmentandEmployee(Request $request)
    {
        $subdepartment = SubDepartment::where(['department' => $request->department_id, 'created_by' => Auth::user()->creatorId()])->get();
        $employee = Employee::with('shift', 'upcomingShift')->where(['department_id' => $request->department_id, 'created_by' => Auth::user()->creatorId()])->get();
        $subdepartment_output = '';
        foreach ($subdepartment as $sub) {
            $subdepartment_output .= '<option value="' . $sub->id . '">' . $sub->name . '</option>';
        }

        $turner_employee = ShiftTurner::select('employee')->where('created_by', Auth::user()->creatorId())->first();
        $turner_employee_arr = unserialize($turner_employee->employee);

        $shifts = Shift::where('created_by', Auth::user()->creatorId())->get();

        $employee_output = '';

        foreach ($employee as $emp) {
            if (in_array($emp->id, $turner_employee_arr)) {
                $check = 'checked';
            } else {
                $check = '';
            }

            $shift_options = '';
            foreach ($shifts as $shift) {
                $selected = ($emp->upcoming_shift_id == $shift->id) ? 'selected' : '';
                $shift_options .= '<option value="' . $shift->id . '" ' . $selected . '>' . $shift->name . '</option>';
            }

            $employee_output .= '<tr>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="employee_id[]" ' . $check . ' id="checkbox' . $emp->id . '" value="' . $emp->id . '">
                                        </div>
                                    </td>
                                    <td>' . $emp->name . '</td>
                                    <td>' . $emp?->shift?->name . '</td>
                                    <td>' . $emp?->upcomingShift?->name . '</td>';

            if (auth()->user()->can('Edit Shift')) {
                $employee_output .= '<td> <select class="form-select shift-change" data-employee-id="' . $emp->id . '">' . $shift_options . '</select></td>';
            }
            $employee_output .= '</tr>';
        }

        $final_output = array('subdepartment_output' => $subdepartment_output, 'employee_output' => $employee_output);
        print_r(json_encode($final_output));
    }

    public function getEmployeeList(Request $request)
    {

        $employee = Employee::with('shift', 'upcomingShift')->where(['subdepartment_id' => $request->subdepartment_id, 'created_by' => Auth::user()->creatorId()])->get();

        $turner_employee = ShiftTurner::select('employee')->where('created_by', Auth::user()->creatorId())->first();
        $turner_employee_arr = unserialize($turner_employee->employee);

        $shifts = Shift::where('created_by', Auth::user()->creatorId())->get();

        $employee_list = '';
        foreach ($employee as $emp) {
            if (in_array($emp->id, $turner_employee_arr)) {
                $check = 'checked';
            } else {
                $check = '';
            }

            $shift_options = '';
            foreach ($shifts as $shift) {
                $selected = ($emp->upcoming_shift_id == $shift->id) ? 'selected' : '';
                $shift_options .= '<option value="' . $shift->id . '" ' . $selected . '>' . $shift->name . '</option>';
            }

            $employee_list .= '<tr>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="employee_id[]" ' . $check . ' id="checkbox' . $emp->id . '" value="' . $emp->id . '">
                                        </div>
                                    </td>
                                    <td>' . $emp->name . '</td>
                                    <td>' . $emp?->shift?->name . '</td>
                                    <td>' . $emp?->upcomingShift?->name . '</td>';

            if (auth()->user()->can('Edit Shift')) {
                $employee_list .= '<td><select class="form-select shift-change" data-employee-id="' . $emp->id . '">' . $shift_options . '</select> </td>';
            }

            $employee_list .= '</tr>';
        }
        print_r($employee_list);
    }

    public function store(Request $request)
    {
        $employee = serialize($request->employee_id);

        $shift_turner = ShiftTurner::where('created_by', Auth::user()->creatorId())->first();

        if ($shift_turner) {
            $shift_turner->employee = $employee;
        } else {
            $shift_turner = new ShiftTurner();
            $shift_turner->employee = $employee;
            $shift_turner->created_by = Auth::user()->creatorId();
            $shift_turner->last_shifted_at = now();
        }

        $shift_turner->save();

        return redirect()->back()->with('success', 'Shift turner updated for selected employees');
    }

    public function updateShift(Request $request, Employee $employee)
    {
        if (!auth()->user()->can('Edit Shift')) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to edit shift.']);
        }

        $request->validate(['shift_id' => 'required|exists:shifts,id']);

        $oldShiftId = $employee->shift_id;

        try {
            $employee->upcoming_shift_id = $request->shift_id;
            $employee->save();

            return response()->json(['success' => true, 'message' => 'Shift updated successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shift. Please try again later.',
                'oldShiftId' => $oldShiftId
            ]);
        }
    }

    public function autoShiftRotationByCreator($creatorId)
    {
        $shiftTurners = ShiftTurner::where('created_by', $creatorId)->get();

        foreach ($shiftTurners as $turner) {
            $selectedEmployees = unserialize($turner->employee);
            if (empty($selectedEmployees))
                continue;

            $shifts = Shift::where('created_by', $turner->created_by)->pluck('id')->toArray();
            if (empty($shifts))
                continue;

            $settings = Utility::settings($turner->created_by);
            $shiftFrequencyDays = (int) ($settings['shift_turner'] ?? 7);

            $lastShifted = $turner->last_shifted_at ?? now()->subDays($shiftFrequencyDays + 1);
            $now = now();

            if (Carbon::parse($lastShifted)->diffInDays($now) >= $shiftFrequencyDays) {
                foreach ($selectedEmployees as $empId) {
                    $employee = Employee::find($empId);
                    if (!$employee)
                        continue;

                    $currentShift = $employee->shift_id;
                    $upcomingShift = $employee->upcoming_shift_id;

                    if (!$upcomingShift || $upcomingShift == $currentShift) {
                        $availableShifts = array_filter($shifts, fn($s) => $s != $currentShift);
                        $upcomingShift = $availableShifts[array_rand($availableShifts)];
                    }

                    $employee->shift_id = $upcomingShift;
                    $nextAvailableShifts = array_filter($shifts, fn($s) => $s != $upcomingShift);
                    $employee->upcoming_shift_id = $nextAvailableShifts[array_rand($nextAvailableShifts)];
                    $employee->save();
                }

                $turner->last_shifted_at = now();
                $turner->save();
            }
        }

        // Log::info("Shift rotated for creator: " . $creatorId);
        return response()->json(['message' => 'Auto shift rotation applied where needed.']);
    }
}