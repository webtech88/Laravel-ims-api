<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\KwtController;
use Illuminate\Http\JsonResponse;

use Auth;

class IgorFormRequest extends FormRequest {

    /**
     * Overrides default FormRequest validation errors
     * 
     * @param Validator $validator
     * 
     * @throws Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator) {
        throw (new ValidationException($validator, KwtController::response([], __('The given data was invalid.'), $validator->getMessageBag(), 400)))->status(400);
    }

}
