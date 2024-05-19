<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\StockTransactionService;
use Illuminate\Support\Facades\Auth;

class SellStock extends Component {
    public $stock;
    public $sellQuantity = 0;
    public $price = 0;

    protected $rules = [
        'sellQuantity' => 'required|integer|min:1',
    ];

    public function sellStock(StockTransactionService $service) {
        $this->validate();
        $user = Auth::user();
        try {
            $service->sellStock($user, $this->stock, $this->sellQuantity, $this->stock->price);
            $this->dispatch('stockSold', ['message' => 'Stock sold successfully.']);
        } catch (\Exception $e) {
            $this->dispatch('stockSaleFailed', ['error' => $e->getMessage()]);
        }
    }

}
