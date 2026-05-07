<?php

namespace App\Http\Controllers;

use App\Exports\accountstatementExport;
use App\Exports\LeaveExport;
use App\Exports\LeaveReportExport;
use App\Exports\PayrollExport;
use App\Exports\TimesheetExport;
use App\Exports\TimesheetReportExport;
use App\Models\AccountList;
use App\Models\AttendanceEmployee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\PaySlip;
use App\Models\TimeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;

class ReportController extends Controller
{

    public function incomeVsExpense(Request $request)
    {

        if (\Auth::user()->can('Manage Report')) {
            $deposit = Deposit::where('created_by', \Auth::user()->creatorId());

            $labels       = $data = [];
            $expenseCount = $incomeCount = 0;
            $incomeData = [];
            $expenseData = [];
            if (!empty($request->start_month) && !empty($request->end_month)) {

                $start = strtotime($request->start_month);
                $end   = strtotime($request->end_month);

                $currentdate = $start;
                $month       = [];
                while ($currentdate <= $end) {
                    $month = date('m', $currentdate);
                    $year  = date('Y', $currentdate);

                    $depositFilter = Deposit::where('created_by', \Auth::user()->creatorId())->whereMonth('date', $month)->whereYear('date', $year)->get();

                    $depositsTotal = 0;
                    foreach ($depositFilter as $deposit) {
                        $depositsTotal += $deposit->amount;
                    }

                    $incomeData[] = $depositsTotal;
                    $incomeCount  += $depositsTotal;

                    $expenseFilter = Expense::where('created_by', \Auth::user()->creatorId())->whereMonth('date', $month)->whereYear('date', $year)->get();
                    $expenseTotal  = 0;
                    foreach ($expenseFilter as $expense) {
                        $expenseTotal += $expense->amount;
                    }
                    $expenseData[] = $expenseTotal;
                    $expenseCount  += $expenseTotal;

                    $labels[]    = date('M Y', $currentdate);
                    $currentdate = strtotime('+1 month', $currentdate);
                }

                $filter['startDateRange'] = date('M-Y', strtotime($request->start_month));
                $filter['endDateRange']   = date('M-Y', strtotime($request->end_month));
            } else {
                for ($i = 0; $i < 6; $i++) {

                    $month = date('m', strtotime("-$i month"));
                    $year  = date('Y', strtotime("-$i month"));

                    $depositFilter = Deposit::where('created_by', \Auth::user()->creatorId())->whereMonth('date', $month)->whereYear('date', $year)->get();

                    $depositTotal = 0;
                    foreach ($depositFilter as $deposit) {
                        $depositTotal += $deposit->amount;
                    }

                    $incomeData[] = $depositTotal;
                    $incomeCount  += $depositTotal;

                    $expenseFilter = Expense::where('created_by', \Auth::user()->creatorId())->whereMonth('date', $month)->whereYear('date', $year)->get();
                    $expenseTotal  = 0;
                    foreach ($expenseFilter as $expense) {
                        $expenseTotal += $expense->amount;
                    }
                    $expenseData[] = $expenseTotal;
                    $expenseCount  += $expenseTotal;

                    $labels[] = date('M Y', strtotime("-$i month"));
                }
                $filter['startDateRange'] = date('M-Y');
                $filter['endDateRange']   = date('M-Y', strtotime("-5 month"));
            }

            $incomeArr['name'] = __('Income');
            $incomeArr['data'] = $incomeData;

            $expenseArr['name'] = __('Expense');
            $expenseArr['data'] = $expenseData;

            $data[] = $incomeArr;
            $data[] = $expenseArr;



            return view('report.income_expense', compact('labels', 'data', 'incomeCount', 'expenseCount', 'filter'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function leave(Request $request)
    {
        if (\Auth::user()->can('Manage Report')) {

            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('All', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All', '');

            $filterYear['branch']        = __('All');
            $filterYear['department']    = __('All');
            $filterYear['type']          = __('Monthly');
            $filterYear['dateYearRange'] = date('M-Y');
            $employees                   = Employee::where('created_by', \Auth::user()->creatorId());
            if (!empty($request->branch)) {
                $employees->where('branch_id', $request->branch);
                $filterYear['branch'] = !empty(Branch::find($request->branch)) ? Branch::find($request->branch)->name : '';
            }
            if (!empty($request->department)) {
                $employees->where('department_id', $request->department);
                $filterYear['department'] = !empty(Department::find($request->department)) ? Department::find($request->department)->name : '';
            }

            $employees = $employees->get();

            $leaves        = [];
            $totalApproved = $totalReject = $totalPending = 0;
            foreach ($employees as $employee) {

                $employeeLeave['id']          = $employee->id;
                $employeeLeave['employee_id'] = $employee->employee_id;
                $employeeLeave['employee']    = $employee->name;

                $approved = Leave::where('employee_id', $employee->id)->where('status', 'Approved');
                $reject   = Leave::where('employee_id', $employee->id)->where('status', 'Reject');
                $pending  = Leave::where('employee_id', $employee->id)->where('status', 'Pending');

                if ($request->type == 'monthly' && !empty($request->month)) {
                    $month = date('m', strtotime($request->month));
                    $year  = date('Y', strtotime($request->month));

                    $approved->whereMonth('applied_on', $month)->whereYear('applied_on', $year);
                    $reject->whereMonth('applied_on', $month)->whereYear('applied_on', $year);
                    $pending->whereMonth('applied_on', $month)->whereYear('applied_on', $year);

                    $filterYear['dateYearRange'] = date('M-Y', strtotime($request->month));
                    $filterYear['type']          = __('Monthly');
                } elseif (!isset($request->type)) {
                    $month     = date('m');
                    $year      = date('Y');
                    $monthYear = date('Y-m');

                    $approved->whereMonth('applied_on', $month)->whereYear('applied_on', $year);
                    $reject->whereMonth('applied_on', $month)->whereYear('applied_on', $year);
                    $pending->whereMonth('applied_on', $month)->whereYear('applied_on', $year);

                    $filterYear['dateYearRange'] = date('M-Y', strtotime($monthYear));
                    $filterYear['type']          = __('Monthly');
                }

                if ($request->type == 'yearly' && !empty($request->year)) {
                    $approved->whereYear('applied_on', $request->year);
                    $reject->whereYear('applied_on', $request->year);
                    $pending->whereYear('applied_on', $request->year);


                    $filterYear['dateYearRange'] = $request->year;
                    $filterYear['type']          = __('Yearly');
                }

                $approved = $approved->count();
                $reject   = $reject->count();
                $pending  = $pending->count();

                $totalApproved += $approved;
                $totalReject   += $reject;
                $totalPending  += $pending;

                $employeeLeave['approved'] = $approved;
                $employeeLeave['reject']   = $reject;
                $employeeLeave['pending']  = $pending;


                $leaves[] = $employeeLeave;
            }

            $starting_year = date('Y', strtotime('-5 year'));
            $ending_year   = date('Y', strtotime('+5 year'));

            $filterYear['starting_year'] = $starting_year;
            $filterYear['ending_year']   = $ending_year;

            $filter['totalApproved'] = $totalApproved;
            $filter['totalReject']   = $totalReject;
            $filter['totalPending']  = $totalPending;

            return view('report.leave', compact('department', 'branch', 'leaves', 'filterYear', 'filter'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function employeeLeave(Request $request, $employee_id, $status, $type, $month, $year)
    {
        if (\Auth::user()->can('Manage Report')) {
            $leaveTypes = LeaveType::where('created_by', \Auth::user()->creatorId())->get();
            $leaves     = [];
            foreach ($leaveTypes as $leaveType) {
                $leave        = new Leave();
                $leave->title = $leaveType->title;
                $totalLeave   = Leave::where('employee_id', $employee_id)->where('status', $status)->where('leave_type_id', $leaveType->id);
                if ($type == 'yearly') {
                    $totalLeave->whereYear('applied_on', $year);
                } else {
                    $m = date('m', strtotime($month));
                    $y = date('Y', strtotime($month));

                    $totalLeave->whereMonth('applied_on', $m)->whereYear('applied_on', $y);
                }
                $totalLeave = $totalLeave->count();

                $leave->total = $totalLeave;
                $leaves[]     = $leave;
            }

            $leaveData = Leave::where('employee_id', $employee_id)->where('status', $status);
            if ($type == 'yearly') {
                $leaveData->whereYear('applied_on', $year);
            } else {
                $m = date('m', strtotime($month));
                $y = date('Y', strtotime($month));

                $leaveData->whereMonth('applied_on', $m)->whereYear('applied_on', $y);
            }

            $leaveData = $leaveData->get();


            return view('report.leaveShow', compact('leaves', 'leaveData'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function accountStatement(Request $request)
    {
        if (\Auth::user()->can('Manage Report')) {
            $accountList = AccountList::where('created_by', \Auth::user()->creatorId())->get()->pluck('account_name', 'id');
            $accountList->prepend('All', '');

            $filterYear['account'] = __('All');
            $filterYear['type']    = __('Income');


            if ($request->type == 'expense') {
                $accountData = Expense::orderBy('id');
                $accounts    = Expense::select('account_lists.id', 'account_lists.account_name')->leftjoin('account_lists', 'expenses.account_id', '=', 'account_lists.id')->groupBy('expenses.account_id')->selectRaw('sum(amount) as total');

                if (!empty($request->start_month) && !empty($request->end_month)) {
                    $start = strtotime($request->start_month);
                    $end   = strtotime($request->end_month);
                } else {
                    $start = strtotime(date('Y-m'));
                    $end   = strtotime(date('Y-m', strtotime("-5 month")));
                }

                $currentdate = $start;

                while ($currentdate <= $end) {
                    $data['month'] = date('m', $currentdate);
                    $data['year']  = date('Y', $currentdate);

                    $accountData->Orwhere(
                        function ($query) use ($data) {
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );

                    $accounts->Orwhere(
                        function ($query) use ($data) {
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );

                    $currentdate = strtotime('+1 month', $currentdate);
                }

                $filterYear['startDateRange'] = date('M-Y', $start);
                $filterYear['endDateRange']   = date('M-Y', $end);

                if (!empty($request->account)) {
                    $accountData->where('account_id', $request->account);
                    $accounts->where('account_lists.id', $request->account);

                    $filterYear['account'] = !empty(AccountList::find($request->account)) ? Department::find($request->account)->account_name : '';
                }

                $accounts->where('expenses.created_by', \Auth::user()->creatorId());

                $filterYear['type'] = __('Expense');
            } else {
                $accountData = Deposit::orderBy('id');
                $accounts    = Deposit::select('account_lists.id', 'account_lists.account_name')->leftjoin('account_lists', 'deposits.account_id', '=', 'account_lists.id')->groupBy('deposits.account_id')->selectRaw('sum(amount) as total');

                if (!empty($request->start_month) && !empty($request->end_month)) {

                    $start = strtotime($request->start_month);
                    $end   = strtotime($request->end_month);
                } else {
                    $start = strtotime(date('Y-m'));
                    $end   = strtotime(date('Y-m', strtotime("-5 month")));
                }

                $currentdate = $start;

                while ($currentdate <= $end) {
                    $data['month'] = date('m', $currentdate);
                    $data['year']  = date('Y', $currentdate);

                    $accountData->Orwhere(
                        function ($query) use ($data) {
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );
                    $currentdate = strtotime('+1 month', $currentdate);

                    $accounts->Orwhere(
                        function ($query) use ($data) {
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );
                    $currentdate = strtotime('+1 month', $currentdate);
                }

                $filterYear['startDateRange'] = date('M-Y', $start);
                $filterYear['endDateRange']   = date('M-Y', $end);

                if (!empty($request->account)) {
                    $accountData->where('account_id', $request->account);
                    $accounts->where('account_lists.id', $request->account);

                    $filterYear['account'] = !empty(AccountList::find($request->account)) ? Department::find($request->account)->account_name : '';
                }
                $accounts->where('deposits.created_by', \Auth::user()->creatorId());
            }

            $accountData->where('created_by', \Auth::user()->creatorId());
            $accountData = $accountData->get();

            $accounts = $accounts->get();


            return view('report.account_statement', compact('accountData', 'accountList', 'accounts', 'filterYear'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payroll(Request $request)
    {
        if (\Auth::user()->can('Manage Report')) {
            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('All', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All', '');

            $filterYear['branch']     = __('All');
            $filterYear['department'] = __('All');
            $filterYear['type']       = __('Monthly');

            $payslips = PaySlip::select('pay_slips.*', 'employees.name')->leftjoin('employees', 'pay_slips.employee_id', '=', 'employees.id')->where('pay_slips.created_by', \Auth::user()->creatorId());


            if ($request->type == 'monthly' && !empty($request->month)) {

                $payslips->where('salary_month', $request->month);

                $filterYear['dateYearRange'] = date('M-Y', strtotime($request->month));
                $filterYear['type']          = __('Monthly');
            } elseif (!isset($request->type)) {
                $month = date('Y-m');

                $payslips->where('salary_month', $month);

                $filterYear['dateYearRange'] = date('M-Y', strtotime($month));
                $filterYear['type']          = __('Monthly');
            }

            if ($request->type == 'yearly' && !empty($request->year)) {
                $startMonth = $request->year . '-01';
                $endMonth   = $request->year . '-12';
                $payslips->where('salary_month', '>=', $startMonth)->where('salary_month', '<=', $endMonth);

                $filterYear['dateYearRange'] = $request->year;
                $filterYear['type']          = __('Yearly');
            }

            if (!empty($request->branch)) {
                $payslips->where('employees.branch_id', $request->branch);

                $filterYear['branch'] = !empty(Branch::find($request->branch)) ? Branch::find($request->branch)->name : '';
            }

            if (!empty($request->department)) {
                $payslips->where('employees.department_id', $request->department);

                $filterYear['department'] = !empty(Department::find($request->department)) ? Department::find($request->department)->name : '';
            }

            $payslips = $payslips->get();

            $totalBasicSalary = $totalNetSalary = $totalAllowance = $totalCommision = $totalLoan = $totalSaturationDeduction = $totalOtherPayment = $totalOverTime = 0;

            foreach ($payslips as $payslip) {
                $totalBasicSalary += $payslip->basic_salary;
                $totalNetSalary   += $payslip->net_payble;

                $allowances = json_decode($payslip->allowance);
                foreach ($allowances as $allowance) {
                    $totalAllowance += $allowance->amount;
                }

                $commisions = json_decode($payslip->commission);
                foreach ($commisions as $commision) {
                    $totalCommision += $commision->amount;
                }

                $loans = json_decode($payslip->loan);
                foreach ($loans as $loan) {
                    $totalLoan += $loan->amount;
                }

                $saturationDeductions = json_decode($payslip->saturation_deduction);
                foreach ($saturationDeductions as $saturationDeduction) {
                    $totalSaturationDeduction += $saturationDeduction->amount;
                }

                $otherPayments = json_decode($payslip->other_payment);
                foreach ($otherPayments as $otherPayment) {
                    $totalOtherPayment += $otherPayment->amount;
                }

                $overtimes = json_decode($payslip->overtime);
                foreach ($overtimes as $overtime) {
                    $days  = $overtime->number_of_days;
                    $hours = $overtime->hours;
                    $rate  = $overtime->rate;

                    $totalOverTime += ($rate * $hours) * $days;
                }
            }

            $filterData['totalBasicSalary']         = $totalBasicSalary;
            $filterData['totalNetSalary']           = $totalNetSalary;
            $filterData['totalAllowance']           = $totalAllowance;
            $filterData['totalCommision']           = $totalCommision;
            $filterData['totalLoan']                = $totalLoan;
            $filterData['totalSaturationDeduction'] = $totalSaturationDeduction;
            $filterData['totalOtherPayment']        = $totalOtherPayment;
            $filterData['totalOverTime']            = $totalOverTime;


            $starting_year = date('Y', strtotime('-5 year'));
            $ending_year   = date('Y', strtotime('+5 year'));

            $filterYear['starting_year'] = $starting_year;
            $filterYear['ending_year']   = $ending_year;

            return view('report.payroll', compact('payslips', 'filterData', 'branch', 'department', 'filterYear'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function monthlyAttendance(Request $request)
    {
        if (\Auth::user()->can('Manage Report')) {

            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('All', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All', '');

            $data['branch']     = __('All');
            $data['department'] = __('All');

            $employees = Employee::select('id', 'name');
            if (!empty($request->employee_id) && $request->employee_id[0] != 0) {
                $employees->whereIn('id', $request->employee_id);
            }

            $employees = $employees->where('created_by', \Auth::user()->creatorId());

            if (!empty($request->branch_id)) {
                $employees->where('branch_id', $request->branch_id);
                $data['branch'] = !empty(Branch::find($request->branch_id)) ? Branch::find($request->branch_id)->name : '';
            }

            if (!empty($request->department)) {
                $employees->where('department_id', $request->department);
                $data['department'] = !empty(Department::find($request->department)) ? Department::find($request->department)->name : '';
            }

            if (!empty($request->employees)) {
                $employees->where('employee_id', $request->employees);
                $data['employees'] = !empty(Employee::find($request->employees)) ? Employee::find($request->employees)->name : '';
            }

            $employees = $employees->get()->pluck('name', 'id');

            if (!empty($request->month)) {
                $currentdate = strtotime($request->month);
                $month       = date('m', $currentdate);
                $year        = date('Y', $currentdate);
                $curMonth    = date('M-Y', strtotime($request->month));
            } else {
                $month    = date('m');
                $year     = date('Y');
                $curMonth = date('M-Y', strtotime($year . '-' . $month));
            }


            //            $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $num_of_days = date('t', mktime(0, 0, 0, $month, 1, $year));
            $dates = [];
            for ($i = 1; $i <= $num_of_days; $i++) {
                $dates[] = str_pad($i, 2, '0', STR_PAD_LEFT);
            }

            $employeesAttendance = [];
            $totalPresent        = $totalLeave = 0;
            $ovetimeHours        = $overtimeMins = $earlyleaveHours = $earlyleaveMins = $lateHours = $lateMins = 0;

            $tz = config('app.timezone') ?: 'UTC';

            foreach ($employees as $id => $employee) {
                $attendances['name'] = $employee;
                $totalPresents = 0;
                $attendanceStatus = [];
                $totalWorkedSeconds = 0;

                foreach ($dates as $date) {
                    $dateFormat = $year . '-' . $month . '-' . $date;

                    if ($dateFormat <= date('Y-m-d')) {
                        $attendance = AttendanceEmployee::where('employee_id', $id)
                            ->where('date', $dateFormat)
                            ->first();

                        if (!empty($attendance)) {

                            // Calculate total worked hours for the day
                            $dayWorkedSeconds = 0;
                            if (!empty($attendance->clock_in) && $attendance->clock_in !== '00:00:00') {
                                $in = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date . ' ' . $attendance->clock_in, $tz);

                                if (!empty($attendance->clock_out) && $attendance->clock_out !== '00:00:00') {
                                    $out = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date . ' ' . $attendance->clock_out, $tz);
                                    if ($out->lessThan($in)) {
                                        $out = $out->addDay();
                                    }
                                } else {
                                    // If clock_out missing, consider now if today's date
                                    if ($attendance->date === Carbon::now($tz)->toDateString()) {
                                        $out = Carbon::now($tz);
                                    } else {
                                        $out = null;
                                    }
                                }

                                if ($out) {
                                    $dayWorkedSeconds = $in->diffInSeconds($out);

                                    if (!empty($attendance->total_break) && $attendance->total_break !== '00:00:00') {
                                        [$bH, $bM, $bS] = array_map('intval', explode(':', $attendance->total_break));
                                        $breakSeconds = $bH * 3600 + $bM * 60 + $bS;
                                        $dayWorkedSeconds -= $breakSeconds;
                                    }
                                    $dayWorkedSeconds = max(0, $dayWorkedSeconds);
                                }
                            }

                            $totalWorkedSeconds += $dayWorkedSeconds;

                            // Status
                            if (in_array($attendance->status, ['present', 'Present'])) {
                                $attendanceStatus[$date] = 'P';
                                $totalPresent++;
                                $totalPresents++;

                                // Overtime, late, early leave
                                if ($attendance->overtime > 0) {
                                    $ovetimeHours += date('h', strtotime($attendance->overtime));
                                    $overtimeMins += date('i', strtotime($attendance->overtime));
                                }
                                if ($attendance->early_leaving > 0) {
                                    $earlyleaveHours += date('h', strtotime($attendance->early_leaving));
                                    $earlyleaveMins  += date('i', strtotime($attendance->early_leaving));
                                }
                                if ($attendance->late > 0) {
                                    $lateHours += date('h', strtotime($attendance->late));
                                    $lateMins  += date('i', strtotime($attendance->late));
                                }
                            } else {
                                $attendanceStatus[$date] = 'A';
                                $totalLeave++;
                            }
                        } else {
                            $attendanceStatus[$date] = 'A';
                            $totalLeave++;
                        }
                    } else {
                        $attendanceStatus[$date] = '';
                    }
                }

                $attendances['status'] = $attendanceStatus;
                $attendances['total_present'] = $totalPresents;
                $attendances['total_hours'] = gmdate('H:i:s', $totalWorkedSeconds); // convert seconds to H:i:s

                $employeesAttendance[] = $attendances;
            }

            $totalOverTime   = $ovetimeHours + ($overtimeMins / 60);
            $totalEarlyleave = $earlyleaveHours + ($earlyleaveMins / 60);
            $totalLate       = $lateHours + ($lateMins / 60);

            $data['totalOvertime']   = $totalOverTime;
            $data['totalEarlyLeave'] = $totalEarlyleave;
            $data['totalLate']       = $totalLate;
            $data['totalPresent']    = $totalPresent;
            $data['totalLeave']      = $totalLeave;
            $data['curMonth']        = $curMonth;

            // dd($employeesAttendance, $branch, $department, $employees, $dates, $data);
            return view('report.monthlyAttendance', compact('employeesAttendance', 'branch', 'department', 'employees', 'dates', 'data'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function timesheet(Request $request)
    {
        if (\Auth::user()->can('Manage Report')) {
            $branch = Branch::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
            $branch->prepend('All', '');

            $department = Department::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
            $department->prepend('All', '');

            $filterYear['branch']     = __('All');
            $filterYear['department'] = __('All');

            /**
             * Base Query
             */
            $attendances = AttendanceEmployee::select(
                'attendance_employees.*',
                'employees.name as employee_name',
                'employees.shift_id'
            )
                ->leftJoin('employees', 'attendance_employees.employee_id', '=', 'employees.id')
                ->where('employees.created_by', Auth::user()->creatorId());


            /**
             * Date Filter
             */
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $attendances->whereBetween('attendance_employees.date', [
                    $request->start_date,
                    $request->end_date
                ]);

                $filterYear['start_date'] = $request->start_date;
                $filterYear['end_date']   = $request->end_date;
            } else {
                $filterYear['start_date'] = date('Y-m-01');
                $filterYear['end_date']   = date('Y-m-t');

                $attendances->whereBetween('attendance_employees.date', [
                    $filterYear['start_date'],
                    $filterYear['end_date']
                ]);
            }

            /**
             * Branch / Department Filters
             */
            if (!empty($request->branch)) {
                $attendances->where('attendance_employees.branch_id', $request->branch);
                $filterYear['branch'] = optional(Branch::find($request->branch))->name;
            }

            if (!empty($request->department)) {
                $attendances->where('attendance_employees.department_id', $request->department);
                $filterYear['department'] = optional(Department::find($request->department))->name;
            }

            $attendances = $attendances->get();

            /**
             * Result containers
             */
            $timesheets = [];
            $timesheetFilters = [];

            /**
             * Helper for formatting minutes
             */
            $formatMinutes = function (int $minutes): string {
                $h = intdiv($minutes, 60);
                $m = $minutes % 60;
                return "{$h} hours {$m} minutes";
            };

            // Pre-load all shifts to avoid N+1 queries
            $shifts = Shift::all()->keyBy('id');
            foreach ($attendances as $attendance) {

                // Skip if no valid clock in
                if (empty($attendance->clock_in) || $attendance->clock_in === '00:00:00') {
                    continue;
                }

                // CLOCK IN
                $clockIn = Carbon::parse($attendance->date . ' ' . $attendance->clock_in);

                // CLOCK OUT
                $clockOutTime = $attendance->clock_out;

                // If clock_out is 00:00:00 or empty, get shift end time
                if (empty($clockOutTime) || $clockOutTime === '00:00:00') {
                    // Try to get shift end time
                    if (!empty($attendance->shift_id) && isset($shifts[$attendance->shift_id])) {
                        $shift = $shifts[$attendance->shift_id];
                        if (!empty($shift->company_end_time) && $shift->company_end_time !== '00:00:00') {
                            $clockOutTime = $shift->company_end_time;
                        }
                    }

                    // If still no clock out time, use 8 hours default
                    if (empty($clockOutTime) || $clockOutTime === '00:00:00') {
                        $clockOut = clone $clockIn;
                        $clockOut->addHours(8);
                    } else {
                        $clockOut = Carbon::parse($attendance->date . ' ' . $clockOutTime);

                        // Handle night shift
                        if ($clockOut->lte($clockIn)) {
                            $clockOut->addDay();
                        }
                    }
                } else {
                    $clockOut = Carbon::parse($attendance->date . ' ' . $clockOutTime);

                    // Handle night shift
                    if ($clockOut->lte($clockIn)) {
                        $clockOut->addDay();
                    }
                }

                // TOTAL DURATION (MINUTES)
                $totalMinutes = $clockIn->diffInMinutes($clockOut);

                // BREAK TIME (HH:MM:SS)
                $breakMinutes = 0;
                if (!empty($attendance->total_break) && $attendance->total_break !== '00:00:00') {
                    $breakParts = explode(':', $attendance->total_break);
                    if (count($breakParts) >= 2) {
                        $bh = (int)$breakParts[0];
                        $bm = (int)$breakParts[1];
                        $breakMinutes = ($bh * 60) + $bm;
                    }
                }

                // WORKING TIME
                $workingMinutes = max(0, $totalMinutes - $breakMinutes);
                $workingHours   = round($workingMinutes / 60, 2);

                // DESCRIPTION
                $description = "Today's timesheet "
                    . $clockIn->format('h:i A') . " "
                    . $clockOut->format('h:i A')
                    . " : Total " . $formatMinutes($totalMinutes)
                    . " - Total Break time : " . $formatMinutes($breakMinutes)
                    . " = total working hours : " . $formatMinutes($workingMinutes);

                /** -----------------------------
                 * Table data
                 * ----------------------------- */
                $timesheets[] = (object)[
                    'employee_id' => $attendance->employee_id,
                    'employee'    => (object)[
                        'name' => $attendance->employee_name,
                    ],
                    'date'        => $attendance->date,
                    'hours'       => $workingHours,
                    'remark'      => $description,
                ];

                /** -----------------------------
                 * Employee summary cards
                 * ----------------------------- */
                if (!isset($timesheetFilters[$attendance->employee_id])) {
                    $timesheetFilters[$attendance->employee_id] = (object)[
                        'employee_id' => $attendance->employee_id,
                        'employee'    => (object)[
                            'name' => $attendance->employee_name,
                        ],
                        'total'       => 0,
                    ];
                }

                $timesheetFilters[$attendance->employee_id]->total += $workingHours;
            }

            /**
             * Final totals
             */
            $timesheetFilters = collect($timesheetFilters);

            $filterYear['totalEmployee'] = $timesheetFilters->count();
            $filterYear['totalHours']    = round($timesheetFilters->sum('total'), 2);

            return view('report.timesheet', compact(
                'timesheets',
                'branch',
                'department',
                'filterYear',
                'timesheetFilters'
            ));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function LeaveReportExport()
    {
        $name = 'leave_' . date('Y-m-d i:h:s');
        $data = \Excel::download(new LeaveReportExport(), $name . '.xlsx');

        return $data;
    }

    public function AccountStatementReportExport(Request $request)
    {
        $name = 'Account Statement_' . date('Y-m-d i:h:s');
        $data = \Excel::download(new accountstatementExport(), $name . '.xlsx');

        return $data;
    }

    public function PayrollReportExport($month, $branch, $department)
    {
        $data = [];
        $data['branch'] = __('All');
        $data['department'] = __('All');

        if ($branch != 0) {
            $data['branch'] = !empty(Branch::find($branch)) ? Branch::find($branch)->id : '';
        }

        if ($department != 0) {
            $data['department'] = !empty(Department::find($department)) ? Department::find($department)->id : '';
        }
        $data['month'] = $month;
        $name = 'Payroll_' . date('Y-m-d i:h:s');
        $data = \Excel::download(new PayrollExport($data), $name . '.xlsx');

        return $data;
    }

    public function exportTimeshhetReport(Request $request)
    {
        $name = 'Timesheet_' . date('Y-m-d i:h:s');
        $data = \Excel::download(new TimesheetReportExport(), $name . '.xlsx');

        return $data;
    }

    public function exportCsv($filter_month, $branch, $department, $employee)
    {
        $data['branch'] = __('All');
        $data['department'] = __('All');

        $employees = Employee::select('id', 'name')->where('created_by', \Auth::user()->creatorId());
        if ($branch != 0) {
            $employees->where('branch_id', $branch);
            $data['branch'] = !empty(Branch::find($branch)) ? Branch::find($branch)->name : '';
        }

        if ($department != 0) {
            $employees->where('department_id', $department);
            $data['department'] = !empty(Department::find($department)) ? Department::find($department)->name : '';
        }
        if ($employee != 0) {
            $employeeIds = explode(',', $employee);
            $emp = Employee::whereIn('id', $employeeIds);
        } else {
            $emp = Employee::where('created_by', \Auth::user()->creatorId());
        }

        $employees = $emp->get()->pluck('name', 'id');

        $currentdate = strtotime($filter_month);
        $month       = date('m', $currentdate);
        $year        = date('Y', $currentdate);
        $data['curMonth']    = date('M-Y', strtotime($filter_month));


        $fileName = $data['branch'] . ' ' . __('Branch') . ' ' . $data['curMonth'] . ' ' . __('Attendance Report of') . ' ' . $data['department'] . ' ' . __('Department') . ' ' . '.csv';

        $employeesAttendance = [];
        $num_of_days = date('t', mktime(0, 0, 0, $month, 1, $year));
        for ($i = 1; $i <= $num_of_days; $i++) {
            $dates[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        foreach ($employees as $id => $employee) {
            $attendances['name'] = $employee;

            foreach ($dates as $date) {
                $dateFormat = $year . '-' . $month . '-' . $date;

                if ($dateFormat <= date('Y-m-d')) {
                    $employeeAttendance = AttendanceEmployee::where('employee_id', $id)->where('date', $dateFormat)->first();

                    if (!empty($employeeAttendance) && $employeeAttendance->status == 'Present') {
                        $attendanceStatus[$date] = 'P';
                    } elseif (!empty($employeeAttendance) && $employeeAttendance->status == 'Leave') {
                        $attendanceStatus[$date] = 'A';
                    } else {
                        $attendanceStatus[$date] = '-';
                    }
                } else {
                    $attendanceStatus[$date] = '-';
                }
                $attendances[$date] = $attendanceStatus[$date];
            }

            $employeesAttendance[] = $attendances;
        }

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $emp = array(
            'employee',
        );

        $columns = array_merge($emp, $dates);

        $callback = function () use ($employeesAttendance, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($employeesAttendance as $attendance) {
                fputcsv($file, str_replace('"', '', array_values($attendance)));
            }


            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getdepartment(Request $request)
    {
        if ($request->branch_id == 0) {
            $departments = Department::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $departments = Department::where('created_by', '=', Auth::user()->creatorId())->where('branch_id', $request->branch_id)->get()->pluck('name', 'id')->toArray();
        }
        return response()->json($departments);
    }

    public function getemployee(Request $request)
    {
        if (!$request->department_id) {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->where('department_id', $request->department_id)->get()->pluck('name', 'id')->toArray();
        }

        return response()->json($employees);
    }
}
