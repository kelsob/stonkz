<?php

use Livewire\Volt\Component;
use App\Models\Stock;
use Illuminate\Support\Facades\Log;

new class extends Component {
    
    public $stockId;
    public $stock;
    public $timeScale = "1D";
    public $chartType = "line";

    public function mount($stockId) {
        $this->stockId = $stockId;
        $this->stock = Stock::find($stockId);
    }
    public function timeScaleChanged($scale) {
        $this->timeScale = $scale;
    }
    public function chartTypeChanged($type) {
        $this->chartType = $type;
    }
} ?>

<div class="flex items-center justify-center min-h-screen bg-gray-100 mt-2">
    <div class="bg-white rounded-lg shadow-md p-4 max-w-5xl w-full">
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

        <div class="flex justify-between items-center mb-2">
            <h3 class="text-2xl font-bold">{{ $stock->ticker }}</h3>
            <p class="text-gray-600 font-semibold">{{ $stock->name }}</p>
        </div>


        @php
            $details = $stock->getChartData('1D');  // Defaulting to 1 Day for example
        @endphp
        <div class="bg-white rounded-lg shadow-lg p-4 hover:bg-gray-100">
            <div class="flex justify-between items-center mb-2">
                <p class="text-2xl {{ $details['priceColorClass'] }} font-bold">
                    {{ $details['priceDifferenceSign'] }}${{ $details['priceDifference'] }}
                </p>
                <p class="text-xl {{ $details['priceColorClass'] }} font-semibold">
                    ({{ $details['percentageDifferenceSign'] }}{{ $details['percentageDifference'] }}%)
                </p>
            </div>
            <canvas id="stockPriceGraph-{{ $stock->id }}" class="stock-chart w-full"
                data-ticker="{{ $stock->ticker }}"
                data-stock-id="{{ $stock->id }}"
                data-name="{{ $stock->name }}"
                data-dataJson="{{ $details['dataJson'] }}"
                data-labelsJson="{{ $details['labelsJson'] }}"
                data-chart-type="{{ $chartType }}"
                ></canvas>
        </div>
        <script type="text/javascript" src="{{ asset('masschartassigner.js') }}"></script>
    </div>
</div>
