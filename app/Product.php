<?php

namespace App;

class Product extends KwtModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';
    protected $hidden = [];

    public function orderShipments() {
        return $this->hasMany('App\OrderShipment', 'product_id', 'id');
    }
    
    public function productInventory() {
        return $this->hasOne('App\ProductInventory', 'product_id', 'id' );
    }
    
    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = strtoupper($value);
    }

    /**
     * Prepares query to include product_inventory data as well
     * 
     * @param array $columns
     * 
     * @return Illuminate\Database\Eloquent\Builder
     */
    public static function prepareInventory($columns = []) {
        return parent::leftJoin('product_inventories', function($join) {
                $join->on('products.id', '=', 'product_inventories.product_id');
            })->select(
            array_merge(
                $columns ? $columns : ['products.*'], 
                [
                    'product_inventories.items_in_stock',
                    'product_inventories.items_supplied',
                    'product_inventories.items_reserved',
                    'product_inventories.items_shipped',
                    'product_inventories.items_returned',
                    'product_inventories.items_lost_stolen'
                ])
            );
    }
    
    /**
     * Returns Product data together with corresponding inventory data
     * 
     * @param array $columns
     * 
     * @return array
     */
    public static function all($columns = []) {
        return static::prepareInventory($columns)->get();
    }
    
    /**
     * Serialization of Product data
     * 
     * @return array
     */
    public function toArray() {
        
        $data = parent::toArray();
        
        //Make sure values are casted float first
        array_walk($data, function($value, $key) use (&$data){
            if(in_array($key, [
                'items_in_stock',
                'items_supplied',
                'items_reserved',
                'items_shipped',
                'items_returned',
                'items_lost_stolen'
            ])) {
                $data[$key] = floatval($value);
            }
        });
        
        return $data;
    }

}
