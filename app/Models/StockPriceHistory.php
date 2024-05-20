<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPriceHistory extends Model
{
    protected $fillable = ['id', 'stock_id', 'price', 'created_at'];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}