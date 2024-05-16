<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Stock;

class StockPriceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stock;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    public function broadcastOn()
    {
        return new Channel('stocks');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->stock->id,
            'price' => $this->stock->price,
        ];
    }
}
