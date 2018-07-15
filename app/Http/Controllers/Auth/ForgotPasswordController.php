<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Hash;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\KwtController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends KwtController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return $this->response([], __('Please verify your email address.'), $validator->errors(), 401);
        }

        $newPassword = $this->generateRamdomString(6);
        $user = User::where('email', $request->input('email'))->first();
        if ($user) {
            $user->update(['password' => Hash::make($newPassword)]);

            return $this->response($user, __('Please check your email.'), [], 200);
        } else {
            return $this->response([], __('Your email does not exist.'), [], 401);
        }
    }

    private function generateRamdomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
