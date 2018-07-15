<?php

namespace App\Http\Requests;

use App\Http\Requests\IgorFormRequest;
use Auth;

class UpdateStatusOfOrder extends IgorFormRequest
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
            //'sku' => 'required|unique:products|max:50',
            'order_status_id' => 'required|numeric|exists:order_statuses,id'
        ];
    }
}

