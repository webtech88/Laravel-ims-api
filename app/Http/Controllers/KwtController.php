<?php

namespace App\Http\Controllers;

use Auth;
use App\Helper;
use App\Http\Requests\CreateProduct;
use App\Product;
use Flash;
use Redirect;
use Response;
use Schema;
use DB;

class KwtController extends Controller
{
    /**
     * Unleashes HTTP response
     * 
     * @param mixed $data
     * @param string $message
     * @param array $errors
     * @param integer $code response code
     * 
     * @return Illuminate\Http\Response
     */
    public static function response($data = [], $message = '', $errors = [], $code = 200) {
        //Respond with default 500 error if actual error code was not specified
        $error_code = ($code !== 200) ? $code : (empty($errors) ? $code : 500);
        return response(
            [
                'status' => $error_code < 400 ? 'ok' : 'error',
                'message' => __($message),
                'data' => $data,
                'errors' => $errors
            ],
            $error_code
        );
    }
}
