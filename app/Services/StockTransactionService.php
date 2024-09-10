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
            $portfolioStock->average_price = ($portfolioStock->average_price * $portfolioStock->quantity + $price * $quantity) / ($portfolioStock->quantity + $quantity);
            $portfolioStock->quantity += $quantity;
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
    
            // Log the transaction details for debugging
            Log::info("User {$user->id} sold {$quantity} shares of stock {$stock->id} at price {$price}.");
    
            $user->balance += $price * $quantity;
            $user->save();
    
            $portfolio = $user->portfolio;
            $portfolioStock = $portfolio->portfolioStocks()->where('stock_id', $stock->id)->first();
    
            if ($portfolioStock) {
                $portfolioStock->quantity -= $quantity;
    
                // Log the updated stock quantity for debugging
                Log::info("User {$user->id} now owns {$portfolioStock->quantity} shares of stock {$stock->id}.");
    
                if ($portfolioStock->quantity <= 0) {
                    $portfolioStock->delete();
                    // Log if the stock is being removed from the portfolio
                    Log::info("User {$user->id} has no more shares of stock {$stock->id}. Stock has been removed from the portfolio.");
                } else {
                    $portfolioStock->save();
                }
            } else {
                // Log if the portfolioStock is not found
                Log::error("Portfolio stock not found for user {$user->id} and stock {$stock->id}.");
            }
    
            return $transaction;
        });
    }
    
}
