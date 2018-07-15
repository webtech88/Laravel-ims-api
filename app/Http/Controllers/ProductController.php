<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Product;
use App\ProductInventoryAdjustment;
use App\Http\Requests\CreateProduct;
use App\Http\Requests\UpdateProduct;
use App\Http\Requests\AdjustProduct;
use App\Http\Requests\AddProduct;
use App\Rules\DoesNotExceedStock;


class ProductController extends KwtController {
    
    /**
     * Available Products index
     * 
     * @param Request $request
     * 
     * @return array
     */
    public function index(Request $request) {
        $params = $request->all();
        if(array_key_exists('sku', $params)) {
            $skus = (array)$params['sku'];
        } elseif (array_key_exists('skus', $params)) {
            $skus = (array)$params['skus'];
        }
        if(isset($skus)) {
            $products = Product::prepareInventory()->whereIn('sku', $skus)->get();
        } else {
            $products = Product::all();
        }
        return $this->response($products);
    }
    
    /**
     * Retrieve data for individual Product
     * 
     * @param Request $request
     * 
     * @return array
     */
    public function show(Request $request) {
        if($product = Product::prepareInventory()
            ->where(['products.sku' => $request->route('sku')])
            ->first()) 
        {
            return $this->response($product);
        }
        return $this->response([], __('Product not found'), [], 404);
    }

    /**
     * Creates a Product
     * 
     * @param Request $request
     * @param CreateProduct $validate
     * 
     * @return array
     */
    public function create(Request $request, CreateProduct $validate)
    {
        if ( $product = Product::onlyTrashed()->where('sku', $request->input('sku'))->first() )
        {
            if ( $product->restore() )
            {
                $product->description = $request->input('description');
                $product->location    = $request->input('location');

                if ( $product->save() )
                {
                    return $this->response($product, __('Product has been restored.'));
                }
            }
        }
        else
        {
            $data = [
                'sku'         => strtoupper($request->input('sku')),
                'description' => $request->input('description'),
                'location'    => $request->input('location')
            ];

            if ( $product = Product::create($data) )
            {
                return $this->response($product, __('Product has been created.'));
            }
        }

        return $this->response([], '', [], 500);
    }
    
    /**
     * Updates a product
     * 
     * @param Request $request
     * @param UpdateProduct $validate
     * 
     * @return array
     */
    public function update(Request $request, UpdateProduct $validate)
    {
        if ( $product = Product::prepareInventory()->where('sku', $request->route('sku'))->first() )
        {
            $product->description = $request->input('description');

            if ( $request->input('location') )
            {
                $product->location = $request->input('location');
            }

            if ( $product->save() )
            {
                return $this->response($product, __('Product has been updated.'));
            }
        }
        else
        {
            return $this->response([], __('Product was not found.'), [], 404);
        }

        return $this->response([], '', [], 500);
    }
    
    /**
     * Deletes (softly) a Product
     * 
     * @param Request $request
     * 
     * @return array
     */
    public function delete(Request $request)
    {
        if ( $product = Product::prepareInventory()->where('sku', $request->route('sku'))->first() )
        {
            if ( $product->delete() )
            {
                //$product->sku = $product->sku . '_deleted_' . time() . '_' . str_random();
                //$product->save();

                return $this->response($product, __('Product has been deleted.'));
            }
        }
        else
        {
            return $this->response([], __('Product was not found.'), [], 404);
        }

        return $this->response([], '', [], 500);
    }
    
    /**
     * Allows to manually adjust amount of Product in stock
     * 
     * @param Request $request
     * @param AddProduct $validate
     * 
     * @return array
     */
    public function adjust(Request $request, AdjustProduct $validate)
    {
        $items = (int)$request->input('items');

        if ( $items > 0 || $request->validate(['items' => ['required', new DoesNotExceedStock($request->route('sku'))]]) )
        {
            $adjustment = ProductInventoryAdjustment::add([
                'sku'            => $request->route('sku'),
                'items_in_stock' => $items,
                'comment'        => $request->input('comment')
            ]);

            if ( $adjustment )
            {
                return $this->response($adjustment, __('Stock has been adjusted.'));
            }
        }
    }
    
    /**
     * Retrieves the backlog of inventory adjustments for a product
     * 
     * @param Request $request
     * @param AdjustProduct $validate
     * 
     * @return Illuminate\Http\Response
     */
    public function inventory(Request $request) {
        if (empty($request->route('sku'))) {
            return $this->response([], __('SKU must be specified.'), [], 400);
        }
        $response = [];
        if (empty($response['product'] = Product::prepareInventory()->where('sku', $request->route('sku'))->first())) {
            return $this->response([], __('Product not found.'), [], 404);
        }
        $response['product_inventory_adjustments'] = ProductInventoryAdjustment::where('product_id', $response['product']->id)
                ->orderBy('id', 'asc')->with([
                    'OrderItem', 
                    'Supply',
                    'OrderShipment',
                    'OrderReturn'
                ])
                ->get();
        return $this->response($response);
    }

}
