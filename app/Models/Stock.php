<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    public $timeScale = "1D";
    public $priceHistories;
    protected $fillable = ['name', 'ticker', 'price', 'motto', 'description', 'volatility'];
    protected $dates = ['deleted_at'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(StockPriceHistory::class);
    }

    public function portfolioStocks()
    {
        return $this->hasMany(PortfolioStock::class);
    }

    public function fetchDataForScale()
    {
        $this->priceHistories = $this->priceHistories()
            ->whereBetween('created_at', $this->getTimeRange($this->timeScale))
            ->orderBy('created_at', 'asc')
            ->get(['price', 'created_at']);
    
        if ($this->priceHistories->isNotEmpty()) {
            $firstPrice = $this->priceHistories->first()->price;
            $lastPrice = $this->priceHistories->last()->price;
            $this->priceDifference = $lastPrice - $firstPrice;
    
            if ($firstPrice == 0) {
                $this->percentageDifference = 0;
            } else {
                $this->percentageDifference = ($this->priceDifference / $firstPrice) * 100;
            }
        } else {
            $this->priceDifference = 0;
            $this->percentageDifference = 0;
        }
    }

    public function getChartData($scale = '1D')
    {
        $this->timeScale = $scale;
        $this->fetchDataForScale();
    
        $dateFormat = match ($this->timeScale) {
            '1H' => 'H:i:s',
            '1D' => 'M d, H:i',
            '1W' => 'M d',
            '1M' => 'M Y',
            '1Y' => 'Y',
        };

        $labels = $this->priceHistories->map(fn ($entry) => $entry->created_at->format($dateFormat))->toArray();
        $data = $this->priceHistories->map(fn ($entry) => $entry->price)->toArray();
    
        $labelsJson = json_encode($labels);
        $dataJson = json_encode($data);
    
        $lastPrice = optional($this->priceHistories->last())->price ?? 0;
        $firstPrice = optional($this->priceHistories->first())->price ?? 0;
        $priceDifference = $lastPrice - $firstPrice;
        $percentageDifference = $firstPrice != 0 ? ($priceDifference / $firstPrice) * 100 : 0;
    
        return [
            'labelsJson' => $labelsJson,
            'dataJson' => $dataJson,
            'currentPrice' => number_format($this->price, 2),
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
        return match ($scale) {
            '1H' => ['start' => $today->copy()->subHour(), 'end' => $today],
            '1D' => ['start' => $today->copy()->subDay(), 'end' => $today],
            '1W' => ['start' => $today->copy()->subWeek(), 'end' => $today],
            '1M' => ['start' => $today->copy()->subMonth(), 'end' => $today],
            '1Y' => ['start' => $today->copy()->subYear(), 'end' => $today],
            default => ['start' => $today->copy()->subDay(), 'end' => $today],
        };
    }
    public function calculateVolatility()
    {
        try {
            // Fetch recent price histories (e.g., last 30 days)
            $priceHistories = $this->priceHistories()
                ->where('created_at', '>=', Carbon::now()->startOfDay())  // Start of the current day
                ->orderBy('created_at', 'asc')
                ->pluck('price')
                ->toArray();

            // If less than 2 data points, set volatility to 0
            if (count($priceHistories) < 2) {
                $this->volatility = 0;
                $this->save();
                return;
            }

            // Calculate percentage changes
            $percentageChanges = [];
            for ($i = 1; $i < count($priceHistories); $i++) {
                $previousPrice = $priceHistories[$i - 1];
                $currentPrice = $priceHistories[$i];

                // Check for division by zero (avoid using $previousPrice == 0)
                if ($previousPrice == 0) {
                    continue;
                }

                $percentageChange = (($currentPrice - $previousPrice) / $previousPrice) * 100;
                $percentageChanges[] = $percentageChange;
            }

            // If no valid percentage changes could be calculated, set volatility to 0
            if (empty($percentageChanges)) {
                $this->volatility = 0;
                $this->save();
                return;
            }

            // Calculate standard deviation of percentage changes
            $averageChange = array_sum($percentageChanges) / count($percentageChanges);
            $variance = array_sum(array_map(function ($x) use ($averageChange) {
                return pow($x - $averageChange, 2);
            }, $percentageChanges)) / count($percentageChanges);
            $volatility = sqrt($variance);

            // Adjust the sign of volatility based on the trend (averageChange)
            if ($averageChange < 0) {
                $volatility = -$volatility;
            }

            // Update the stock's volatility
            $this->volatility = $volatility;
            $this->save();
        } catch (\Exception $e) {
            // Log the error message to help diagnose any issues
            \Log::error('Error calculating volatility for stock ID ' . $this->id . ': ' . $e->getMessage());
            // Set volatility to 0 in case of an error
            $this->volatility = 0;
            $this->save();
        }
    }

    
}
