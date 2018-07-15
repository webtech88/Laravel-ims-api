<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Order;
use App\OrderItem;
use App\OrderStatus;
use App\Http\Requests\CreateOrder;
use App\Http\Requests\UpdateStatusOfOrder;
use App\Events\OrderStatusChanged;

class OrderController extends KwtController
{
    public function index(Request $request) {
        return $this->response(Order::all());
    }
    
    /**
     * Retrieves order with related items
     *
     * @param Request $request
     *
     * @return array
     */
    public function show(Request $request) {
        $id = $request->route('id');
        if(preg_match('/^[0-9]+$/', $id)) {
            $order = Order::prepareCustomer()->with('orderItems')->find($id);
        } else {
            $order = Order::prepareCustomer()->with('orderItems')->where('order_reference', $id)->first();
        }
        if($order) {
            return $this->response($order);
        }
        
        return $this->response([], __('Order not found'), [], 404);
    }

    /**
     * Retrieves all possible order statuses
     *
     * @param Request $request
     *
     * @return array
     */
    public function statuses(Request $request)
    {
	    return $this->response(OrderStatus::all());
    }
    
    public function deliver() {
        
    }
    
    /**
     * Place an order
     *
     * @param Request $request
     * @param CreateOrder $validate
     *
     * @return stdClass
     */
    public function create(Request $request, CreateOrder $validate) {
        if ($order = Order::create([
            'order_reference' => $request->input('order_reference'),
            'customer_reference' => $request->input('customer_reference'),
            'customer_first_name' => $request->input('customer_first_name'),
            'customer_last_name' => $request->input('customer_last_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_phone_number' => $request->input('customer_phone_number'),
            'customer_billing_company' => $request->input('customer_billing_company'),
            'customer_billing_first_name' => $request->input('customer_billing_first_name'),
            'customer_billing_last_name' => $request->input('customer_billing_last_name'),
            'customer_billing_address_line1' => $request->input('customer_billing_address_line1'),
            'customer_billing_address_line2' => $request->input('customer_billing_address_line2'),
            'customer_billing_city' => $request->input('customer_billing_city'),
            'customer_billing_county' => $request->input('customer_billing_county'),
            'customer_billing_postcode' => $request->input('customer_billing_postcode'),
            'customer_billing_country_code' => $request->input('customer_billing_country_code'),
            'customer_billing_country' => $request->input('customer_billing_country'),
            'customer_billing_phone_number' => $request->input('customer_billing_phone_number'),
            'customer_delivery_company' => $request->input('customer_delivery_company'),
            'customer_delivery_first_name' => $request->input('customer_delivery_first_name'),
            'customer_delivery_last_name' => $request->input('customer_delivery_last_name'),
            'customer_delivery_address_line1' => $request->input('customer_delivery_address_line1'),
            'customer_delivery_address_line2' => $request->input('customer_delivery_address_line2'),
            'customer_delivery_city' => $request->input('customer_delivery_city'),
            'customer_delivery_county' => $request->input('customer_delivery_county'),
            'customer_delivery_postcode' => $request->input('customer_delivery_postcode'),
            'customer_delivery_country_code' => $request->input('customer_delivery_country_code'),
            'customer_delivery_country' => $request->input('customer_delivery_country'),
            'customer_delivery_phone_number' => $request->input('customer_delivery_phone_number'),
            'delivery_instructions' => $request->input('delivery_instructions', ''),
            'warehouse_instructions' => $request->input('warehouse_instructions', ''),
            'gift_message' => $request->input('gift_message', ''),
            'delivery_method_code' => $request->input('delivery_method_code'),
            'order_lines' => $request->input('order_lines')
        ])) {
            return $this->response($order, __('Order has been placed. Stock updated.'));
        }
    }

    public function status(Request $request, UpdateStatusOfOrder $validate)
    {
        if ( preg_match('/^d+$/', $request->route('id')) )
        {
            $column = 'id';
        }
        else
        {
            $column = 'order_reference';
        }

        if ( $order = Order::where($column, $request->route('id'))->first() )
        {
            $order->order_status_id = $request->input('order_status_id');
            
            if ( $order->save() )
            {
                // Save Order Status changes
                event(new OrderStatusChanged($order, $request->input('comment')));

                return $this->response([], __('Order status has been changed.'));
            }
        }
        else
        {
            return $this->response([], __('Order not found'), [], 404);
        }
        
        return $this->response([], '', [], 500);
    }

    public function orderStatusChanges(Request $request)
    {
        if ( preg_match('/^d+$/', $request->route('id')) )
        {
            $column = 'id';
        }
        else
        {
            $column = 'order_reference';
        }

        if ( $order = Order::where($column, $request->route('id'))->with('orderStatusChanges')->first() )
        {
            return $this->response($order);
        }
        else
        {
            return $this->response([], __('Order not found'), [], 404);
        }
        
        return $this->response([], '', [], 500);
    }
}
