<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Hash;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\KwtController;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends KwtController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'old_password' => 'required|string|min:6',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|min:6|same:password',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return $this->response([], __('Please verify your passwords.'), $validator->errors(), 401);
        }

        $user = User::where('id', $request->user()->id)->first();

        if ($user) {

            if (Hash::check($request->input('old_password'), $user->password) ) {
                $user->password = Hash::make($request->input('password'));

                if($user->save()) {
                    return $this->response($user, __('The password updated successfully.'), [], 200);
                }
            } else {
                return $this->response([], __('Please verify your old password.'), [], 401);
            }
            
        } else {
            return $this->response([], __('User does not exist.'), [], 401);
        }

        return $this->response([], '', [], 500);
    }
}
