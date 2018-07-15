<?php

namespace App\Http\Requests;

use App\Http\Requests\IgorFormRequest;
use Auth;

class SupplyExistingProduct extends IgorFormRequest
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
            'sku' => 'required|max:50|exists:products,sku',
            'items_supplied' => 'required|numeric|min:0.00000001'
        ];
    }
    
    public function messages() {
        return [
            'items_supplied.min' => __('Incorrect quantity of product.')
        ];
    }
}

