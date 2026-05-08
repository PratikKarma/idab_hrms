<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport1;
use App\Exports\EmployeeExport2;
use App\Exports\EmployeeExport3;
use App\Imports\EmployeesImport;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Document;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Plan;
use App\Models\SubDepartment;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Models\JoiningLetter;
use App\Exports\EmployeesExport4;
use App\Models\Contract;
use App\Models\ExperienceCertificate;
use App\Models\LoginDetail;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\NOC;
use App\Models\PaySlip;
use App\Models\Shift;
use App\Models\Termination;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Srmklive\PayPal\Services\Str;

class EmployeeController extends Controller
{

    public function index()
    {

        if (Auth::user()->can('Manage Employee')) {
            if (Auth::user()->type == 'employee') {
                $employees = Employee::where('user_id', '=', Auth::user()->id)->get();
            } else {
                $employees = Employee::where('created_by', Auth::user()->creatorId())->with(['branch', 'department', 'designation', 'user'])->get();
            }

            return view('employee.index', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('Create Employee')) {
            $company_settings = Utility::settings();
            $documents = Document::where('created_by', Auth::user()->creatorId())->get();
            $branches = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $subdepartments = SubDepartment::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $shift = Shift::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employees = User::where('created_by', Auth::user()->creatorId())->get();
            $employeesId = Auth::user()->employeeIdFormat($this->employeeNumber());

            return view('employee.create', compact('employees', 'employeesId', 'departments', 'subdepartments', 'designations', 'shift', 'documents', 'branches', 'company_settings'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('Create Employee')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'fname' => 'required',
                    'lname' => 'required',
                    'dob' => 'required',
                    'gender' => 'required',
                    'phone' => 'required|numeric',
                    'address' => 'required',
                    'email' => 'required|unique:users',
                    'password' => 'required',
                    'branch_id' => 'required',
                    'department_id' => 'required',
                    'subdepartment_id' => 'required',
                    'designation_id' => 'required',
                    'company_shift_time' => 'required',
                    'shift_id' => 'required',
                    'document.*' => 'required',
                ],
                [
                    'company_shift_time.required' => __('The Employee Zone Time field is required.'),
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->withInput()->with('error', $messages->first());
            }

            $objUser = User::find(Auth::user()->creatorId());
            $total_employee = $objUser->countEmployees();
            $plan = Plan::find($objUser->plan);
            $date = date("Y-m-d H:i:s");
            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->where('created_by', Auth::user()->creatorId())->first();
            $passcode = random_int(100000, 999999);
            // new company default language
            if ($default_language == null) {
                $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();
            }

            if ($total_employee < $plan->max_employees || $plan->max_employees == -1) {

                $user = User::create(
                    [
                        'name' => trim($request->input('fname') . ' ' . $request->input('lname')),
                        'email' => $request['email'],
                        'password' => Hash::make($request['password']),
                        'type' => 'employee',
                        'lang' => !empty($default_language) ? $default_language->value : 'en',
                        'created_by' => Auth::user()->creatorId(),
                        'email_verified_at' => $date,
                        'passcode' => $passcode
                    ]
                );
                $user->save();
                $user->assignRole('Employee');
            } else {
                return redirect()->back()->with('error', __('Your employee limit is over, Please upgrade plan.'));
            }


            if (!empty($request->document) && !is_null($request->document)) {
                $document_implode = implode(',', array_keys($request->document));
            } else {
                $document_implode = null;
            }

            $company_start_time = null;
            $company_end_time   = null;

            if (!empty($request->company_shift_time)) {
                $times = explode('-', $request->company_shift_time);
                $company_start_time = isset($times[0]) ? trim($times[0]) : null;
                $company_end_time   = isset($times[1]) ? trim($times[1]) : null;
            }

            $employee = Employee::create(
                [
                    'user_id' => $user->id,
                    'name' => $request['fname'],
                    'last_name' => $request['lname'],
                    'dob' => $request['dob'],
                    'gender' => $request['gender'],
                    'phone' => $request['phone'] ?? null,
                    'address' => $request['address'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                    'employee_id' => $this->employeeNumber(),
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'subdepartment_id' => $request['subdepartment_id'],
                    'designation_id' => $request['designation_id'],
                    'shift_id' => $request['shift_id'],
                    'company_doj' => $request['company_doj'],
                    'company_start_time' => $company_start_time,
                    'company_end_time'   => $company_end_time,
                    'documents' => $document_implode,
                    'account_holder_name' => $request['account_holder_name'],
                    'account_number' => $request['account_number'],
                    'bank_name' => $request['bank_name'],
                    'bank_identifier_code' => $request['bank_identifier_code'],
                    'branch_location' => $request['branch_location'],
                    'tax_payer_id' => $request['tax_payer_id'],
                    'created_by' => Auth::user()->creatorId(),
                ]
            );

            if ($request->hasFile('document')) {
                foreach ($request->document as $key => $document) {

                    $image_size = $request->file('document')[$key]->getSize();
                    $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);

                    if ($result == 1) {
                        $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('document')[$key]->getClientOriginalExtension();
                        $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                        $dir = 'uploads/document/';

                        $path = Utility::upload_coustom_file($request, 'document', $fileNameToStore, $dir, $key, []);

                        if ($path['flag'] == 1) {
                            $url = $path['url'];
                        } else {
                            return redirect()->back()->with('error', __($path['msg']));
                        }
                        $employee_document = EmployeeDocument::create(
                            [
                                'employee_id' => $employee['employee_id'],
                                'document_id' => $key,
                                'document_value' => $path['url'],
                                'created_by' => Auth::user()->creatorId(),
                            ]
                        );
                        $employee_document->save();
                    }
                }
            }
            $setings = Utility::settings();
            if ($setings['new_employee'] == 1) {
                $department = Department::find($request['department_id']);
                $branch = Branch::find($request['branch_id']);
                $designation = Designation::find($request['designation_id']);
                $uArr = [
                    'employee_email' => $user->email,
                    'employee_password' => $request->password,
                    'employee_name' => trim($request->input('fname') . ' ' . $request->input('lname')),
                    'employee_branch' => !empty($branch->name) ? $branch->name : '',
                    'employee_department' => !empty($department->name) ? $department->name : '',
                    'employee_designation' => !empty($designation->name) ? $designation->name : '',
                    'employee_passcode' => $user->passcode,
                ];
                $resp = Utility::sendEmailTemplate('new_employee', [$user->id => $user->email], $uArr);

                return redirect()->route('employee.index')->with('success', __('Employee successfully created.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
            }
            return redirect()->route('employee.index')->with('success', __('Employee successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Employee Not Found.'));
        }
        if (Auth::user()->can('Edit Employee')) {
            $company_settings = Utility::settings();
            $employee = Employee::find($id);
            $documents = Document::where('created_by', Auth::user()->creatorId())->get();
            $branches = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $subdepartments = SubDepartment::where(['department' => $employee->department_id, 'created_by' => Auth::user()->creatorId()])->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $shift = Shift::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employeesId = Auth::user()->employeeIdFormat($employee->employee_id);

            return view('employee.edit', compact('company_settings', 'employee', 'employeesId', 'branches', 'departments', 'subdepartments', 'shift', 'designations', 'documents'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('Edit Employee')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'last_name' => 'required',
                    'dob' => 'required',
                    'email' => 'required|unique:employees,email,' . $id . ',id',
                    'gender' => 'required',
                    'phone' => 'required|numeric',
                    'address' => 'required',
                    'company_shift_time' => 'required',
                ],
                [
                    'company_shift_time.required' => __('The Employee Zone Time field is required.'),
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $employee = Employee::findOrFail($id);
            $input = $request->all();

            $company_start_time = null;
            $company_end_time   = null;

            if (!empty($request->company_shift_time)) {
                $times = explode('-', $request->company_shift_time);
                $company_start_time = isset($times[0]) ? trim($times[0]) : null;
                $company_end_time   = isset($times[1]) ? trim($times[1]) : null;
            }

            $employee->company_start_time = $company_start_time;
            $employee->company_end_time   = $company_end_time;

            if ($request->document) {

                foreach ($request->document as $key => $document) {
                    $employee_document = EmployeeDocument::where('employee_id', $employee->employee_id)->where('document_id', $key)->first();
                    if (!empty($document)) {

                        //storage limit
                        $dir = 'uploads/document/';
                        if (!empty($employee_document)) {
                            $file_path = $dir . $employee_document->document_value;
                        }
                        $image_size = $request->file('document')[$key]->getSize();
                        $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);

                        if ($result == 1) {
                            if (!empty($$file_path)) {
                                Utility::changeStorageLimit(Auth::user()->creatorId(), $file_path);
                            }

                            $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('document')[$key]->getClientOriginalExtension();
                            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                            $dir = 'uploads/document/';

                            $image_path = $dir . $fileNameToStore;

                            $path = Utility::upload_coustom_file($request, 'document', $fileNameToStore, $dir, $key, []);
                            if (!empty($employee_document)) {
                                if ($employee_document->document_value) {
                                    File::delete(storage_path('uploads/document/' . $employee_document->document_value));
                                }
                                $employee_document->document_value = $fileNameToStore;
                                $employee_document->save();
                            } else {
                                $employee_document = new EmployeeDocument();
                                $employee_document->employee_id = $employee->employee_id;
                                $employee_document->document_id = $key;
                                $employee_document->document_value = $fileNameToStore;
                                $employee_document->save();
                            }

                            if ($path['flag'] == 1) {
                                $url = $path['url'];
                            } else {
                                return redirect()->back()->with('error', __($path['msg']));
                            }
                        }
                    }
                }
            }

            if (!empty($request->document) && !is_null($request->document)) {
                $document_implode = implode(',', array_keys($request->document));
            } else {
                $document_implode = null;
            }

            $input['documents'] = $document_implode;

            $employee->fill($input)->save();
            if (!empty($request->email)) {
                $user = User::find($employee->user_id);
                $user->email = $request->email;
                $user->save();
            }

            if ($request->salary) {
                return redirect()->route('setsalary.index')->with('success', 'Employee successfully updated.');
            }

            if (Auth::user()->type != 'employee') {
                return redirect()->route('employee.index')->with('success', __('Employee successfully updated.') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
            } else {
                return redirect()->route('employee.show', Crypt::encrypt($employee->id))->with('success', __('Employee successfully updated.') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('Delete Employee')) {
            $employee = Employee::findOrFail($id);
            $user = User::where('id', '=', $employee->user_id)->first();
            $emp_documents = EmployeeDocument::where('employee_id', $employee->employee_id)->get();
            $ContractEmployee = Contract::where('employee_name', '=', $employee->user_id)->get();
            $payslips = PaySlip::where('employee_id', $id)->get();
            $employee->delete();
            $user->delete();

            foreach ($ContractEmployee as $contractdelete) {
                $contractdelete->delete();
            }

            foreach ($payslips as $payslip) {
                $payslip->delete();
            }

            $dir = storage_path('uploads/document/');
            foreach ($emp_documents as $emp_document) {

                $emp_document->delete();
                if (!empty($emp_document->document_value)) {

                    $file_path = 'uploads/document/' . $emp_document->document_value;
                    $result = Utility::changeStorageLimit(Auth::user()->creatorId(), $file_path);
                }
            }

            return redirect()->route('employee.index')->with('success', 'Employee successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        if (Auth::user()->can('Show Employee')) {
            try {
                $empId = Crypt::decrypt($id);
            } catch (\RuntimeException $e) {
                return redirect()->back()->with('error', __('Employee not avaliable'));
            }
            $documents = Document::where('created_by', Auth::user()->creatorId())->get();
            $branches = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employee = Employee::find($empId);
            $employeesId = Auth::user()->employeeIdFormat($employee->employee_id);
            $empId = Crypt::decrypt($id);

            return view('employee.show', compact('employee', 'employeesId', 'branches', 'departments', 'designations', 'documents'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function employeeNumber()
    {
        $latest = Employee::where('created_by', '=', Auth::user()->creatorId())->latest('id')->first();
        if (!$latest) {
            return 1;
        }

        return $latest->employee_id + 1;
    }

    public function importFile()
    {
        return view('employee.import');
    }

    public function exportFile()
    {
        return view('employee.export-excels');
    }

    protected function ensureRequiredTablesExist(array $tables)
    {
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                throw new \Exception("Missing required table: `$table`. Please run migrations.");
            }
        }
    }

    public function import(Request $request)
    {
        try {
            $this->ensureRequiredTablesExist([
                'branches',
                'departments',
                'sub_departments',
                'designations',
                'shifts',
                'employees',
                'users'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $rows = (new EmployeesImport())->toArray($request->file('file'))[0];

        if (count($rows) <= 1) {
            return redirect()->back()->with('error', 'The file is empty or improperly formatted.');
        }

        $totalRecords = count($rows) - 1;
        $importErrors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            DB::beginTransaction();
            try {
                // === Basic Validation ===
                if (empty($row[6]) || empty($row[7])) {
                    throw new \Exception("Missing required email or password at row $i.");
                }

                $existingEmployee = Employee::where('email', $row[6])->first();
                $existingUser = User::where('email', $row[6])->first();

                $branchId = $this->check_branch($row[9]);
                $departmentId = $this->check_department($row[10], $branchId);
                $subDeptId = $this->check_sub_department($row[11], $departmentId);
                $designationId = $this->check_designation($row[12], $departmentId);
                $shiftId = $this->check_shift($row[13]);

                if ($existingEmployee && $existingUser) {
                    $employee = $existingEmployee;
                } else {
                    $user = new User();
                    $user->name = $row[0] . ' ' . $row[1];
                    $user->email = $row[6];
                    $user->password = Hash::make($row[7]);
                    $user->type = 'employee';
                    $user->lang = 'en';
                    $user->created_by = Auth::user()->creatorId();
                    $user->save();
                    $user->assignRole('Employee');

                    $employee = new Employee();
                    $employee->employee_id = $this->employeeNumber();
                    $employee->user_id = $user->id;
                }

                $employee->fill([
                    'name' => $row[0],
                    'last_name' => $row[1],
                    'dob' => $row[2],
                    'gender' => $row[3],
                    'phone' => $row[4],
                    'address' => $row[5],
                    'email' => $row[6],
                    'password' => Hash::make($row[7]),
                    'branch_id' => $branchId,
                    'department_id' => $departmentId,
                    'subdepartment_id' => $subDeptId,
                    'designation_id' => $designationId,
                    'shift_id' => $shiftId,
                    'company_doj' => $row[14],
                    'account_holder_name' => $row[15],
                    'account_number' => $row[16],
                    'bank_name' => $row[17],
                    'bank_identifier_code' => $row[18],
                    'branch_location' => $row[19],
                    'tax_payer_id' => $row[20],
                    'created_by' => Auth::user()->creatorId(),
                ]);

                $employee->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                $friendlyMessage = "An error occurred while processing this row. Please review the data and try again.";

                $errorText = $e->getMessage();

                if (str::contains($errorText, ['Unknown column'])) {
                    $friendlyMessage = "One of the required columns in the database is missing. Please contact the administrator.";
                } elseif (Str::contains($errorText, ['Integrity constraint violation'])) {
                    $friendlyMessage = "This entry violates a database rule (e.g., duplicate email or invalid foreign key).";
                } elseif (Str::contains($errorText, ['Cannot add or update a child row'])) {
                    $friendlyMessage = "Invalid reference data (e.g., branch, department, sub department, shift or designation might not exist).";
                } elseif (Str::contains($errorText, ['SQLSTATE'])) {
                    $friendlyMessage = "A database error occurred. Please verify the data in this row.";
                } elseif (Str::contains($errorText, ['Call to undefined method'])) {
                    $friendlyMessage = "A technical error occurred. Please inform the developer team.";
                } elseif (Str::contains($errorText, ['Trying to access array offset'])) {
                    $friendlyMessage = "Some expected data is missing in this row. Please review the file format.";
                }

                $importErrors[] = [
                    'row_number' => $i + 1,
                    'row_data' => implode(', ', $row),
                    'error_message' => $friendlyMessage
                ];
            }
        }

        if (empty($importErrors)) {
            return redirect()->back()->with('success', "All {$totalRecords} records imported successfully.");
        } else {
            $errorCount = count($importErrors);
            $successCount = $totalRecords - $errorCount;

            Session::put('import_errors', $importErrors);

            $errorSummary = "{$successCount} records imported, {$errorCount} failed. ";
            $errorSummary .= '<br><a href="javascript:void(0)" '
                . 'onclick="var el=document.getElementById(\'importErrorDetails\'); '
                . 'el.style.display = (el.style.display===\'none\'?\'block\':\'none\'); '
                . 'return false;" '
                . 'style="color:#007bff; text-decoration:underline;">'
                . 'See details below</a>';

            $htmlDetails = '<div id="importErrorDetails" style="display:none; '
                . 'margin-top:10px; padding:10px; background: rgb(255 49 49); '
                . 'border:1px solid rgb(255, 95, 95); border-radius:4px; max-height:200px; overflow-y:auto;">'
                . '<ul id="errorList" style="padding-left:10px; margin:0;">';

            foreach ($importErrors as $index => $err) {
                $htmlDetails .= '<li id="error-' . $index . '" style="margin-bottom:10px; position:relative; padding:10px; border:1px solid #ddd; border-radius:4px; list-style-type: none;">'
                    . '<button onclick="removeError(\'error-' . $index . '\')" style="position:absolute; top:-10px; right:-10px; background:#ffd400; color:white; border:none; border-radius:50%; width:24px; height:24px; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Remove">&times;</button>'
                    . '<strong>Row ' . htmlspecialchars($err['row_number']) . ':</strong> '
                    . htmlspecialchars($err['error_message']) . '<br>'
                    . '<em>Data:</em> ' . htmlspecialchars($err['row_data'])
                    . '</li>';
            }

            $htmlDetails .= '</ul></div>';

            $escapedErrorSummary = addslashes($errorSummary . $htmlDetails);

            return redirect()->back()->with('error', $escapedErrorSummary);

        }
    }
    public function check_branch($branch_name)
    {
        $branch = Branch::where(['name' => $branch_name, 'created_by' => Auth::user()->creatorId()])->first();
        if (isset($branch->id)) {
            $branch_id = $branch->id;
        } else {
            $branch = new Branch();
            $branch->name = $branch_name;
            $branch->created_by = Auth::user()->creatorId();
            $branch->save();
            $branch_id = $branch->id;
        }
        return $branch_id;
    }
    public function check_department($department_name, $branch_id)
    {
        $department = Department::where(['branch_id' => $branch_id, 'name' => $department_name, 'created_by' => Auth::user()->creatorId()])->first();
        if (isset($department->id)) {
            $department_id = $department->id;
        } else {
            $department = new Department();
            $department->branch_id = $branch_id;
            $department->name = $department_name;
            $department->slug = strtoupper(substr($department_name, 0, 3));
            $department->created_by = Auth::user()->creatorId();
            $department->save();
            $department_id = $department->id;
        }
        return $department_id;
    }

    public function check_sub_department($sub_department_name, $department_id)
    {
        $subdepartment = SubDepartment::where(['department' => $department_id, 'name' => $sub_department_name, 'created_by' => Auth::user()->creatorId()])->first();
        if (isset($subdepartment->id)) {
            $sub_department_id = $subdepartment->id;
        } else {
            $sub_department = new SubDepartment();
            $sub_department->department = $department_id;
            $sub_department->name = $sub_department_name;
            $sub_department->created_by = Auth::user()->creatorId();
            $sub_department->save();
            $sub_department_id = $sub_department->id;
        }
        return $sub_department_id;
    }

    public function check_designation($designation_name, $department_id)
    {
        $designation = Designation::where(['department_id' => $department_id, 'name' => $designation_name, 'created_by' => Auth::user()->creatorId()])->first();
        if (!empty($designation)) {
            $designation_id = $designation->id;
        } else {
            $designation = new Designation();
            $designation->department_id = $department_id;
            $designation->name = $designation_name;
            $designation->created_by = Auth::user()->creatorId();
            $designation->save();
            $designation_id = $designation->id;
        }
        return $designation_id;
    }

    public function check_shift($shift_name)
    {
        $shift = Shift::where(['name' => $shift_name, 'created_by' => Auth::user()->creatorId()])->first();
        if (!empty($shift)) {
            $shift_id = $shift->id;
        } else {
            $shift = new Shift();
            $shift->name = $shift_name;
            $shift->created_by = Auth::user()->creatorId();
            $shift->save();
            $shift_id = $shift->id;
        }
        return $shift_id;
    }

    public function exportExcelD()
    {
        $name = 'employee_' . date('Y-m-d i:h:s');
        $data = Excel::download(new EmployeesExport4(), $name . '.xlsx');

        return $data;
    }
    public function exportExcelA()
    {
        $name = 'employee-form-A_' . date('Y-m-d i:h:s');
        $data = Excel::download(new EmployeeExport1(), $name . '.xlsx');

        return $data;
    }
    public function exportExcelB()
    {
        $name = 'employee-form-B_' . date('Y-m-d i:h:s');
        $data = Excel::download(new EmployeeExport2(), $name . '.xlsx');

        return $data;
    }
    public function exportExcelC()
    {
        $name = 'employee-form-C_' . date('Y-m-d i:h:s');
        $data = Excel::download(new EmployeeExport3(), $name . '.xlsx');

        return $data;
    }

    public function profile(Request $request)
    {
        if (Auth::user()->can('Manage Employee Profile')) {
            $employees = Employee::where('created_by', Auth::user()->creatorId())->with(['designation', 'user']);
            if (!empty($request->branch_id)) {
                $employees->where('branch_id', $request->branch_id);
            }
            if (!empty($request->department_id)) {
                $employees->where('department_id', $request->department_id);
            }
            if (!empty($request->designation_id)) {
                $employees->where('designation_id', $request->designation_id);
            }
            $employees = $employees->get();

            $brances = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');

            $departments = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');

            $designations = Designation::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('employee.profile', compact('employees', 'departments', 'designations', 'brances'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function profileShow($id)
    {
        if (Auth::user()->can('Show Employee Profile')) {
            $empId = Crypt::decrypt($id);
            $documents = Document::where('created_by', Auth::user()->creatorId())->get();
            $branches = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employee = Employee::find($empId);
            if ($employee == null) {
                $employee = Employee::where('user_id', $empId)->first();
            }

            $employeesId = Auth::user()->employeeIdFormat($employee->employee_id);

            return view('employee.show', compact('employee', 'employeesId', 'branches', 'departments', 'designations', 'documents'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function lastLogin(Request $request)
    {
        $users = User::where('created_by', Auth::user()->creatorId())->get();

        $time = date_create($request->month);
        $firstDayofMOnth = (date_format($time, 'Y-m-d'));
        $lastDayofMonth = \Carbon\Carbon::parse($request->month)->endOfMonth()->toDateString();
        $objUser = Auth::user();

        $usersList = User::where('created_by', '=', $objUser->creatorId())
            ->whereNotIn('type', ['super admin', 'company'])->get()->pluck('name', 'id');
        $usersList->prepend('All', '');
        if ($request->month == null) {
            $userdetails = DB::table('login_details')
                ->join('users', 'login_details.user_id', '=', 'users.id')
                ->select(DB::raw('login_details.*, users.id as user_id , users.name as user_name , users.email as user_email ,users.type as user_type'))
                ->where(['login_details.created_by' => Auth::user()->creatorId()])
                ->whereMonth('date', date('m'))->whereYear('date', date('Y'));
        } else {
            $userdetails = DB::table('login_details')
                ->join('users', 'login_details.user_id', '=', 'users.id')
                ->select(DB::raw('login_details.*, users.id as user_id , users.name as user_name , users.email as user_email ,users.type as user_type'))
                ->where(['login_details.created_by' => Auth::user()->creatorId()]);
        }
        if (!empty($request->month)) {
            $userdetails->where('date', '>=', $firstDayofMOnth);
            $userdetails->where('date', '<=', $lastDayofMonth);
        }
        if (!empty($request->employee)) {
            $userdetails->where(['user_id' => $request->employee]);
        }
        $userdetails = $userdetails->get();

        return view('employee.lastLogin', compact('users', 'usersList', 'userdetails'));
    }

    public function employeeJson(Request $request)
    {
        $employees = Employee::where('branch_id', $request->branch)->get()->pluck('name', 'id')->toArray();

        return response()->json($employees);
    }

    public function joiningletterPdf($id)
    {
        $users = Auth::user();

        $currantLang = $users->currentLanguage();
        $joiningletter = JoiningLetter::where('lang', $currantLang)->where('created_by', Auth::user()->creatorId())->first();
        $date = date('Y-m-d');
        $employees = Employee::where('id', $id)->where('created_by', Auth::user()->creatorId())->first();
        $settings = Utility::settings();

        if ($employees && $employees->company_start_time && $employees->company_end_time) {
            $startTime = $employees->company_start_time;
            $endTime   = $employees->company_end_time;
        } else {
            $startTime  = Utility::getValByName('company_start_time');
            $endTime    = Utility::getValByName('company_end_time');
        }
        $secs = strtotime($startTime) - strtotime("00:00");
        $result = date("H:i", strtotime($endTime) - $secs);
        $obj = [
            'date' => Auth::user()->dateFormat($date),
            'app_name' => env('APP_NAME'),
            'employee_name' => $employees->name,
            'address' => !empty($employees->address) ? $employees->address : '',
            'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',
            'start_date' => !empty($employees->company_doj) ? $employees->company_doj : '',
            'branch' => !empty($employees->Branch->name) ? $employees->Branch->name : '',
            'start_time' => !empty($startTime) ? $startTime : '',
            'end_time' => !empty($endTime) ? $endTime : '',
            'total_hours' => $result,
        ];

        $joiningletter->content = JoiningLetter::replaceVariable($joiningletter->content, $obj);
        return view('employee.template.joiningletterpdf', compact('joiningletter', 'employees'));
    }
    public function joiningletterDoc($id)
    {
        $users = Auth::user();

        $currantLang = $users->currentLanguage();
        $joiningletter = JoiningLetter::where('lang', $currantLang)->where('created_by', Auth::user()->creatorId())->first();
        $date = date('Y-m-d');
        $employees = Employee::where('id', $id)->where('created_by', Auth::user()->creatorId())->first();

        if ($employees && $employees->company_start_time && $employees->company_end_time) {
            $startTime = $employees->company_start_time;
            $endTime   = $employees->company_end_time;
        } else {
            $startTime  = Utility::getValByName('company_start_time');
            $endTime    = Utility::getValByName('company_end_time');
        }

        $settings = Utility::settings();
        $secs = strtotime($startTime) - strtotime("00:00");
        $result = date("H:i", strtotime($endTime) - $secs);

        $obj = [
            'date' => Auth::user()->dateFormat($date),

            'app_name' => env('APP_NAME'),
            'employee_name' => $employees->name,
            'address' => !empty($employees->address) ? $employees->address : '',
            'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',
            'start_date' => !empty($employees->company_doj) ? $employees->company_doj : '',
            'branch' => !empty($employees->Branch->name) ? $employees->Branch->name : '',
            'start_time' => !empty($startTime) ? $startTime : '',
            'end_time' => !empty($endTime) ? $endTime : '',
            'total_hours' => $result,

        ];
        $joiningletter->content = JoiningLetter::replaceVariable($joiningletter->content, $obj);
        return view('employee.template.joiningletterdocx', compact('joiningletter', 'employees'));
    }

    public function ExpCertificatePdf($id)
    {
        $currantLang = Cookie::get('LANGUAGE');
        if (!isset($currantLang)) {
            $currantLang = 'en';
        }
        $termination = Termination::where('employee_id', $id)->where('created_by', Auth::user()->creatorId())->first();
        $experience_certificate = ExperienceCertificate::where('lang', $currantLang)->where('created_by', Auth::user()->creatorId())->first();
        $date = date('Y-m-d');
        $employees = Employee::where('id', $id)->where('created_by', Auth::user()->creatorId())->first();
        $settings = Utility::settings();

        if ($employees && $employees->company_start_time && $employees->company_end_time) {
            $startTime = $employees->company_start_time;
            $endTime   = $employees->company_end_time;
        } else {
            $startTime  = Utility::getValByName('company_start_time');
            $endTime    = Utility::getValByName('company_end_time');
        }

        $secs = strtotime($startTime) - strtotime("00:00");
        $result = date("H:i", strtotime($endTime) - $secs);
        $date1 = date_create($employees->company_doj);
        $date2 = date_create($employees->termination_date);
        $diff = date_diff($date1, $date2);
        $duration = $diff->format("%a days");

        if (!empty($termination->termination_date)) {

            $obj = [
                'date' => Auth::user()->dateFormat($date),
                'app_name' => env('APP_NAME'),
                'employee_name' => $employees->name,
                'payroll' => !empty($employees->salaryType->name) ? $employees->salaryType->name : '',
                'duration' => $duration,
                'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',

            ];
        } else {
            return redirect()->back()->with('error', __('Termination date is required.'));
        }


        $experience_certificate->content = ExperienceCertificate::replaceVariable($experience_certificate->content, $obj);
        return view('employee.template.ExpCertificatepdf', compact('experience_certificate', 'employees'));
    }
    public function ExpCertificateDoc($id)
    {
        $currantLang = Cookie::get('LANGUAGE');
        if (!isset($currantLang)) {
            $currantLang = 'en';
        }
        $termination = Termination::where('employee_id', $id)->where('created_by', Auth::user()->creatorId())->first();
        $experience_certificate = ExperienceCertificate::where('lang', $currantLang)->where('created_by', Auth::user()->creatorId())->first();
        $date = date('Y-m-d');
        $employees = Employee::where('id', $id)->where('created_by', Auth::user()->creatorId())->first();
        ;
        $settings = Utility::settings();

        if ($employees && $employees->company_start_time && $employees->company_end_time) {
            $startTime = $employees->company_start_time;
            $endTime   = $employees->company_end_time;
        } else {
            $startTime  = Utility::getValByName('company_start_time');
            $endTime    = Utility::getValByName('company_end_time');
        }
        $secs = strtotime($startTime) - strtotime("00:00");
        $result = date("H:i", strtotime($endTime) - $secs);
        $date1 = date_create($employees->company_doj);
        $date2 = date_create($employees->termination_date);
        $diff = date_diff($date1, $date2);
        $duration = $diff->format("%a days");
        if (!empty($termination->termination_date)) {
            $obj = [
                'date' => Auth::user()->dateFormat($date),
                'app_name' => env('APP_NAME'),
                'employee_name' => $employees->name,
                'payroll' => !empty($employees->salaryType->name) ? $employees->salaryType->name : '',
                'duration' => $duration,
                'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',

            ];
        } else {
            return redirect()->back()->with('error', __('Termination date is required.'));
        }

        $experience_certificate->content = ExperienceCertificate::replaceVariable($experience_certificate->content, $obj);
        return view('employee.template.ExpCertificatedocx', compact('experience_certificate', 'employees'));
    }
    public function NocPdf($id)
    {
        $users = Auth::user();

        $currantLang = $users->currentLanguage();
        $noc_certificate = NOC::where('lang', $currantLang)->where('created_by', Auth::user()->creatorId())->first();
        $date = date('Y-m-d');
        $employees = Employee::where('id', $id)->where('created_by', Auth::user()->creatorId())->first();
        $settings = Utility::settings();
        if ($employees && $employees->company_start_time && $employees->company_end_time) {
            $startTime = $employees->company_start_time;
            $endTime   = $employees->company_end_time;
        } else {
            $startTime  = Utility::getValByName('company_start_time');
            $endTime    = Utility::getValByName('company_end_time');
        }
        $secs = strtotime($startTime) - strtotime("00:00");
        $result = date("H:i", strtotime($endTime) - $secs);


        $obj = [
            'date' => Auth::user()->dateFormat($date),
            'employee_name' => $employees->name,
            'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',
            'app_name' => env('APP_NAME'),
        ];

        $noc_certificate->content = NOC::replaceVariable($noc_certificate->content, $obj);
        return view('employee.template.Nocpdf', compact('noc_certificate', 'employees'));
    }
    public function NocDoc($id)
    {
        $users = Auth::user();

        $currantLang = $users->currentLanguage();
        $noc_certificate = NOC::where('lang', $currantLang)->where('created_by', Auth::user()->creatorId())->first();
        $date = date('Y-m-d');
        $employees = Employee::where('id', $id)->where('created_by', Auth::user()->creatorId())->first();
        $settings = Utility::settings();

        if ($employees && $employees->company_start_time && $employees->company_end_time) {
            $startTime = $employees->company_start_time;
            $endTime   = $employees->company_end_time;
        } else {
            $startTime  = Utility::getValByName('company_start_time');
            $endTime    = Utility::getValByName('company_end_time');
        }

        $secs = strtotime($startTime) - strtotime("00:00");
        $result = date("H:i", strtotime($endTime) - $secs);


        $obj = [
            'date' => Auth::user()->dateFormat($date),
            'employee_name' => $employees->name,
            'designation' => !empty($employees->designation->name) ? $employees->designation->name : '',
            'app_name' => env('APP_NAME'),
        ];

        $noc_certificate->content = NOC::replaceVariable($noc_certificate->content, $obj);
        return view('employee.template.Nocdocx', compact('noc_certificate', 'employees'));
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

    public function json(Request $request)
    {
        if ($request->department_id == 0) {
            $designations = Designation::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        $designations = Designation::where('department_id', $request->department_id)->where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();

        return response()->json($designations);
    }

    public function view($id)
    {
        $users = LoginDetail::find($id);
        return view('employee.user_log', compact('users'));
    }

    public function logindestroy($id)
    {
        $employee = LoginDetail::where('user_id', $id)->delete();

        return redirect()->back()->with('success', 'Employee successfully deleted.');
    }

    public function employeePassword($id)
    {
        $eId = Crypt::decrypt($id);

        $user = User::find($eId);

        $employee = User::where('id', $eId)->first();

        return view('employee.reset', compact('user', 'employee'));
    }

    public function employeePasswordReset(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $user = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'is_login_enable' => 1,
        ])->save();

        return redirect()->route('employee.index')->with(
            'success',
            'Employee Password successfully updated.'
        );
    }
    public function getEmployees(Request $request)
    {
        if ($request->employee_id == 0) {
            $employees = Employee::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
        } else {
            $employees = Employee::where('created_by', '=', Auth::user()->creatorId())->where('id', '!=', $request->employee_id)->get()->pluck('name', 'id');
        }

        return response()->json($employees);
    }

    public function employeeSub(Request $request)
    {
        $subdepartment = SubDepartment::where('department', $request->department_id)->get();

        $output = '<option value="">Select Sub Department</option>';
        foreach ($subdepartment as $sub) {
            $output .= '<option value="' . $sub->id . '">' . $sub->name . '</option>';
        }
        echo $output;
    }

    public function getEmployeesApi(Request $request)
    {
        if ($request->employee_id == 0) {
            $employees = Employee::where('created_by', '=', Auth::user()->creatorId())->get();
        } else {
            $employees = Employee::where('user_id', '=', $request->employee_id)->get();
        }
        
        return response()->json([
            'status' => 'success',
            'message' => __('Employee list get successfully.'),
            'employee' => $employees
        ]);
        // return response()->json($employees);
    }
}
