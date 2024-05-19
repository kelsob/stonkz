<div class="mb-4">
    <label for="purchaseQuantity" class="block text-sm font-medium text-gray-700">Purchase Quantity</label>
    <input type="number" id="purchaseQuantity" wire:model.live="purchaseQuantity" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
    <button wire:click="purchaseStock" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
        Purchase Stock (-${{ $purchaseQuantity * $price}})
    </button>
</div>
