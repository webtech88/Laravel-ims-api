<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\OrderStatusChange;

class SaveOrderStatusChange
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderStatusChanged  $event
     * @return void
     */
    public function handle(OrderStatusChanged $event)
    {
        OrderStatusChange::create([
            'order_id'        => $event->order->id,
            'order_status_id' => $event->order->order_status_id,
            'comment'         => $event->comment
        ]);
    }
}
