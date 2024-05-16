<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    public $timeScale = "1D";
    public $priceHistories;
    protected $fillable = ['name', 'ticker', 'price', 'motto', 'description'];
    protected $dates = ['deleted_at'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_stocks')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(StockPriceHistory::class);
    }

    public function fetchDataForScale()
    {
        // Fetch the price histories based on the given time scale
        $this->priceHistories = $this->priceHistories()
            ->whereBetween('created_at', $this->getTimeRange($this->timeScale))
            ->orderBy('created_at', 'asc')
            ->get(['price', 'created_at']);
    
        // Check if there are any price histories retrieved
        if ($this->priceHistories->isNotEmpty()) {
            $firstPrice = $this->priceHistories->first()->price;
            $lastPrice = $this->priceHistories->last()->price;
            $this->priceDifference = $lastPrice - $firstPrice;
    
            // Avoid division by zero by checking if the first price is not zero
            if ($firstPrice == 0) {
                $this->percentageDifference = 0;
            } else {
                $this->percentageDifference = ($this->priceDifference / $firstPrice) * 100;
            }
        } else {
            // Set defaults if no data is found
            $this->priceDifference = 0;
            $this->percentageDifference = 0;
        }
    }

    public function getChartData($scale = '1D')
    {
        // Set the time scale and fetch the data
        $this->timeScale = $scale;
        $this->fetchDataForScale();
    
        $dateFormat = match ($this->timeScale) {
            '1H' => 'H:i:s',      // Hour:Minute:Second for hourly data
            '1D' => 'M d, H:i',   // Month Day, Hour:Minute for daily data
            '1W' => 'M d',        // Month Day for weekly data
            '1M' => 'M Y',        // Month Year for monthly data
            '1Y' => 'Y'           // Year for yearly data
        };

        // Extract labels and data for the chart
        $labels = $this->priceHistories->map(function ($entry) use ($dateFormat) {
            return $entry->created_at->format($dateFormat); // Format date as you prefer
        })->toArray();
    
        $data = $this->priceHistories->map(function ($entry) {
            return $entry->price;
        })->toArray();
    
        // JSON encode the labels and data for passing to the frontend
        $labelsJson = json_encode($labels);
        $dataJson = json_encode($data);
    
        // Calculate additional details needed for the display
        $lastPrice = optional($this->priceHistories->last())->price ?? 0;
        $firstPrice = optional($this->priceHistories->first())->price ?? 0;
        $priceDifference = $lastPrice - $firstPrice;
        $percentageDifference = $firstPrice != 0 ? ($priceDifference / $firstPrice) * 100 : 0;
    
        // Format the display of the prices and differences
        return [
            'labelsJson' => $labelsJson,
            'dataJson' => $dataJson,
            'currentPrice' => number_format($lastPrice, 2),
            'priceDifference' => number_format(abs($priceDifference), 2),
            'priceDifferenceSign' => $priceDifference >= 0 ? '+' : '-',
            'percentageDifference' => number_format(abs($percentageDifference), 2),
            'percentageDifferenceSign' => $percentageDifference >= 0 ? '+' : '-',
            'priceColorClass' => $priceDifference >= 0 ? 'text-green-600' : 'text-red-600',
        ];
    }
    



    private function getTimeRange($scale)
    {
        $today = Carbon::now();
        switch ($scale) {
            case '1H':
                return ['start' => $today->copy()->subHour(), 'end' => $today];
            case '1D':
                return ['start' => $today->copy()->subDay(), 'end' => $today];
            case '1W':
                return ['start' => $today->copy()->subWeek(), 'end' => $today];
            case '1M':
                return ['start' => $today->copy()->subMonth(), 'end' => $today];
            case '1Y':
                return ['start' => $today->copy()->subYear(), 'end' => $today];
            default:
                return ['start' => $today->copy()->subDay(), 'end' => $today];
        }
    }
    
}
