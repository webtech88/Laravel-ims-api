<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends KwtModel
{
    /**
     * Bunch of order statuses pointing to the database id of each.
     */
    public static $backorder = 1;
    public static $confirmed = 2;
    public static $picking = 3;
    public static $packing = 4;
    public static $packed = 5;
    public static $dated_delivery = 6;
    public static $dispatched = 7;
    public static $delivered = 8;
    
    /**
     * Retrieves human friendly status of order
     * 
     * @param integer $statusId
     * @return string
     */
    public static function decode($statusId) {
        if($st = self::find($statusId)) {
            return __($st->title);
        }
        return __('Undefined');
    }
}
