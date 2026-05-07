<?php

namespace App\Http\Controllers\Auth;

use App\Events\VerifyReCaptchaToken;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\User;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use app\Models\Plan;
use App\Models\GenerateOfferLetter;
use App\Models\JoiningLetter;
use App\Models\ExperienceCertificate;
use App\Models\NOC;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */

    public function __construct()
    {
        // $this->middleware('guest');
    }

    public function create($lang = '')
    {
        if ($lang == '') {
            $lang = \App\Models\Utility::getValByName('default_language');
        }

        \App::setLocale($lang);
        return view('auth.register', compact('lang'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $settings = \App\Models\Utility::settings();
        $validation = [];
        if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes') {
            if ($settings['google_recaptcha_version'] == 'v2-checkbox') {
                $validation['g-recaptcha-response'] = 'required';
            } elseif ($settings['google_recaptcha_version'] == 'v3') {
                $result = event(new VerifyReCaptchaToken($request));


                if (!isset($result[0]['status']) || $result[0]['status'] != true) {
                    $key = 'g-recaptcha-response';
                    $request->merge([$key => null]); // Set the key to null

                    $validation['g-recaptcha-response'] = 'required';
                }
            } else {
                $validation = [];
            }
        } else {
            $validation = [];
        }
        $validator = Validator::make(
            $request->all(),
            $validation
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $default_language = \DB::table('settings')->select('value')->where('name', 'default_language')->first();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $passcode = random_int(100000, 999999);
        do {
            $code = rand(100000, 999999);
        } while (User::where('referral_code', $code)->exists());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'company',
            'lang' => !empty($default_language) ? $default_language->value : '',
            'plan' => 1,
            'referral_code' => $code,
            'used_referral_code' => $request->ref_code,
            'created_by' => 1,
            'passcode' => $passcode
        ]);

        // event(new Registered($user));

        Auth::login($user);

        if ($settings['email_verification'] == 'off') {
            try {
                $uArr = [
                    'email' => $request->email,
                    'password' => $request->password,
                    'passcode' => $user->passcode
                ];
                Utility::sendEmailTemplate('new_user', [$user->email], $uArr);
            } catch (\Throwable $th) {
            }
        }

        if ($settings['email_verification'] == 'on') {

            try {
                Utility::getSMTPDetails(1);
                // event(new Registered($user));
                $user->sendEmailVerificationNotification();
                $role_r = Role::findByName('company');
                $user->assignRole($role_r);
                // $user->userDefaultData($user->id);
                $user->userDefaultDataRegister($user->id);
                GenerateOfferLetter::defaultOfferLetterRegister($user->id);
                ExperienceCertificate::defaultExpCertificatRegister($user->id);
                JoiningLetter::defaultJoiningLetterRegister($user->id);
                NOC::defaultNocCertificateRegister($user->id);
            } catch (\Exception $e) {
                $user->delete();
                return redirect('/register')->with('status', __('Email SMTP settings does not configured so please contact to your site admin.'));
            }
            if ($request->plan_id != 0) {
                return redirect()->route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($request->plan_id));
            } else {
                return view('auth.verify-email');
            }
        } else {

            $user->email_verified_at = date('h:i:s');
            $user->save();
            $role_r = Role::findByName('company');

            $user->assignRole($role_r);
            // $user->userDefaultData($user->id);
            $user->userDefaultDataRegister($user->id);
            GenerateOfferLetter::defaultOfferLetterRegister($user->id);
            ExperienceCertificate::defaultExpCertificatRegister($user->id);
            JoiningLetter::defaultJoiningLetterRegister($user->id);
            NOC::defaultNocCertificateRegister($user->id);
            if ($request->plan_id != 0) {
                return redirect()->route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($request->plan_id));
            } else {
                return redirect(RouteServiceProvider::HOME);
            }
        }
    }

    public function showRegistrationForm($ref = '', $plan_id = '', $lang = '')
    {
        if (empty($lang)) {
            $lang = Utility::getValByName('default_language');
        }

        App::setLocale($lang);
        if (Utility::getValByName('disable_signup_button') == 'on') {
            if ($ref == '') {
                $ref = 0;
            }

            if ($plan_id == '') {
                $plan_id = 0;
            }

            $refCode = User::where('referral_code', '=', $ref)->first();
            if (isset($refCode) && $refCode->referral_code != $ref) {
                return redirect()->route('register');
            }

            $setting = \Modules\LandingPage\Entities\LandingPageSetting::settings();

            return view('auth.register', compact('lang', 'ref', 'plan_id', 'setting'));
        } else {
            return abort('404', 'Page not found');
        }
    }

    public function companyStore(Request $request)
    {
        $settings = Utility::settings();

        $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['nullable', Rules\Password::defaults()],
        ]);

        do {
            $code = rand(100000, 999999);
        } while (User::where('referral_code', $code)->exists());
        $random_password = $request->password ?? Str::random(8);
        $passcode = random_int(100000, 999999);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($random_password),
            'type' => 'company',
            'lang' => !empty($default_language) ? $default_language->value : '',
            'plan' => 1,
            'referral_code' => $code,
            'used_referral_code' => 0,
            'created_by' => 1,
            'passcode' => $passcode
        ]);

        Auth::login($user);

        if ($settings['email_verification'] == 'off') {
            try {
                $uArr = [
                    'email' => $request->email,
                    'password' => $request->password,
                    'passcode' => $user->passcode
                ];
                Utility::sendEmailTemplate('new_user', [$user->email], $uArr);
            } catch (\Throwable $th) {
            }
        }
        $user->email_verified_at = date('h:i:s');
        $user->save();
        $role_r = Role::findByName('company');
        $user->assignRole($role_r);
        // $user->userDefaultData($user->id);
        $user->userDefaultDataRegister($user->id);
        GenerateOfferLetter::defaultOfferLetterRegister($user->id);
        ExperienceCertificate::defaultExpCertificatRegister($user->id);
        JoiningLetter::defaultJoiningLetterRegister($user->id);
        NOC::defaultNocCertificateRegister($user->id);
        return response()->json([
            'status' => 'success',
            'messsage' => __('Company registered successfully.'),
            'company' => $user
        ]);
    }

    public function employeeStore(Request $request)
    {
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
            return response()->json([
                'status' => 'error',
                'messsage' => $messages->first()
            ]);
        }

        $objUser = User::find(Auth::user()->creatorId());
        $total_employee = $objUser->countEmployees();
        $plan = Plan::find($objUser->plan);
        $date = date("Y-m-d H:i:s");
        $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->where('created_by', Auth::user()->creatorId())->first();

        // new company default language
        if ($default_language == null) {
            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();
        }

        if ($total_employee < $plan->max_employees || $plan->max_employees == -1) {
            $passcode = random_int(100000, 999999);
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
            return response()->json([
                'status' => 'error',
                'messsage' => __('Your employee limit is over, Please upgrade plan.')
            ]);
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
                'employee_passcode' => $user->passcode
            ];
            $resp = Utility::sendEmailTemplate('new_employee', [$user->id => $user->email], $uArr);

            return response()->json([
                'status' => 'success',
                'messsage' => __('Employee registered successfully.'),
                'employee' => $user
            ]);
        }
        return response()->json([
            'status' => 'success',
            'messsage' => __('Employee registered successfully.'),
            'employee' => $user
        ]);
    }
}
