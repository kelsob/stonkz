<div class="mb-4">
    @php
        $userBalance = Auth::user()->balance;
        $purchaseQuantity = isset($purchaseQuantity) && is_numeric($purchaseQuantity) ? $purchaseQuantity : 0;
        $totalPurchasePrice = $purchaseQuantity * $price;
        $canAfford = $totalPurchasePrice <= $userBalance;
    @endphp

    <label for="purchaseQuantity" class="block text-sm font-medium text-gray-700">Purchase Quantity</label>
    <input type="number" id="purchaseQuantity" wire:model.live="purchaseQuantity" class="mt-1 p-2 border border-gray-300 rounded-md w-full" min="0">
    
    <button wire:click="purchaseStock"
            class="mt-2 font-bold py-2 px-4 rounded w-full
                   {{ $canAfford ? 'bg-blue-500 hover:bg-blue-700 text-white' : 'bg-gray-500 text-gray-300 cursor-not-allowed' }}"
            {{ !$canAfford ? 'disabled' : '' }}>
        Purchase Stock (-${{ $totalPurchasePrice }})
    </button>
</div>
