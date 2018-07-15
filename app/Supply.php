<?php

namespace App;

use Illuminate\Support\Facades\DB;
use App\Product;
use App\ProductInventory;
use App\ProductInventoryAdjustment;
use App\Events\InventoryAdjusted;

class Supply extends KwtModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'supplies';
    protected $hidden = [];

    /**
     * Creates a new Supply database entry and adjusts stock numbers
     * 
     * @param array $data sku and items_in_stock of supplied Product
     * @return array Product inventory updated data
     */
    public static function create($data) {
        if($data = DB::transaction(function() use ($data) {
            if ($product = Product::where('sku', '=', strtoupper($data['sku']))->first()) {
                
                //Find or Create Inventory
                $inventory = ProductInventory::firstOrCreate(['product_id' => $product->id]);
                
                //Create Supply
                $supply = static::query()->create([
                    'product_id' => $product->id,
                    'items_in_stock' => array_key_exists('items_in_stock', $data) ? $data['items_in_stock'] : $data['items_supplied'],
                    'items_supplied' => $data['items_supplied']
                ]);
                
                //Create Inventory adjustment
                $inventoryAdjustment = ProductInventoryAdjustment::create([
                    'product_id' => $supply->product_id,
                    'supply_id' => $supply->id,
                    'items_in_stock' => $supply->items_in_stock,
                    'items_supplied' => $supply->items_supplied
                ]);
                
                //Adjust Inventory
                if(ProductInventory::where('id', $inventory->id)->update(parent::adjustInventory($inventory, $inventoryAdjustment))) {
                    $inventory = ProductInventory::where('id', $inventory->id)->first();
                }
                
                return [
                    'inventory' => $inventory,
                    'product' => $product
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
