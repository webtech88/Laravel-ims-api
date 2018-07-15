<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends KwtModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_inventories';
    protected $hidden = [];
}
