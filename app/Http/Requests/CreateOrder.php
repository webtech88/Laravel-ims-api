<?php

namespace App\Http\Requests;

use App\Http\Requests\IgorFormRequest;
use Auth;

class CreateOrder extends IgorFormRequest
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
            'order_reference' => 'required|unique:orders,order_reference',
//            'customer_reference' => 'required',
            'customer_first_name' => 'required',
            'customer_last_name' => 'required',
            'customer_email' => 'required|email',
            'customer_phone_number' => 'required',
//            'customer_billing_company' => 'required',
            'customer_billing_first_name' => 'required',
            'customer_billing_last_name' => 'required',
            'customer_billing_address_line1' => 'required',
//            'customer_billing_address_line2' => 'required',
            'customer_billing_city' => 'required',
            'customer_billing_county' => 'required',
            'customer_billing_postcode' => 'required',
            'customer_billing_country_code' => 'required',
            'customer_billing_country' => 'required',
            'customer_billing_phone_number' => 'required',
//            'customer_delivery_company' => 'required',
            'customer_delivery_first_name' => 'required',
            'customer_delivery_last_name' => 'required',
            'customer_delivery_address_line1' => 'required',
//            'customer_delivery_address_line2' => 'required',
            'customer_delivery_city' => 'required',
            'customer_delivery_county' => 'required',
            'customer_delivery_postcode' => 'required',
            'customer_delivery_country_code' => 'required',
            'customer_delivery_country' => 'required',
            'customer_delivery_phone_number' => 'required',
//            'delivery_instructions' => 'required',
//            'warehouse_instructions' => 'required',
//            'gift_message' => 'required',
            'delivery_method_code' => 'required',
            'order_lines' => 'required|array|min:1',
            //'order_lines.*.sku' => 'required|exists:products,sku',
            'order_lines.*.sku' => 'required',
            'order_lines.*.quantity_ordered' => 'required|integer|min:0.00000001',
            'order_lines.*.product_description' => 'required',
        ];
    }
    
    public function messages() {
        return [
            'order_lines.*.sku.required' => __('Product SKU is not specified'),
            'order_lines.*.sku.exists' => __('A product you try to order (SKU::input) does not exist.'),
            'order_lines.*.quantity_ordered.min' => __('Incorrect quantity of order line.'),
            'order_lines.*.quantity_ordered.required' => __('Missing quantity for an order line.'),
            'order_lines.*.product_description.required' => __('Missing product description for an order line.'),
        ];
    }

}

