<?php

namespace App;

use Illuminate\Support\Facades\DB;

use App\Customer;
use App\Product;
use App\ProductInventory;
use App\ProductInventoryAdjustment;
use App\OrderItem;
use App\OrderStatus;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;


class Order extends KwtModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orders';
    protected $hidden = [];
    
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
    
    public function orderShipments()
    {
        return $this->hasMany('App\OrderShipment', 'order_id', 'id');
    }
    
    public function orderItems()
    {
		return $this->hasMany('App\OrderItem', 'order_id', 'id');
    }

    public function orderStatusChanges()
    {
        return $this->hasMany('App\OrderStatusChange', 'order_id', 'id')
                    ->select('order_status_changes.*', 'order_statuses.title')
                    ->join('order_statuses', 'order_statuses.id', '=', 'order_status_changes.order_status_id');
    }
    
    public static function prepareCustomer($columns = [])
    {
        return parent::leftJoin('customers', function($join)
        { 
            $join->on('customers.id', '=', 'orders.customer_id'); 
        })
        ->select(array_merge( ['customers.*'], $columns ? $columns : ['orders.*'] ));
    }
    
    /**
     * Returns Product data together with corresponding inventory data
     * 
     * @param array $columns
     * 
     * @return array
     */
    public static function all($columns = [])
    {
        return static::prepareCustomer($columns)->with('orderItems')->get();
    }
    
    /**
     * Creates an Order and respective OrderItems
     * 
     * @param array $data order data including order lines
     * 
     * @return type
     */
    public static function create($data)
    {
        // Prepare the customer
        $customer = Customer::firstOrCreate([
            'customer_reference'    => $data['customer_reference']
        ], [
            'customer_first_name'   => $data['customer_first_name'],
            'customer_last_name'    => $data['customer_last_name'],
            'customer_email'        => $data['customer_email'],
            'customer_phone_number' => $data['customer_phone_number']
        ]);
        
        $data = DB::transaction(function() use ($data, $customer)
        {
            // Create Order
            $order = static::query()->create([
                'order_reference'                 => $data['order_reference'],
                'customer_id'                     => $customer->id,
                'customer_billing_company'        => $data['customer_billing_company'],
                'customer_billing_first_name'     => $data['customer_billing_first_name'],
                'customer_billing_last_name'      => $data['customer_billing_last_name'],
                'customer_billing_address_line1'  => $data['customer_billing_address_line1'],
                'customer_billing_address_line2'  => $data['customer_billing_address_line2'],
                'customer_billing_city'           => $data['customer_billing_city'],
                'customer_billing_county'         => $data['customer_billing_county'],
                'customer_billing_postcode'       => $data['customer_billing_postcode'],
                'customer_billing_country_code'   => $data['customer_billing_country_code'],
                'customer_billing_country'        => $data['customer_billing_country'],
                'customer_billing_phone_number'   => $data['customer_billing_phone_number'],
                'customer_delivery_company'       => $data['customer_delivery_company'],
                'customer_delivery_first_name'    => $data['customer_delivery_first_name'],
                'customer_delivery_last_name'     => $data['customer_delivery_last_name'],
                'customer_delivery_address_line1' => $data['customer_delivery_address_line1'],
                'customer_delivery_address_line2' => $data['customer_delivery_address_line2'],
                'customer_delivery_city'          => $data['customer_delivery_city'],
                'customer_delivery_county'        => $data['customer_delivery_county'],
                'customer_delivery_postcode'      => $data['customer_delivery_postcode'],
                'customer_delivery_country_code'  => $data['customer_delivery_country_code'],
                'customer_delivery_country'       => $data['customer_delivery_country'],
                'customer_delivery_phone_number'  => $data['customer_delivery_phone_number'],
                'delivery_instructions'           => $data['delivery_instructions'],
                'warehouse_instructions'          => $data['warehouse_instructions'],
                'gift_message'                    => $data['gift_message'],
                'delivery_method_code'            => $data['delivery_method_code'],
            ]);
            
            $order->order_status_id = OrderStatus::$confirmed;

            $orderProducts = $orderItems = [];
            
            // Prepare data for every Product in Order
            foreach ( $data['order_lines'] as $orderLine )
            {
	            $product = Product::firstOrCreate([
	            	'sku' => strtoupper($orderLine['sku'])
	            ], [
		            'description' => $orderLine['product_description']
	            ]);
	            
                //if ($product = Product::where('sku', '=', strtoupper($orderLine['sku']))->first()) {
	            if ( $product )
				{
                    $orderProducts[] = $product;
                    
                    // Find or Create Inventory
                    $inventory = ProductInventory::firstOrCreate([
                        'product_id' => $product->id
                    ], [
                        'items_in_stock'    => 0,
                        'items_supplied'    => 0,
                        'items_shipped'     => 0,
                        'items_returned'    => 0,
                        'items_lost_stolen' => 0
                    ]);

                    if ( $inventory->items_in_stock >= $orderLine['quantity_ordered'] )
                    {
                        $items_reserved = $orderLine['quantity_ordered'];
                    }
                    else
                    {
                        // Not enough in stock => order is a backorder
                        $order->order_status_id = OrderStatus::$backorder;
                        $items_reserved = $inventory->items_in_stock;
                    }

                    // Create Order Item
                    $orderItem = OrderItem::create([
                        'product_id'     => $product->id,
                        'order_id'       => $order->id,
                        'items_ordered'  => $orderLine['quantity_ordered'],
                        'items_reserved' => $items_reserved
                    ]);

                    if ( $inventory->items_in_stock > 0 )
                    {
                        // Create Inventory adjustment
                        $inventoryAdjustment = ProductInventoryAdjustment::create([
                            'product_id'     => $orderItem->product_id,
                            'order_id'       => $order->id,
                            'order_item_id'  => $orderItem->id,
                            'items_in_stock' => -$orderItem->items_reserved,
                            'items_reserved' => $orderItem->items_reserved
                        ]);

                        // Adjust Inventory
                        $inventory->update(parent::adjustInventory($inventory, $inventoryAdjustment));
                        $inventory->save();
                    }

                    $orderItems[] = [
                        'order_id'          => $order->id,
                        'product_id'        => $product->id,
                        'ordered'           => $orderItem->items_ordered,
                        'reserved'          => $orderItem->items_reserved,
                        'shipped'           => 0,
                        'sku'               => $product->sku,
                        'description'       => $product->description,
                        'location'          => $product->location,
                        'items_in_stock'    => $inventory->items_in_stock,
                        'items_supplied'    => $inventory->items_supplied,
                        'items_reserved'    => $inventory->items_reserved,
                        'items_shipped'     => $inventory->items_shipped,
                        'items_returned'    => $inventory->items_returned,
                        'items_lost_stolen' => $inventory->items_lost_stolen
                    ];
                }
            }

            // Save Order Status changes
            event(new OrderStatusChanged($order));

            // Order status persisted
            $order->save();
            
            return [
                'order'          => $order,
                'order_items'    => $orderItems,
                'order_products' => $orderProducts,
                'customer'       => $customer
            ];
        });
        
        // Shoot the order placed event
        event(new OrderPlaced($data['order'], $data['order_items'], $data['order_products']));
        return array_merge($data['customer']->toArray(), $data['order']->toArray(), ['order_items' => $data['order_items']]);
    }
    
    public function status_change() {
//        return DB::transaction(function() use ($data) {
//            if ($product = Product::where('sku', '=', strtoupper($data['sku']))->first()) {
//                
//                //Find or Create Inventory
//                $inventory = ProductInventory::firstOrCreate(['product_id' => $product->id]);
//                
//                //Create Supply
//                $orderItem = OrderItem::create([
//                    'product_id' => $product->id,
//                    'order_id' => '',
//                    'items_in_stock' => array_key_exists('items_in_stock', $data) ? $data['items_in_stock'] : $data['items_supplied'],
//                    'items_reserved' => $data['quantity_ordered']
//                ]);
//                
//                //Create Inventory adjustment
//                $inventoryAdjustment = ProductInventoryAdjustment::create([
//                    'product_id' => $supply->product_id,
//                    'supply_id' => $supply->id,
//                    'items_in_stock' => $supply->items_in_stock,
//                    'items_supplied' => $supply->items_supplied
//                ]);
//                
//                //Adjust Inventory
//                if(ProductInventory::where('id', $inventory->id)->update(parent::adjustInventory($inventory, $inventoryAdjustment))) {
//                    return ProductInventory::where('id', $inventory->id)->first();
//                }
//            }
//            
//            return NULL;
//        });
    }
}
