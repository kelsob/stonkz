
<div class="mx-auto max-w-screen-lg mt-2">
    <div class="flex item-center justify-between space-x-4 mb-2 mt-2">
        <!-- Time Scale Buttons Left-aligned -->
        <div class="flex space-x-2 pl-2">
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
        <div class="flex space-x-2 pr-2">
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
        @foreach ($stocks as $stock)
            @php
                $details = $stock->getChartData('1D');  // Defaulting to 1 Day for example
            @endphp
            <a href="{{ route('stockdetails', ['stockId' => $stock->id]) }}" class="block text-decoration-none">
                <div class="bg-white rounded-lg shadow-md p-4 hover:bg-gray-100">
                    <div class="flex justify-between items-center mb-0">
                        <h3 class="text-lg font-bold">{{ $stock->ticker }}</h3>

                        <!-- Adjust this container to align items inline and center the first two with the larger one -->
                        <div class="flex items-center gap-1">
                            <p class="text-sm {{ $details['priceColorClass'] }} font-bold">
                                {{ $details['priceDifferenceSign'] }}${{ $details['priceDifference'] }}
                            </p>
                            <p class="text-sm {{ $details['priceColorClass'] }} font-semibold">
                                ({{ $details['percentageDifferenceSign'] }}{{ $details['percentageDifference'] }}%)
                            </p>
                            <p class="text-xl font-bold">
                                ${{ $details['currentPrice'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Temporary debug output -->
                    <div class="flex justify-between items-center mb-0">
                        <p class="text-gray-600 mb-1">{{ $stock->name }}</p>
                    </div>
                    <canvas id="stockPriceGraph-{{ $stock->id }}" class="stock-chart"
                        data-ticker="{{ $stock->ticker }}"
                        data-stock-id="{{ $stock->id }}"
                        data-name="{{ $stock->name }}"
                        data-dataJson="{{ $details['dataJson'] }}"
                        data-labelsJson="{{ $details['labelsJson'] }}"
                        data-chart-type="{{ $chartType }}"
                        ></canvas>
                </div>
            </a>
        @endforeach
        <script type="text/javascript" src="{{ asset('masschartassigner.js') }}"></script>
    </div>
    <div class="p-2">
        {{ $stocks->links() }}
    </div>
</div>