<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use League\Csv\Reader;

class ImportStocks extends Command
{
    protected $signature = 'import:stocks';
    protected $description = 'Imports stocks from a CSV file';

    public function __construct() 
    {
        parent::__construct();
    }

    public function handle()
    {
        $csv = Reader::createFromPath(storage_path('public/stocks.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            Stock::create([
                'id' => $record['id'],
                'name' => $record['name'],
                'ticker' => $record['ticker'],
                'price' => $record['price'],
                'motto' => $record['motto'],
                'description' => $record['description'],
            ]);
        }

        $this->info('Stocks imported successfully!');
    }
}