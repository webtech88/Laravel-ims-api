<?php

namespace App\Http\Requests;

use App\Http\Requests\IgorFormRequest;

use Auth;

class UpdateProduct extends IgorFormRequest
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
            'description' => 'required',
//            'location' => 'required',
        ];
    }
}

