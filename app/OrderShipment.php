<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderShipment extends KwtModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_shipments';
    protected $hidden = [];
}
