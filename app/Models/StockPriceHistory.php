<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPriceHistory extends Model
{
    protected $fillable = ['stock_id', 'price'];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}