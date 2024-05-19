<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use App\Models\Stock;
use App\Services\StockTransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Details extends Component {
    public $stockId;
    public $stock;
    public $timeScale = "1D";
    public $chartType = "line";
    public $purchaseQuantity = 0;
    public $sellQuantity = 0;

    protected $rules = [
        'purchaseQuantity' => 'required|integer|min:1',
        'sellQuantity' => 'required|integer|min:1',
    ];

    public function mount($stockId) {
        $this->stockId = $stockId;
        $this->stock = Stock::find($stockId);
    }

    public function timeScaleChanged($scale) {
        $this->timeScale = $scale;
    }

    public function chartTypeChanged($type) {
        $this->chartType = $type;
    }

    public function purchaseStock(StockTransactionService $service) {
        $this->validateOnly('purchaseQuantity');
        $user = Auth::user();
        try {
            $service->buyStock($user, $this->stock, $this->purchaseQuantity, $this->stock->price);
            request()->session()->flash('message', 'Stock purchased successfully.');
            $this->dispatch('user-balance-changed'); 
        } catch (\Exception $e) {
            request()->session()->flash('error', $e->getMessage());
        }
    }

    public function sellStock(StockTransactionService $service) {
        $this->validateOnly('sellQuantity');
        $user = Auth::user();
        try {
            $service->sellStock($user, $this->stock, $this->sellQuantity, $this->stock->price);
            session()->flash('message', 'Stock sold successfully.');
            $this->dispatch('user-balance-changed'); 
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render() {
        Log::info($this->purchaseQuantity);
        return view('livewire.stocks.details', [
            'purchaseQuantity' => $this->purchaseQuantity,
            'sellQuantity' => $this->sellQuantity
        ]);
    }

};
