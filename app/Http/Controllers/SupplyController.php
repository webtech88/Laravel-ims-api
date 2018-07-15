<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Supply;
use App\Http\Requests\SupplyExistingProduct;

class SupplyController extends KwtController
{
    
    /**
     * Retrieves the history product supplies
     * 
     * @param Request $request
     * @return array data to form JSON response from
     */
    public function index(Request $request) {
        return $this->response(Supply::all());
    }
    
    /**
     * Supply a Product
     * @param Request $request
     * @param SupplyExistingProduct $validate
     * @return mixed data to form JSON response from
     */
    public function create(Request $request, SupplyExistingProduct $validate) {
        if ($supply = Supply::create([
            'sku' => $request->input('sku'),
            'items_supplied' => $request->input('items_supplied')
        ])) {
            return $this->response($supply, __('Item Supply has been recorded. Stock updated.'));
        }
    }
}