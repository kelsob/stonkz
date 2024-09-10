<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use App\Services\StockTransactionService; // Import the StockTransactionService
use Illuminate\Support\Facades\Log;


class SellStock extends Component {
    public $stock;
    public $sellQuantity = 0;
    public $price = 0;
    public $availableQuantity = 0;

    public function mount($stock) {
        $this->stock = $stock;
        $this->price = $stock->price;
        $this->updateAvailableQuantity();
    }

    public function updateAvailableQuantity() {
        // Fetch the quantity of the stock the user has in their portfolio
        $portfolioStock = Auth::user()->portfolio->portfolioStocks()->where('stock_id', $this->stock->id)->first();
        $this->availableQuantity = $portfolioStock ? $portfolioStock->quantity : 0;
    }

    // Listen for the stockPurchased event with message field
    #[On('stockPurchased')]
    public function updateStockAfterPurchase($data) {
        if ($this->stock->id == $data['stockId']) {
            $this->updateAvailableQuantity();
        }
    }

    // Listen to itself after selling stock
    #[On('stockSold')]
    public function updateStockAfterSale($data) {
        if ($this->stock->id == $data['stockId']) {
            $this->updateAvailableQuantity(); // Recalculate available quantity after sale
        }
    }

    public function sellStock(StockTransactionService $service) {
        $user = Auth::user();
        
        if ($this->sellQuantity > 0 && $this->sellQuantity <= $this->availableQuantity) {
            try {
                // Call the StockTransactionService to sell the stock
                $service->sellStock($user, $this->stock, $this->sellQuantity, $this->stock->price);
    
                Log::info("dispatching that sell event.");
                // Dispatch an event after successfully selling the stock with a more descriptive message
                $this->dispatch('stockSold', [
                    'message' => sprintf(
                        'Stock sale successful of %d shares of %s (%s) at market rate of $%.2f per share. Total sale value: $%.2f.',
                        $this->sellQuantity,
                        $this->stock->name,
                        $this->stock->ticker,
                        $this->stock->price,
                        $this->sellQuantity * $this->stock->price
                    ),
                    'stockId' => $this->stock->id,
                ]);
    
                // Reset the sell quantity after sale
                $this->sellQuantity = 0;
            } catch (\Exception $e) {
                // Handle any exceptions
                $this->dispatch('stockSellFailed', ['error' => $e->getMessage()]);
            }
        }
    }

    public function render() {
        return view('livewire.sell-stock', [
            'availableQuantity' => $this->availableQuantity,
        ]);
    }
}
