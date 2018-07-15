<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Events\InventoryAdjusted;

use App\Order;
use App\OrderItem;
use App\OrderStatus;

class ProductInventoryAdjustment extends KwtModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_inventory_adjustments';
    protected $hidden = [];
    
    public function OrderItem()
    {
        return $this->belongsTo('App\OrderItem', 'order_item_id', 'id');
    }
    
    public function Supply()
    {
        return $this->belongsTo('App\Supply', 'supply_id', 'id');
    }
    
    public function OrderShipment()
    {
        return $this->belongsTo('App\OrderShipment', 'order_shipment_id', 'id');
    }
    
    public function OrderReturn()
    {
        return $this->belongsTo('App\OrderReturn', 'order_return_id', 'id');
    }

    public function orderedProducts()
    {
        return $this->hasMany('App\orderItem', 'product_id', 'product_id');
    }

    public static function add($data)
    {
        if ( $data = DB::transaction(function() use ($data)
        {
            if ( $product = Product::where('sku', '=', strtoupper($data['sku']))->first() )
            {
                //Find or Create Inventory
                $inventory = ProductInventory::firstOrCreate([
                    'product_id' => $product->id
                ], [
                    'items_in_stock'    => 0,
                    'items_supplied'    => 0,
                    'items_shipped'     => 0,
                    'items_returned'    => 0,
                    'items_lost_stolen' => 0
                ]);
                
                if ( $data['items_in_stock'] < 0 )
                {
                    $items_lost_stolen = abs(array_key_exists('items_lost_stolen', $data) ? $data['items_lost_stolen'] : $data['items_in_stock']);

                    if ( $inventory->items_in_stock >= $items_lost_stolen )
                    {
                        $items_in_stock = -$items_lost_stolen;
                    }
                    else
                    {
                        $items_in_stock = -$inventory->items_in_stock;
                    }
                }
                else
                {
                    $items_in_stock = $data['items_in_stock'];
                    $items_lost_stolen = 0;
                }
                
                //Create Inventory adjustment
                $inventoryAdjustment = ProductInventoryAdjustment::create([
                    'product_id'        => $product->id,
                    'items_in_stock'    => $items_in_stock,
                    'items_lost_stolen' => $items_lost_stolen,
                    'comment'           => $data['comment']
                ]);
                
                $inventory->update(parent::adjustInventory($inventory, $inventoryAdjustment));
                $inventory->save();

                // revalidate backorder orders which contain specific product
                if ( $data['items_in_stock'] > 0 )
                {
                    $ordered_products = $inventoryAdjustment
                                        ->orderedProducts()
                                        ->select('order_items.*')
                                        ->join('orders', 'orders.id', '=', 'order_items.order_id')
                                        ->where('orders.order_status_id', OrderStatus::$backorder)
                                        ->oldest('orders.created_at')
                                        ->get();

                    if ( $ordered_products )
                    {
                        foreach ( $ordered_products as $ordered_product )
                        {
                            $orderItem = $ordered_product->toArray();
                            $diff = $orderItem['ordered'] - ($orderItem['reserved'] + $orderItem['shipped']);

                            if ( $diff > 0 )
                            {
                                if ( $diff <= $inventory->items_in_stock )
                                {
                                    $quantity = min($diff, $inventory->items_in_stock);

                                    $adjustment = ProductInventoryAdjustment::create([
                                        'product_id'     => $orderItem['product_id'],
                                        'order_id'       => $orderItem['order_id'],
                                        'order_item_id'  => $orderItem['id'],
                                        'items_in_stock' => -$quantity,
                                        'items_reserved' => $quantity
                                    ]);

                                    $inventory->update(parent::adjustInventory($inventory, $adjustment));
                                    $inventory->save();

                                    OrderItem::where('id', $orderItem['id'])->update([
                                        'items_reserved' => ($orderItem['reserved'] + $quantity)
                                    ]);

                                    // Check if all order products are in stock, and change status to confirmed
                                    $order = Order::where('id', $orderItem['order_id'])->with('orderItems')->first();

                                    if ( $order )
                                    {
                                        $all_in_stock = true;

                                        foreach ( $order->orderItems as $order_item )
                                        {
                                            $oItem = $order_item->toArray();

                                            if ( $oItem['ordered'] != ($oItem['reserved'] + $oItem['shipped']) )
                                            {
                                                $all_in_stock = false;
                                            }
                                        }

                                        if ( $all_in_stock )
                                        {
                                            $order->order_status_id = OrderStatus::$confirmed;
                                            $order->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                return [
                    'inventory' => $inventory,
                    'product'   => $product
                ];
            }
            
            return NULL;
        })) {
            //Shoot the inventory adjusted event
            event(new InventoryAdjusted($data['inventory'], $data['product']));

            return $data['inventory'];
        }
        
        return NULL;
    }
}
