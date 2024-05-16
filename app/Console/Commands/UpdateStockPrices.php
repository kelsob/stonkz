<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;

class UpdateStockPrices extends Command
{
    protected $signature = 'stocks:update';
    protected $description = 'Updates stock prices';

    public function handle()
    {
        error_log("update stock prices.");

        $stocks = Stock::all();
        foreach ($stocks as $stock) {
            $oldPrice = $stock->price;
            $newPrice = $oldPrice + rand(-10, 10); // Random increase or decrease by up to 10 units
            $stock->price = $newPrice;
            $stock->save();

            // Save price history
            $stock->priceHistories()->create(['price' => $newPrice]);

            // Broadcast the update
            event(new \App\Events\StockPriceUpdated($stock));
        }

        $this->info('Stock prices updated successfully.');
    }
}