<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Product;
use App\ProductInventory;

class DoesNotExceedStock implements Rule
{
    private $product;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($sku)
    {
        $this->product = Product::where('sku', $sku)->with('ProductInventory')->first();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $items_in_stock = 0;

        if ( $this->product )
        {
            if ( $this->product->productInventory )
            {
                $items_in_stock = $this->product->productInventory->items_in_stock;
            }
        }

        return abs($value) > $items_in_stock ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __($this->product->sku . '. There is not enough product in stock.');
    }
}
