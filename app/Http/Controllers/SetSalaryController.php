<?php

namespace App\Http\Controllers;

use App\Models\AccountList;
use App\Models\Allowance;
use App\Models\AllowanceOption;
use App\Models\Bonous;
use App\Models\Commission;
use App\Models\DeductionOption;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanOption;
use App\Models\OtherPayment;
use App\Models\Overtime;
use App\Models\PayslipType;
use App\Models\Peark;
use App\Models\Pension;
use App\Models\SaturationDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class SetSalaryController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Set Salary')) {
            $employees = Employee::where(
                [
                    'created_by' => Auth::user()->creatorId(),
                ]
            )->get();

            return view('setsalary.index', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('Edit Set Salary')) {

            $payslip_type = PayslipType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $allowance_options = AllowanceOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $loan_options = LoanOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $deduction_options = DeductionOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            if (Auth::user()->type == 'employee') {
                $currentEmployee = Employee::where('user_id', '=', Auth::user()->id)->first();
                $allowances = Allowance::where('employee_id', $currentEmployee->id)->get();
                $commissions = Commission::where('employee_id', $currentEmployee->id)->get();
                $loans = Loan::where('employee_id', $currentEmployee->id)->get();
                $saturationdeductions = SaturationDeduction::where('employee_id', $currentEmployee->id)->get();
                $otherpayments = OtherPayment::where('employee_id', $currentEmployee->id)->get();
                $overtimes = Overtime::where('employee_id', $currentEmployee->id)->get();
                $employee = Employee::where('user_id', '=', Auth::user()->id)->first();

                return view('setsalary.employee_salary', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances'));
            } else {
                $allowances = Allowance::where('employee_id', $id)->get();
                $commissions = Commission::where('employee_id', $id)->get();
                $loans = Loan::where('employee_id', $id)->get();
                $saturationdeductions = SaturationDeduction::where('employee_id', $id)->get();
                $otherpayments = OtherPayment::where('employee_id', $id)->get();
                $overtimes = Overtime::where('employee_id', $id)->get();
                $employee = Employee::find($id);

                return view('setsalary.edit', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $payslip_type = PayslipType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $allowance_options = AllowanceOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $loan_options = LoanOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $deduction_options = DeductionOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        if (Auth::user()->type == 'employee') {
            $employee = Employee::where('user_id', auth()->id())->first();

            if (!$employee) {
                abort(404, 'Employee not found');
            }

            $employeeSalary = $employee->salary;

            $allowances = Allowance::where('employee_id', $employee->id)->get();
            $commissions = Commission::where('employee_id', $employee->id)->get();
            $loans = Loan::where('employee_id', $employee->id)->get();
            $saturationdeductions = SaturationDeduction::where('employee_id', $employee->id)->get();
            $otherpayments = OtherPayment::where('employee_id', $employee->id)->get();
            $overtimes = Overtime::where('employee_id', $employee->id)->get();
            $pension = Pension::where('employee_id', $employee->id)->get();
            $peark = Peark::where('employee_id', $employee->id)->get();
            $bonous = Bonous::where('employee_id', $employee->id)->get();

            foreach ([$allowances, $commissions, $loans, $saturationdeductions, $otherpayments] as $collection) {
                foreach ($collection as $value) {
                    if ($value->type === 'percentage') {
                        $value->tota_allow = ($value->amount * $employeeSalary) / 100;
                    } else {
                        $value->tota_allow = $value->amount;
                    }
                }
            }

            $com = 0;
            $bon = 0;
            $over = 0;
            $allowance = 0;

            foreach ($commissions as $item) {
                $com += $item->tota_allow ?? $item->amount;
            }

            foreach ($bonous as $item) {
                $bon += $item->amount;
            }

            foreach ($overtimes as $item) {
                $over += $item->rate;
            }

            foreach ($allowances as $item) {
                $allowance += $item->tota_allow ?? $item->amount;
            }

            $gross_salary = $employeeSalary + $com + $bon + $over + $allowance;

            return view('setsalary.employee_salary', compact('employee', 'payslip_type', 'peark', 'bonous', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances', 'pension', 'gross_salary'));
        } else {
            $allowances = Allowance::where('employee_id', $id)->get();
            $commissions = Commission::where('employee_id', $id)->get();
            $loans = Loan::where('employee_id', $id)->get();
            $saturationdeductions = SaturationDeduction::where('employee_id', $id)->get();
            $otherpayments = OtherPayment::where('employee_id', $id)->get();
            $pension = Pension::where('employee_id', $id)->get();
            $peark = Peark::where('employee_id', $id)->get();
            $bonous = Bonous::where('employee_id', $id)->get();
            $overtimes = Overtime::where('employee_id', $id)->get();
            $employee = Employee::find($id);

            $empsal = 0;

            foreach ($allowances as $value) {
                if ($value->type == 'percentage') {
                    $employee = Employee::find($value->employee_id);
                    $empsal = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($commissions as $value) {
                if ($value->type == 'percentage') {
                    $employee = Employee::find($value->employee_id);
                    $empsal = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($loans as $value) {
                if ($value->type == 'percentage') {
                    $employee = Employee::find($value->employee_id);
                    $empsal = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($saturationdeductions as $value) {
                if ($value->type == 'percentage') {
                    $employee = Employee::find($value->employee_id);
                    $empsal = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($otherpayments as $value) {
                if ($value->type == 'percentage') {
                    $employee = Employee::find($value->employee_id);
                    $empsal = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            //for gross salary

            $gross_salary = 0;

            foreach ($allowances as $item) {

                if ($item->type == 'percentage') {
                    $allow = ($item->amount * $employee->salary) / 100;
                    $gross_salary = $gross_salary + $allow;
                } else {
                    $gross_salary = $gross_salary + $item->amount;
                }
            }

            foreach ($commissions as $item) {

                if ($item->type == 'percentage') {
                    $comm = ($item->amount * $employee->salary) / 100;
                    $gross_salary = $gross_salary + $comm;
                } else {
                    $gross_salary = $gross_salary + $item->amount;
                }
            }

            foreach ($bonous as $item) {

                if ($item->type == 'percentage') {
                    $bonous = ($item->amount * $employee->salary) / 100;
                    $gross_salary = $gross_salary + $bonous;
                } else {
                    $gross_salary = $gross_salary + $item->amount;
                }
            }

            foreach ($overtimes as $item) {

                if ($item->type == 'percentage') {
                    $over = ($item->rate * $employee->salary) / 100;
                    $gross_salary = $gross_salary + $over;
                } else {
                    $gross_salary = $gross_salary + $item->rate;
                }
            }

            $gross_salary = $gross_salary + $employee->salary;

            return view('setsalary.employee_salary', compact('employee', 'payslip_type', 'peark', 'bonous', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances', 'pension', 'gross_salary'));
        }
    }


    public function employeeUpdateSalary(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'salary_type' => 'required',
                'salary' => 'required',
                'from_account_type' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $employee = Employee::findOrFail($id);
        $input = $request->all();
        $employee->fill($input);
        $employee->account_type = $input['from_account_type'];
        $employee->save();

        return redirect()->back()->with('success', 'Employee Salary Updated.');
    }

    public function employeeSalary()
    {
        if (Auth::user()->type == "employee") {
            $employees = Employee::where('user_id', Auth::user()->id)->get();
            return view('setsalary.index', compact('employees'));
        }
    }

    public function employeeBasicSalary($id)
    {
        $payslip_type = PayslipType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $payslip_type->prepend('Select Payslip Type', '');

        $from_account_type = AccountList::where('created_by', Auth::user()->creatorId())->get()->pluck('account_name', 'id');
        $from_account_type->prepend('Select Account Type', '');

        $employee = Employee::find($id);

        return view('setsalary.basic_salary', compact('employee', 'payslip_type', 'from_account_type'));
    }
}
