<?php

namespace App\Http\Controllers\Auth;

use Validator;
use DB;
use Cookie;
use Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use App\Http\Controllers\KwtController;
use App\User;

class LoginController extends KwtController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * get user api
     * 
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request)
    {
      return $this->response($request->user(), __('success'), [], 200);
    }

    /**
     * Logout api
     * 
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
      $accessTokens = DB::table('oauth_access_tokens')
                          ->where('user_id', $request->user()->id)
                          ->where('revoked', false)
                          ->get();
      
      if ($accessTokens) {
        foreach($accessTokens as $accessToken) {
          $refreshToken = DB::table('oauth_refresh_tokens')
                              ->where('access_token_id', $accessToken->id)
                              ->update(['revoked' => true]);
        }

        $accessTokens = DB::table('oauth_access_tokens')
                            ->where('user_id', $request->user()->id)
                            ->where('revoked', false)
                            ->update(['revoked' => true]);
      }

      return $this->response([], __('User logged out.'), [], 200);
    }
}
