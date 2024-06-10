<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioStock extends Model
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
