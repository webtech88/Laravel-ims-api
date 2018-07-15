<?php

namespace App\Listeners;

use App\Events\Event;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\OrderPlaced;
use App\Events\InventoryAdjusted;
use App\Product;

class InventoryAdjustmentSubscriber
{
    
    /**
     * Receives an array of products stock numbers are updated for
     * 
     * @param array $products
     */
    private function makeClientsCallbacks($products) {
        $skus = array_map(function($product){return $product->sku;}, $products);
//        dd($skus);
//        echo 'callback to an every involved client goes here';
    }
    
    /**
     * Handles product inventory adjustment event
     * 
     * @param InventoryAdjusted $event
     * 
     * @return void
     */
    public function onInventoryAdjustment(InventoryAdjusted $event) {
        /**
         * @todo Add event listener logic for shipping back orders
         */
        return $this->makeClientsCallbacks([
            Product::where('id', $event->productInventory->product_id)->first()
        ]);
    }
    
    /**
     * Handles OrderPlaced event
     * 
     * @param OrderPlaced $event
     * 
     * @return void
     */
    public function onOrderPlaced(OrderPlaced $event) {
        return $this->makeClientsCallbacks($event->products);
    }
    
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\InventoryAdjusted',
            'App\Listeners\InventoryAdjustmentSubscriber@onInventoryAdjustment'
        );

        $events->listen(
            'App\Events\OrderPlaced',
            'App\Listeners\InventoryAdjustmentSubscriber@onOrderPlaced'
        );
    }
}
