<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use App\Models\Stock;
use App\Services\StockTransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class Details extends Component {
    public $stockId;
    public $stock;
    public $timeScale = "1D";
    public $chartType = "line";


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
    #[On('stockPurchased')]
    public function handleStockPurchased($data) {
        session()->flash('message', $data['message']);
        $this->dispatch('user-balance-changed'); 
    }

    #[On('stockPurchaseFailed')]
    public function handleStockPurchaseFailed($data) {
        session()->flash('error', $data['error']);
    }

    #[On('stockSold')]
    public function handleStockSold($data) {
        session()->flash('message', $data['message']);
        $this->dispatch('user-balance-changed'); 
    }

    #[On('stockSaleFailed')]
    public function handleStockSaleFailed($data) {
        session()->flash('error', $data['error']);
    }
};
