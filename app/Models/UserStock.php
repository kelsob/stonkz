<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStock extends Model
{
    protected $table = 'user_stocks'; // Explicitly defining the table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'stock_id',
        'shares',
        'purchase_price',
    ];

    /**
     * Get the user that owns the UserStock.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the stock associated with the UserStock.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Additional methods can be defined here to handle business logic,
     * like calculating the value of the stock holding, handling buying or selling logic, etc.
     */

    /**
     * Calculate the current value of the holding.
     *
     * @return float
     */
    public function currentValue(): float
    {
        return $this->shares * $this->stock->price;
    }

    /**
     * Update shares and purchase price when buying more stocks.
     *
     * @param int $quantity
     * @param float $price
     * @return void
     */
    public function buyMoreShares(int $quantity, float $price): void
    {
        $this->shares += $quantity;
        $this->purchase_price = ($this->purchase_price * $this->shares + $price * $quantity) / ($this->shares + $quantity);
        $this->save();
    }
}
