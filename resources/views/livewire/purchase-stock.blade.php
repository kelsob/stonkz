<div class="mb-4">
    @php
        $userBalance = Auth::user()->balance;
        $purchaseQuantity = isset($purchaseQuantity) && is_numeric($purchaseQuantity) ? $purchaseQuantity : 0;
        $totalPurchasePrice = $purchaseQuantity * $price;
        $formattedTotalPrice = number_format(abs($totalPurchasePrice), 2);
        $canAfford = $purchaseQuantity > 0 && ($price > 0 ? $totalPurchasePrice <= $userBalance : true); // For negative prices, can always afford
        
        // If the price is positive, calculate maxPurchasableQuantity; otherwise, no limit for negative prices
        $maxPurchasableQuantity = $price > 0 ? intval($userBalance / $price) : null;
        $priceSign = $totalPurchasePrice >= 0 ? '' : '-'; // Determine if the price is negative or positive
    @endphp

    <label for="purchaseQuantity" class="block text-sm font-medium text-gray-700">Purchase Quantity</label>
    
    <input type="number" id="purchaseQuantity" 
           wire:model.live="purchaseQuantity" 
           class="mt-1 p-2 border border-gray-300 rounded-md w-full" 
           min="0" 
           @if ($price > 0)
               max="{{ $maxPurchasableQuantity }}" 
           @endif
           step="1"
           oninput="this.value = Math.max(this.min, {{ $price > 0 ? 'Math.min(this.max, this.value)' : 'this.value' }});"> <!-- Conditional max enforcement -->
    
    <button wire:click="purchaseStock"
            class="mt-2 font-bold py-2 px-4 rounded w-full
                   {{ $canAfford ? 'bg-blue-500 hover:bg-blue-700 text-white' : 'bg-gray-500 text-gray-300 cursor-not-allowed' }}"
            {{ !$canAfford ? 'disabled' : '' }}>
        Purchase Stock ({{ $priceSign }}${{ $formattedTotalPrice }})
    </button>

    @if(!$canAfford && $purchaseQuantity > 0 && $price > 0 && $purchaseQuantity > $maxPurchasableQuantity)
        <p class="text-red-500 text-sm mt-2">You cannot afford to purchase this quantity.</p>
    @endif
</div>
