<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Order;

class OrderPlaced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $order;
    
    public $orderItems;
    
    public $products;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order, $orderItems, $products)
    {
        $this->order = $order;
        $this->orderItems = $orderItems;
        $this->products = $products;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
