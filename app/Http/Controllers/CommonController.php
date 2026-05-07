<?php

namespace App\Http\Controllers;

use App\Models\AllowanceOption;
use App\Models\AwardType;
use App\Models\Branch;
use App\Models\DeductionOption;
use App\Models\Department;
use App\Models\Designation;
use App\Models\ExpenseType;
use App\Models\GoalType;
use App\Models\IncomeType;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\LoanOption;
use App\Models\PaymentType;
use App\Models\Shift;
use App\Models\SubDepartment;
use App\Models\TerminationType;
use App\Models\Trainer;
use App\Models\TrainingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    public function createDepartment(Request $request)
    {
        $department = new Department();
        $department->branch_id = $request->branch;
        $department->name = $request->name;
        $department->slug = strtoupper($request->slug);
        $department->created_by = Auth::user()->creatorId();
        $department->save();

        $getDepartment = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getDepartment as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'department', 'output' => $output, 'department' => $getDepartment)));
        exit;
    }

    public function createBranch(Request $request)
    {
        $branch = new Branch();
        $branch->name = $request->name;
        $branch->created_by = Auth::user()->creatorId();
        $branch->save();

        $getBranch = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getBranch as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'branch', 'output' => $output, 'branch' => $getBranch)));
        exit;
    }

    public function createDesignation(Request $request)
    {

        $designation = new Designation();
        $designation->department_id = $request->department;
        $designation->name = $request->name;
        $designation->created_by = Auth::user()->creatorId();

        $designation->save();

        $getDesignation = Designation::where(['created_by' => Auth::user()->creatorId(), 'department_id' => $request->department])->get()->pluck('name', 'id');
        $output = "";

        foreach ($getDesignation as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'designation', 'output' => $output, 'designation' => $getDesignation)));
        exit;
    }

    public function creategoalType(Request $request)
    {

        $goaltype = new GoalType();
        $goaltype->name = $request->name;
        $goaltype->created_by = Auth::user()->creatorId();
        $goaltype->save();

        $getGoalType = GoalType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getGoalType as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'goaltype', 'output' => $output, 'goaltype' => $getGoalType)));
        exit;
    }

    public function createtrainingType(Request $request)
    {
        $trainingtype = new TrainingType();
        $trainingtype->name = $request->name;
        $trainingtype->created_by = Auth::user()->creatorId();
        $trainingtype->save();

        $getTrainingType = TrainingType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getTrainingType as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }
        print_r(json_encode(array('section' => 'trainingtype', 'output' => $output, 'trainingtype' => $getTrainingType)));
        exit;
    }

    public function createTrainer(Request $request)
    {

        $trainer = new Trainer();
        $trainer->branch = $request->branch;
        $trainer->firstname = $request->firstname;
        $trainer->lastname = $request->lastname;
        $trainer->contact = $request->contact;
        $trainer->email = $request->email;
        $trainer->address = $request->address;
        $trainer->expertise = $request->expertise;
        $trainer->created_by = Auth::user()->creatorId();
        $trainer->save();

        $getTrainer = Trainer::where('created_by', Auth::user()->creatorId())->get()->pluck('firstname', 'id');
        $output = "";

        foreach ($getTrainer as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'trainer', 'output' => $output, 'trainer' => $getTrainer)));
        exit;
    }

    public function createAwardType(Request $request)
    {

        $awardtype = new AwardType();
        $awardtype->name = $request->name;
        $awardtype->created_by = Auth::user()->creatorId();
        $awardtype->save();

        $getAwardType = AwardType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getAwardType as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }


        print_r(json_encode(array('section' => 'award-type', 'output' => $output, 'awardtype' => $getAwardType)));
        exit;
    }

    public function createTerminationType(Request $request)
    {

        $terminationtype = new TerminationType();
        $terminationtype->name = $request->name;
        $terminationtype->created_by = Auth::user()->creatorId();
        $terminationtype->save();


        $getTerminationType = TerminationType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getTerminationType as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'termination_type', 'output' => $output, 'termination' => $getTerminationType)));
        exit;
    }

    public function createJobCategory(Request $request)
    {

        $jobCategory = new JobCategory();
        $jobCategory->title = $request->name;
        $jobCategory->created_by = Auth::user()->creatorId();
        $jobCategory->save();



        $getJobCategory = JobCategory::where('created_by', Auth::user()->creatorId())->get()->pluck('title', 'id');
        // print_r($getJobCategory); exit;
        $output = "";

        foreach ($getJobCategory as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'job-category', 'output' => $output, 'jobcategory' => $getJobCategory)));
        exit;
    }

    public function createJob(Request $request)
    {

        $job = new Job();
        $job->title = $request->job_title;
        $job->branch = $request->branch;
        $job->category = $request->job_category;
        $job->skill = $request->skill_box;
        $job->position = $request->no_of_position;
        $job->status = $request->status;
        $job->created_by = Auth::user()->creatorId();
        $job->save();



        $getJob = Job::where('created_by', Auth::user()->creatorId())->get()->pluck('title', 'id');
        $output = "";

        foreach ($getJob as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'job', 'output' => $output, 'job' => $getJob)));
        exit;
    }

    public function createAllowanceOptions(Request $request)
    {

        $allowanceoption = new AllowanceOption();
        $allowanceoption->name = $request->name;
        $allowanceoption->created_by = Auth::user()->creatorId();
        $allowanceoption->save();

        $getAllowanceOption = AllowanceOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getAllowanceOption as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'allowanceoption', 'output' => $output, 'allowance' => $getAllowanceOption)));
        exit;
    }

    public function createDeductionOptions(Request $request)
    {

        $deductionoption = new DeductionOption();
        $deductionoption->name = $request->name;
        $deductionoption->created_by = Auth::user()->creatorId();
        $deductionoption->save();

        $getDeductionOption = DeductionOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getDeductionOption as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'deduction-options', 'output' => $output, 'deduction' => $getDeductionOption)));
        exit;
    }

    public function createLoanOption(Request $request)
    {

        $loanoption = new LoanOption();
        $loanoption->name = $request->name;
        $loanoption->created_by = Auth::user()->creatorId();
        $loanoption->save();

        $getLoanOption = LoanOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getLoanOption as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'loan-options', 'output' => $output, 'loanOption' => $getLoanOption)));
        exit;
    }

    public function createSaturationOptions(Request $request)
    {
        $deductionoption = new DeductionOption();
        $deductionoption->name = $request->name;
        $deductionoption->created_by = Auth::user()->creatorId();
        $deductionoption->save();

        $getDeductionOption = DeductionOption::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getDeductionOption as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'deduction-option', 'output' => $output, 'deductionOption' => $getDeductionOption)));
        exit;
    }

    public function createShift(Request $request)
    {

        $shift = new Shift();
        $shift->name = $request->name;
        $shift->created_by = Auth::user()->creatorId();

        $shift->save();

        $getShift = Shift::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getShift as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'shift', 'output' => $output, 'shift' => $getShift)));
        exit;
    }

    public function createSubDepartment(Request $request)
    {

        $subdepartments = new SubDepartment();
        $subdepartments->department = $request->department;
        $subdepartments->name = $request->name;
        $subdepartments->created_by = Auth::user()->creatorId();
        $subdepartments->save();

        $getSubDepartment = SubDepartment::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getSubDepartment as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'subdepartment', 'output' => $output, 'subdepartment' => $getSubDepartment)));
        exit;
    }
    public function createIncomeCategory(Request $request)
    {

        $incometype = new IncomeType();
        $incometype->name = $request->name;
        $incometype->created_by = Auth::user()->creatorId();
        $incometype->save();

        $getIncomeType = IncomeType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getIncomeType as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'category', 'output' => $output, 'category' => $getIncomeType)));
        exit;
    }
    public function createPaymentMethod(Request $request)
    {

        $paymenttype = new PaymentType();
        $paymenttype->name = $request->name;
        $paymenttype->created_by = Auth::user()->creatorId();
        $paymenttype->save();

        $getPaymentType = PaymentType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getPaymentType as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'paymenttype', 'output' => $output, 'paymenttype' => $getPaymentType)));
        exit;
    }
    public function createExpenseCategory(Request $request)
    {

        $expensetype = new ExpenseType();
        $expensetype->name = $request->name;
        $expensetype->created_by = Auth::user()->creatorId();
        $expensetype->save();

        $getExpenseType = ExpenseType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $output = "";

        foreach ($getExpenseType as $key => $item) {
            $output .= "<option value='" . $key . "'>" . $item . "</option>";
        }

        print_r(json_encode(array('section' => 'expensecategory', 'output' => $output, 'ExpenseType' => $getExpenseType)));
        exit;
    }

    public function getBranch()
    {
        $getBranch = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        print_r(json_encode($getBranch));
        exit;
    }

    public function getDepartment()
    {
        $getDepartment = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        print_r(json_encode($getDepartment));
        exit;
    }

    public function getTrainingType()
    {
        $getTrainingType = TrainingType::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        print_r(json_encode($getTrainingType));
        exit;
    }

    public function getTrainer()
    {
        $getTrainer = Trainer::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        print_r(json_encode($getTrainer));
        exit;
    }
}
