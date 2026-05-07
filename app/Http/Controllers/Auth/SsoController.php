<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Mail\SsoLoginMail;
use App\Models\Employee;
use App\Providers\RouteServiceProvider;

class SsoController extends Controller
{
    public function ssoLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid user email'
            ], 401);
        }

        // Optional: check active/disabled flags
        // Treat `is_active == 0` or `is_disable == 1` as not allowed to login
        if (isset($user->is_active) && $user->is_active == 0) {
            return response()->json([
                'status' => false,
                'message' => 'User is inactive'
            ], 403);
        }

        if (isset($user->is_disable) && $user->is_disable == 0) {
            return response()->json([
                'status' => false,
                'message' => 'User is disabled'
            ], 403);
        }

        // Login user
        Auth::login($user);
        if ($user->type == "employee") {
            $user->shift = Employee::where('user_id', $user->id)->with(['shift'])->first();
        }
        // Create API token (Sanctum)
        $token = $user->createToken('SSO API Token')->plainTextToken;
        if($user->type != 'company'){
            $dataUser = User::where('created_by', $user->id)->first();
            $companyUserId = $dataUser ? $dataUser->id : $user->created_by;
        }else{
            $companyUserId = $user->id;
        }

        if (isset($companyUserId)) {
            // $user->CompanyUserID = $companyUserId;
            $user->loginDelayTime = DB::table('settings')->where([['created_by', (int)$companyUserId],['name', 'login_deley_min']])->first('value');
            $user->logoutDelayTime = DB::table('settings')->where([['created_by', $companyUserId],['name', 'logout_lead_time']])->first('value');
        }

        return response()->json([
            'status' => true,
            'message' => 'SSO Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }
}
