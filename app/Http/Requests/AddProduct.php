<?php

namespace App\Http\Requests;

use App\Http\Requests\IgorFormRequest;
use Auth;

class AddProduct extends IgorFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * @todo Allow only if user is an administrator!
         */
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'items' => 'required|numeric|min:0',
            'comment' => 'required',
        ];
    }
    
    public function messages() {
        return [
            'items.*' => __('The items must be a positive amount.'),
            'comment.*' => __('Reason for adjustment must be described in comment.'),
        ];
    }
}

