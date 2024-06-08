<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;


class PortfolioStock extends Pivot
{
    use HasFactory;

    protected $fillable = ['portfolio_id', 'stock_id', 'quantity', 'average_price'];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
