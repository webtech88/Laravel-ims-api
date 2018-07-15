<?php

namespace App\Http\Requests;

use App\Http\Requests\IgorFormRequest;
use Auth;

class CreateProduct extends IgorFormRequest
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
            'sku' => 'required|unique:products,sku,NULL,id,deleted_at,NULL|max:50',
            'description' => 'required',
//            'location' => 'required',
        ];
    }
}

