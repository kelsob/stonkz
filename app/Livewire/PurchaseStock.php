<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\StockTransactionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;


class PurchaseStock extends Component {
    public $stock;
    public $purchaseQuantity = 0;
    public $price = 0;
    public $userBalance = 0;

    protected $rules = [
        'purchaseQuantity' => 'required|integer|min:1',
    ];

    public function mount($stock) {
        $this->stock = $stock;
        $this->price = $stock->price;
        $this->userBalance = Auth::user()->balance;
    }

    // Listen for 'stockSold' event to update the user's balance
    #[On('stockSold')]
    public function updateUserBalanceAfterSale() {
        // Refresh user's balance after the stock is sold
        Log::info("stock been sold");
        $this->userBalance = Auth::user()->balance;
    }

    public function purchaseStock(StockTransactionService $service) {
        $this->validate();
        $user = Auth::user();
        try {
            // Buy the stock
            $service->buyStock($user, $this->stock, $this->purchaseQuantity, $this->stock->price);
            
            // Dispatch the 'stockPurchased' event with a descriptive message and stockId
            $this->dispatch('stockPurchased', [
                'message' => sprintf(
                    'Stock purchase successful of %d shares of %s (%s) at market rate of $%.2f per share. Total cost: $%.2f.',
                    $this->purchaseQuantity,
                    $this->stock->name,
                    $this->stock->ticker,
                    $this->stock->price,
                    $this->purchaseQuantity * $this->stock->price
                ),
                'stockId' => $this->stock->id,
            ]);

            // Reset the purchase quantity to 0
            $this->purchaseQuantity = 0;
            // Refresh user's balance after the purchase
            $this->userBalance = Auth::user()->balance;

        } catch (\Exception $e) {
            // Dispatch the 'stockPurchaseFailed' event with an error message
            $this->dispatch('stockPurchaseFailed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function render() {
        return view('livewire.purchase-stock', [
            'userBalance' => $this->userBalance,
        ]);
    }
}
