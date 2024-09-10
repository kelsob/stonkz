<div class="mb-4">
    @php
        $portfolioStock = Auth::user()->portfolio->portfolioStocks()->where('stock_id', $stock->id)->first();
        $availableQuantity = $portfolioStock ? $portfolioStock->quantity : 0;
        $sellQuantity = isset($sellQuantity) && is_numeric($sellQuantity) ? $sellQuantity : 0;
        $canSell = $sellQuantity > 0 && $sellQuantity <= $availableQuantity;
        $totalSellValue = $sellQuantity * $price;
    @endphp

    <label for="sellQuantity" class="block text-sm font-medium text-gray-700">Sell Quantity</label>
    <input type="number" id="sellQuantity" wire:model.live="sellQuantity" class="mt-1 p-2 border border-gray-300 rounded-md w-full" min="0" max="{{ $availableQuantity }}">

    <button wire:click="sellStock"
            class="mt-2 font-bold py-2 px-4 rounded w-full
                   {{ $canSell ? 'bg-red-500 hover:bg-red-700 text-white' : 'bg-gray-500 text-gray-300 cursor-not-allowed' }}"
            {{ !$canSell ? 'disabled' : '' }}>
        Sell Stock (+{{ $totalSellValue < 0 ? '-$' . number_format(abs($totalSellValue), 2) : '$' . number_format($totalSellValue, 2) }})
    </button>

    @if(!$canSell && $sellQuantity > $availableQuantity)
        <p class="text-red-500 text-sm mt-2">You cannot sell more than you own.</p>
    @endif
</div>
