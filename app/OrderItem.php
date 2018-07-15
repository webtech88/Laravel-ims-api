<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\ProductInventoryAdjustment;

class OrderItem extends KwtModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_items';
    protected $hidden = [];

    public function product()
	{
		return $this->belongsTo('App\Product', 'product_id', 'id')->with('productInventory');
	}

	public function order()
	{
		return $this->belongsTo('App\Order', 'order_id', 'id');
	}

    public function toArray()
	{
		$attributes = $this->getAttributes();

		$order_item = [
			'id'         => $attributes['id'],
			'order_id'   => $attributes['order_id'],
			'product_id' => $attributes['product_id'],
			'ordered'    => $attributes['items_ordered'],
			'reserved'   => $attributes['items_reserved'],
			'shipped'    => 0
		];

		if ( $this->product )
		{
			$order_item = array_merge($order_item, [
				'sku'               => $this->product->sku,
				'description'       => $this->product->description,
				'location'          => $this->product->location,
				'items_in_stock'    => 0,
				'items_supplied'    => 0,
				'items_reserved'    => 0,
				'items_shipped'     => 0,
				'items_returned'    => 0,
				'items_lost_stolen' => 0
			]);

			if ( $this->product->productInventory )
			{
				$order_item = array_merge($order_item, [
					'items_in_stock'    => $this->product->productInventory->items_in_stock,
					'items_supplied'    => $this->product->productInventory->items_supplied,
					'items_reserved'    => $this->product->productInventory->items_reserved,
					'items_shipped'     => $this->product->productInventory->items_shipped,
					'items_returned'    => $this->product->productInventory->items_returned,
					'items_lost_stolen' => $this->product->productInventory->items_lost_stolen
				]);
			}

			if ( $adjustments = ProductInventoryAdjustment::where('order_item_id', $attributes['id'])->get() )
			{
				$order_item['shipped'] = $adjustments->sum('items_shipped');
			}
		}

		return $order_item;
	}
}
