<div class="mb-4">
    <label for="sellQuantity" class="block text-sm font-medium text-gray-700">Sell Quantity</label>
    <input type="number" id="sellQuantity" wire:model.live="sellQuantity" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
    <button wire:click="sellStock" class="mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded w-full">
        Sell Stock (+${{ $sellQuantity * $price}})
    </button>
</div>
