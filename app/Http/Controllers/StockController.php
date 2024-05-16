<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    public function getData($stockId, Request $request)
    {
        $timeScale = $request->query('timeScale', '1D'); // Default to '1D' if not specified
    
        $stock = Stock::find($stockId);
        if (!$stock) {
            return response()->json(['error' => 'Stock not found'], 404);
        }
    
        $chartData = $stock->getChartData($timeScale);
    
        return response()->json([
            'labels' => $chartData['labelsJson'],
            'values' => $chartData['dataJson'],
            'currentPrice' => $chartData['currentPrice'],
            'priceDifference' => $chartData['priceDifference'],
            'priceDifferenceSign' => $chartData['priceDifferenceSign'],
            'percentageDifference' => $chartData['percentageDifference'],
            'percentageDifferenceSign' => $chartData['percentageDifferenceSign'],
            'priceColorClass' => $chartData['priceColorClass'],
        ]);
    }
}

