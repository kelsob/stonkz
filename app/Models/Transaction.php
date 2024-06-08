<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'id', 'user_id', 'stock_id', 'quantity', 'price', 'type', 'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Handle buying stocks.
     *
     * @param int $userId
     * @param int $stockId
     * @param int $quantity
     * @param float $price
     */
    public static function buy($userId, $stockId, $quantity, $price)
    {
        $transaction = new self([
            'user_id' => $userId,
            'stock_id' => $stockId,
            'quantity' => $quantity,
            'price' => $price,
            'type' => 'buy'
        ]);
        $transaction->save();
    }

    /**
     * Handle selling stocks.
     *
     * @param int $userId
     * @param int $stockId
     * @param int $quantity
     * @param float $price
     */
    public static function sell($userId, $stockId, $quantity, $price)
    {
        $transaction = new self([
            'user_id' => $userId,
            'stock_id' => $stockId,
            'quantity' => -$quantity, // Negative quantity for selling
            'price' => $price,
            'type' => 'sell'
        ]);
        $transaction->save();
    }
}
