<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratePriceHistories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:generate-histories {count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate random price histories for each stock.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ask if the user wants to truncate the existing data
        if ($this->confirm('Do you wish to truncate existing price histories?')) {
            \App\Models\StockPriceHistory::truncate();
            $this->info('All existing price histories have been deleted.');
        }
    
        $count = $this->argument('count');
        $stocks = \App\Models\Stock::all();  // Assuming you have a Stock model
    
        foreach ($stocks as $stock) {
            $lastPrice = $stock->price; // Start with the current stock price
    
            for ($i = 0; $i < $count; $i++) {
                $change = rand(-10, 10); // Random increase or decrease by up to 10 units
                $newPrice = $lastPrice + $change;
                $lastPrice = $newPrice; // Update last price for the next iteration
    
                $priceHistory = new \App\Models\StockPriceHistory([
                    'stock_id' => $stock->id,
                    'price' => $newPrice,
                    'recorded_at' => now()->subMinutes(15 * $i),  // Move backward by 15 seconds per step
                ]);
                $priceHistory->save();
            }
        }
    
        $this->info("Successfully generated $count price histories for each stock.");
    }    
}
