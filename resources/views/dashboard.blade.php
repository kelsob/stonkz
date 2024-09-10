<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Simulation Game') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="mx-auto flex justify-center align-center objects-center items-start gap-2">

            <!-- Volatile Stocks Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg min-w-fit min-h-auto">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">Today's Most Volatile Stocks</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr>
                                    <th class="px-2 py-2 text-left text-gray-600">Ticker</th>
                                    <th class="px-2 py-2 text-left text-gray-600">Current Price</th>
                                    <th class="px-2 py-2 text-left text-gray-600">Price Change</th>
                                    <th class="px-2 py-2 text-left text-gray-600">Percentage Change</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $volatileStocks = \App\Models\Stock::orderByRaw('ABS(volatility) desc')->take(5)->get();
                                @endphp
                                @foreach ($volatileStocks as $stock)
                                    @php
                                        $formattedPrice = number_format(abs($stock->price), 2);
                                        $pricePrefix = $stock->price < 0 ? '-' : '';
                                        $startOfDayPrice = $stock->priceHistories()
                                            ->where('created_at', '>=', \Carbon\Carbon::now()->startOfDay())
                                            ->orderBy('created_at', 'asc')
                                            ->pluck('price')
                                            ->first() ?? $stock->price;
                                        $priceChange = $stock->price - $startOfDayPrice;
                                        $formattedPriceChange = number_format(abs($priceChange), 2);
                                        $priceChangePrefix = $priceChange < 0 ? '-' : '';
                                        $percentageChange = ($startOfDayPrice != 0)
                                            ? (($priceChange) / $startOfDayPrice) * 100
                                            : 0;
                                        $formattedPercentageChange = number_format(abs($percentageChange), 2);
                                        $colorClass = $priceChange >= 0 ? 'text-green-600' : 'text-red-600';
                                        $arrowSymbol = $priceChange >= 0 ? '▲' : '▼';
                                    @endphp
                                    <tr>
                                        <td class="px-2 py-2">
                                            <a href="{{ route('stockdetails', ['stockId' => $stock->id]) }}" class="text-blue-600 hover:underline">
                                                {{ $stock->ticker }}
                                            </a>
                                        </td>
                                        <td class="px-2 py-2">{{ $pricePrefix }}${{ $formattedPrice }}</td>
                                        <td class="px-2 py-2 {{ $colorClass }}">
                                            <span class="inline-flex items-center whitespace-nowrap">
                                                {{ $arrowSymbol }} {{ $priceChangePrefix }}${{ $formattedPriceChange }}
                                            </span>
                                        </td>
                                        <td class="px-2 py-2 {{ $colorClass }}">
                                            <span class="inline-flex items-center whitespace-nowrap">
                                                {{ $arrowSymbol }} {{ $formattedPercentageChange }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- User Portfolio Section (if logged in) -->
            @auth
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg min-w-fit">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-lg mb-4">Your Portfolio</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-2 text-left text-gray-600">Ticker</th>
                                        <th class="px-2 py-2 text-left text-gray-600">Shares</th>
                                        <th class="px-2 py-2 text-left text-gray-600">Avg. Price</th>
                                        <th class="px-2 py-2 text-left text-gray-600">Current Price</th>
                                        <th class="px-2 py-2 text-left text-gray-600">Total Value</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $userPortfolio = Auth::user()->portfolio()->with('portfolioStocks.stock')->first();
                                    @endphp
                                    @if ($userPortfolio && $userPortfolio->portfolioStocks->isNotEmpty())
                                        @foreach ($userPortfolio->portfolioStocks as $portfolioStock)
                                            @php
                                                $currentPrice = number_format(abs($portfolioStock->stock->price), 2);
                                                $currentPricePrefix = $portfolioStock->stock->price < 0 ? '-' : '';
                                                $totalValue = $portfolioStock->quantity * $portfolioStock->stock->price;
                                            @endphp
                                            <tr>
                                                <td class="px-2 py-2">
                                                    <a href="{{ route('stockdetails', ['stockId' => $portfolioStock->stock->id]) }}" class="text-blue-600 hover:underline">
                                                        {{ $portfolioStock->stock->ticker }}
                                                    </a>
                                                </td>
                                                <td class="px-2 py-2">{{ $portfolioStock->quantity }}</td>
                                                <td class="px-2 py-2">${{ number_format($portfolioStock->average_price, 2) }}</td>
                                                <td class="px-2 py-2">{{ $currentPricePrefix }}${{ $currentPrice }}</td>
                                                <td class="px-2 py-2">${{ number_format($totalValue, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="px-2 py-2 text-gray-500 text-center">You don't have any stocks in your portfolio yet.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Available Funds Section inside portfolio -->
                        <div class="mt-4">
                            <h4 class="font-semibold text-lg mb-2">Available Funds</h4>
                            <p class="text-lg font-bold text-green-600">${{ number_format(Auth::user()->balance, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endauth

        </div>
    </div>
</x-app-layout>
