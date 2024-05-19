
<div>
<!-- Flash Messages -->
    @if (session('message'))
        <div class="container mx-auto mt-4">
            <div class="bg-green-100 border-t border-b border-green-500 text-green-700 px-4 py-3 rounded mx-auto max-w-2xl text-center">
                <p class="font-bold">{{ session('message') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mx-auto mt-4">
            <div class="bg-red-100 border-t border-b border-red-500 text-red-700 px-4 py-3 rounded mx-auto max-w-2xl text-center">
                <p class="font-bold">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-center bg-gray-100 space-x-2 p-2">
        <!-- Flash Messages -->
        <div class="bg-white rounded-lg shadow-md p-4 max-w-xl w-full">
            <div class="flex items-center justify-between mb-2">
                <!-- Time Scale Buttons Left-aligned -->
                <div class="flex space-x-2">
                    @foreach(['1H' => '1H', '1D' => '1D', '1W' => '1W', '1M' => '1M', '1Y' => '1Y'] as $scale => $label)
                    <button wire:click="timeScaleChanged('{{ $scale }}')"
                        class="font-bold py-0 px-2 rounded transition duration-300 ease-in-out 
                            {{ $timeScale == $scale ? 'bg-blue-500 text-white' : 'bg-white text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white' }}"
                            id='{{ $scale }}Button'>
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                <!-- Chart Type Buttons Right-aligned -->
                <div class="flex space-x-2">
                    @foreach(['line' => 'Line', 'bar' => 'Bar', 'pie' => 'Pie'] as $type => $label)
                    <button wire:click="chartTypeChanged('{{ $type }}')"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded transition duration-300 ease-in-out {{ $chartType == $type ? 'bg-green-700' : '' }}"
                        id="{{ $type }}Button">
                        <!-- SVG icons here -->
                        @if($type == 'line')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" class="w-6 h-6">
                                <path d="M2 17 L5 13 L8 15 L11 9 L14 11 L17 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @elseif($type == 'bar')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6">
                                <rect x="1" y="14" width="4" height="4" />
                                <rect x="8" y="10" width="4" height="8" />
                                <rect x="15" y="2" width="4" height="16" />
                            </svg>
                        @elseif($type == 'pie')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" stroke="currentColor" class="w-6 h-6">
                                <circle cx="16" cy="16" r="15" fill-opacity="0.5" stroke-width="2"/>
                                <path d="M16 16 L16 5 A15 15 0 0 1 27 16 Z" fill="currentColor"/>
                            </svg>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-between items-center pl-2 pr-2">
                <h3 class="text-2xl font-bold">{{ $stock->ticker }}</h3>
                <p class="text-gray-600 font-semibold">{{ $stock->name }}</p>
            </div>

            @php
                $details = $stock->getChartData('1D');  // Defaulting to 1 Day for example
            @endphp
            <div class="bg-white rounded-lg shadow-lg pr-4 pl-2 pt-2 pb-2">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xl {{ $details['priceColorClass'] }} font-bold">
                        ${{ $details['currentPrice'] }}
                    </p>
                    <div class="text-right">
                        <p class="text-2xl {{ $details['priceColorClass'] }} font-semibold">
                            {{ $details['priceDifferenceSign'] }}${{ $details['priceDifference'] }}
                        </p>
                        <p class="text-xl {{ $details['priceColorClass'] }} font-semibold">
                            ({{ $details['percentageDifferenceSign'] }}{{ $details['percentageDifference'] }}%)
                        </p>
                    </div>
                </div>
                <canvas id="stockPriceGraph-{{ $stock->id }}" class="stock-chart w-fit"
                    data-ticker="{{ $stock->ticker }}"
                    data-stock-id="{{ $stock->id }}"
                    data-name="{{ $stock->name }}"
                    data-dataJson="{{ $details['dataJson'] }}"
                    data-labelsJson="{{ $details['labelsJson'] }}"
                    data-chart-type="{{ $chartType }}">
                </canvas>
            </div>
            <script type="text/javascript" src="{{ asset('masschartassigner.js') }}"></script>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 max-w-lg w-full mt-1">
            <div>
                <p class="text-xl font-semibold">{{ $stock->description }}</p>
                <p class="text-xl font-semibold">{{ $stock->motto }}</p>
            </div>
            <!-- Purchase Quantity Section -->
            <div class="mb-4">
                <label for="purchaseQuantity" class="block text-sm font-medium text-gray-700">Purchase Quantity</label>
                <input type="number" id="purchaseQuantity" wire:model.blur="purchaseQuantity" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                <button wire:click="purchaseStock" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
                    Purchase Stock (-${{ number_format($purchaseQuantity * $details['currentPrice'], 2)}})
                </button>
            </div>

            <!-- Sell Quantity Section -->
            <div class="mb-4">
                <label for="sellQuantity" class="block text-sm font-medium text-gray-700">Sell Quantity</label>
                <input type="number" id="sellQuantity" wire:model.blur="sellQuantity" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                <button wire:click="sellStock" class="mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded w-full">
                    Sell Stock (+${{ number_format($sellQuantity * $details['currentPrice'], 2)}})
                </button>
            </div>
        </div>
    </div>
</div>