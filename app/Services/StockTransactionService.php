<?php
namespace App\Services;

use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PortfolioStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockTransactionService
{
    public function buyStock(User $user, Stock $stock, int $quantity, float $price): Transaction
    {
        $totalCost = $price * $quantity;
        if ($user->balance < $totalCost) {
            throw new \Exception("Insufficient funds.");
        }

        return DB::transaction(function () use ($user, $stock, $quantity, $price) {
            $transaction = new Transaction([
                'user_id' => $user->id,
                'stock_id' => $stock->id,
                'quantity' => $quantity,
                'price' => $price,
                'type' => 'buy'
            ]);
            $transaction->save();

            $user->balance -= $price * $quantity;
            $user->save();

            $portfolio = $user->portfolio;
            $portfolioStock = $portfolio->portfolioStocks()->firstOrNew(['stock_id' => $stock->id]);
            $portfolioStock->quantity += $quantity;
            $portfolioStock->average_price = ($portfolioStock->average_price * $portfolioStock->quantity + $price * $quantity) / ($portfolioStock->quantity + $quantity);
            $portfolioStock->save();

            return $transaction;
        });
    }

    public function sellStock(User $user, Stock $stock, int $quantity, float $price): Transaction
    {
        return DB::transaction(function () use ($user, $stock, $quantity, $price) {
            $transaction = new Transaction([
                'user_id' => $user->id,
                'stock_id' => $stock->id,
                'quantity' => -$quantity,
                'price' => $price,
                'type' => 'sell'
            ]);
            $transaction->save();

            $user->balance += $price * $quantity;
            $user->save();

            $portfolio = $user->portfolio;
            $portfolioStock = $portfolio->portfolioStocks()->where('stock_id', $stock->id)->first();
            if ($portfolioStock) {
                $portfolioStock->quantity -= $quantity;
                if ($portfolioStock->quantity <= 0) {
                    $portfolioStock->delete();
                } else {
                    $portfolioStock->save();
                }
            }

            return $transaction;
        });
    }
}
