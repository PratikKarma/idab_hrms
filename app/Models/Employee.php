<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Percentage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    protected $table = 'employees';
    protected $fillable = [
        'user_id',
        'name',
        'last_name',
        'dob',
        'gender',
        'phone',
        'address',
        'email',
        'password',
        'employee_id',
        'branch_id',
        'department_id',
        'subdepartment_id',
        'designation_id',
        'shift_id',
        'company_doj',
        'company_start_time',
        'company_end_time',
        'documents',
        'account_holder_name',
        'account_number',
        'bank_name',
        'bank_identifier_code',
        'branch_location',
        'lunch_break',
        'tax_payer_id',
        'pf_id',
        'esic_id',
        'salary_type',
        'account_type',
        'salary',
        'created_by',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function upcomingShift()
    {
        return $this->belongsTo(Shift::class, 'upcoming_shift_id');
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id', 'employee_id')->get();
    }

    public function salary_type()
    {
        return $this->hasOne(PayslipType::class, 'id', 'salary_type')->pluck('name')->first();
    }

    public function account_type()
    {
        return $this->hasOne(AccountList::class, 'id', 'account_type')->pluck('account_name')->first();
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class, 'employee_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'employee_id');
    }

    public function bonuses()
    {
        return $this->hasMany(Bonous::class, 'employee_id');
    }

    public function pearks()
    {
        return $this->hasMany(Peark::class, 'employee_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'employee_id');
    }

    public function saturationDeductions()
    {
        return $this->hasMany(SaturationDeduction::class, 'employee_id');
    }

    public function otherPayments()
    {
        return $this->hasMany(OtherPayment::class, 'employee_id');
    }

    public function pensions()
    {
        return $this->hasMany(Pension::class, 'employee_id');
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class, 'employee_id');
    }

    public function attendances()
    {
        return $this->hasMany(AttendanceEmployee::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }


    public function get_net_salary()
    {
        $employee = Employee::with([
            'allowances',
            'commissions',
            'bonuses',
            'pearks',
            'loans',
            'saturationDeductions',
            'otherPayments',
            'pensions',
            'overtimes'
        ])->find($this->id);

        $salary = $employee->salary;
        $per_day_amount = $salary / Carbon::now()->daysInMonth;

        $calcTotal = function ($items) use ($salary) {
            return $items->sum(function ($item) use ($salary) {
                return $item->type === 'percentage'
                    ? ($item->amount * $salary / 100)
                    : $item->amount;
            });
        };

        $total_allowance = $calcTotal($employee->allowances);
        $total_commission = $calcTotal($employee->commissions);
        $total_bonus = $calcTotal($employee->bonuses);
        $total_pearks = $calcTotal($employee->pearks);
        $total_loan = $calcTotal($employee->loans);
        $total_saturation_deduction = $calcTotal($employee->saturationDeductions);
        $total_other_payment = $calcTotal($employee->otherPayments);
        $total_pension = $calcTotal($employee->pensions);

        // Overtime
        $total_overtime = $employee->overtimes->sum(function ($ot) {
            return $ot->number_of_days * $ot->hours * $ot->rate;
        });

        // Attendance and Leave
        $today = Carbon::now();
        $days_so_far = $today->day;

        $attendance_count = AttendanceEmployee::where('employee_id', $this->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->whereDate('date', '<=', $today)
            ->distinct(DB::raw('DATE(date)'))
            ->count(DB::raw('DISTINCT DATE(date)'));

        $leaves = Leave::where('employee_id', $this->id)
            ->whereMonth('start_date', $today->month)
            ->whereYear('start_date', $today->year)
            ->get();

        $total_leave_days = $leaves->sum('total_leave_days');
        $present_days = $attendance_count + $total_leave_days;
        $absent_days = max($days_so_far - $present_days, 0);
        $leave_deduction = $absent_days * $per_day_amount;

        // Net Salary
        $adjustments = $total_allowance + $total_commission + $total_bonus + $total_pearks
            + $total_other_payment + $total_overtime
            - $total_loan - $total_saturation_deduction - $total_pension
            - $leave_deduction;

        return $salary + $adjustments;
    }

    public static function allowance($id)
    {
        //allowance
        $allowances = Allowance::where('employee_id', '=', $id)->get();
        $total_allowance = 0;
        foreach ($allowances as $allowance) {
            $total_allowance = $allowance->amount + $total_allowance;
        }

        $allowance_json = json_encode($allowances);

        return $allowance_json;
    }

    public static function commission($id)
    {
        //commission
        $commissions = Commission::where('employee_id', '=', $id)->get();
        $total_commission = 0;

        foreach ($commissions as $commission) {
            $total_commission = $commission->amount + $total_commission;
        }
        $commission_json = json_encode($commissions);

        return $commission_json;
    }

    public static function loan($id)
    {
        //Loan
        $loans = Loan::where('employee_id', '=', $id)->get();
        $total_loan = 0;
        foreach ($loans as $loan) {
            $total_loan = $loan->amount + $total_loan;
        }
        $loan_json = json_encode($loans);

        return $loan_json;
    }

    public static function saturation_deduction($id)
    {
        //Saturation Deduction
        $saturation_deductions = SaturationDeduction::where('employee_id', '=', $id)->get();
        $total_saturation_deduction = 0;
        foreach ($saturation_deductions as $saturation_deduction) {
            $total_saturation_deduction = $saturation_deduction->amount + $total_saturation_deduction;
        }
        $saturation_deduction_json = json_encode($saturation_deductions);

        return $saturation_deduction_json;
    }

    public static function other_payment($id)
    {
        //OtherPayment
        $other_payments = OtherPayment::where('employee_id', '=', $id)->get();
        $total_other_payment = 0;
        foreach ($other_payments as $other_payment) {
            $total_other_payment = $other_payment->amount + $total_other_payment;
        }
        $other_payment_json = json_encode($other_payments);

        return $other_payment_json;
    }

    public static function overtime($id)
    {
        //Overtime
        $over_times = Overtime::where('employee_id', '=', $id)->get();
        $total_over_time = 0;
        foreach ($over_times as $over_time) {
            $total_work = $over_time->number_of_days * $over_time->hours;
            $amount = $total_work * $over_time->rate;
            $total_over_time = $amount + $total_over_time;
        }
        $over_time_json = json_encode($over_times);

        return $over_time_json;
    }

    public static function employee_id()
    {
        $employee = Employee::latest()->first();

        return !empty($employee) ? $employee->id + 1 : 1;
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function phone()
    {
        return $this->hasOne(Employee::class, 'id', 'phone');
    }

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function designation()
    {
        return $this->hasOne(Designation::class, 'id', 'designation_id');
    }

    public function salaryType()
    {
        return $this->hasOne(PayslipType::class, 'id', 'salary_type');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function paySlip()
    {
        return $this->hasOne(PaySlip::class, 'id', 'employee_id');
    }


    public function present_status($employee_id, $data)
    {
        return AttendanceEmployee::where('employee_id', $employee_id)->where('date', $data)->first();
    }
    public static function employee_name($name)
    {

        $employee = Employee::where('id', $name)->first();
        if (!empty($employee)) {
            return $employee->name;
        }
    }


    public static function login_user($name)
    {
        $user = User::where('id', $name)->first();
        return $user->name;
    }

    public static function employee_salary($salary)
    {

        $employee = Employee::where("salary", $salary)->first();
        if ($employee->salary == '0' || $employee->salary == '0.0') {
            return "-";
        } else {
            return $employee->salary;
        }
    }
}
