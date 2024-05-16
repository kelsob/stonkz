<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockTransactionService
{
    public function buyStock(User $user, Stock $stock, int $quantity, float $price): Transaction
    {
        // Check if the user has enough funds
        $totalCost = $price * $quantity;
        if ($user->balance < $totalCost) {
            throw new \Exception("Insufficient funds.");
        }

        // Check stock availability or other conditions
        // Assuming we handle stock quantities, this part of the logic would go here

        // If all checks pass, perform the transaction
        return DB::transaction(function () use ($user, $stock, $quantity, $price) {
            $transaction = new Transaction([
                'user_id' => $user->id,
                'stock_id' => $stock->id,
                'quantity' => $quantity,
                'price' => $price,
                'type' => 'buy'
            ]);
            $transaction->save();

            // Update user's balance
            $user->balance -= $price * $quantity;
            $user->save();

            return $transaction;
        });
    }

    public function sellStock(User $user, Stock $stock, int $quantity, float $price): Transaction
    {
        // Similar checks and logic for selling stocks
        // Ensure the user has the stock and the right quantity to sell
        // Perform the transaction within a database transaction

        return DB::transaction(function () use ($user, $stock, $quantity, $price) {
            $transaction = new Transaction([
                'user_id' => $user->id,
                'stock_id' => $stock->id,
                'quantity' => -$quantity,  // Negative to indicate selling
                'price' => $price,
                'type' => 'sell'
            ]);
            $transaction->save();

            // Update user's balance
            $user->balance += ($price * $quantity);
            $user->save();

            return $transaction;
        });
    }
}