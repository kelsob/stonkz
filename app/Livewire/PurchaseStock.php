<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\StockTransactionService;
use Illuminate\Support\Facades\Auth;

class PurchaseStock extends Component {
    public $stock;
    public $purchaseQuantity = 0;
    public $price = 0;

    protected $rules = [
        'purchaseQuantity' => 'required|integer|min:1',
    ];

    public function purchaseStock(StockTransactionService $service) {
        $this->validate();
        $user = Auth::user();
        try {
            $service->buyStock($user, $this->stock, $this->purchaseQuantity, $this->stock->price);
            $this->dispatch('stockPurchased', ['message' => 'Stock purchased successfully.']);
        } catch (\Exception $e) {
            $this->dispatch('stockPurchaseFailed', ['error' => $e->getMessage()]);
        }
    }
}
