<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockTransactionService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(StockTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function buy(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric'
        ]);

        $user = $request->user();

        try {
            $transaction = $this->transactionService->buyStock(
                $user,
                $request->stock_id,
                $request->quantity,
                $request->price
            );
            return response()->json(['message' => 'Purchase successful', 'transaction' => $transaction], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function sell(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric'
        ]);

        $user = $request->user();

        try {
            $transaction = $this->transactionService->sellStock(
                $user,
                $request->stock_id,
                $request->quantity,
                $request->price
            );
            return response()->json(['message' => 'Sale successful', 'transaction' => $transaction], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
