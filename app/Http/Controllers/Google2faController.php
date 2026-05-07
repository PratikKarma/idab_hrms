<?php

namespace App\Http\Controllers;

use PragmaRX\Google2FA\Google2FA;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Google2faController extends Controller
{

    public function index()
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user = \Auth::user();
        $user->google2fa_secret = $secret;
        $user->save();
        return redirect()->route('profile')->with('success', __('Secret key generated successfully.'));
    }

    public function enable(Request $request)
    {
        $user = \Auth::user();
        $google2fa = new Google2FA();
        $secret = $request->input('secret');
        $verify = $google2fa->verifyKey($user->google2fa_secret, $secret);
        if ($verify) {
            $user->google2fa_enable = 1;
            $user->save();
            return redirect()->route('profile')->with('success', __('2FA is enabled successfully.'));
        } else {
            return redirect()->back()->with('error', __('Invalid verification code, Please try again.'));
        }
    }

    public function disable()
    {
        $validator = \Validator::make(
            request()->all(),
            [
                'password' => 'required',
            ]
        );



        $user = \Auth::user();
        if (! (\Hash::check(request()->password, $user->password))) {
            return redirect()->route('profile')->with('error', __('Your password does not matches with your account password. Please try again.'));
        }
        $user->google2fa_enable = 0;
        $user->save();
        return redirect()->route('profile')->with('success', __('2FA is now disabled.'));
    }
}
