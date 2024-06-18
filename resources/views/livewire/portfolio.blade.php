<div class="container mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">Your Portfolio</h2>
    <div class="bg-white shadow-md rounded-lg p-6">
        <table class="w-full text-left table-auto">
            <thead>
                <tr>
                    <th class="px-4 py-2">Stock</th>
                    <th class="px-4 py-2">Ticker</th>
                    <th class="px-4 py-2">Quantity</th>
                    <th class="px-4 py-2">Average Price</th>
                    <th class="px-4 py-2">Current Price</th>
                    <th class="px-4 py-2">Total Value</th>
                    <th class="px-4 py-2">Change</th>
                </tr>
            </thead>
            <tbody>
                @foreach($portfolio->portfolioStocks as $portfolioStock)
                    @php
                        $currentPrice = $portfolioStock->stock->price;
                        $totalValue = $portfolioStock->quantity * $currentPrice;
                        $change = (($currentPrice - $portfolioStock->average_price) / $portfolioStock->average_price) * 100;
                        $changeClass = $change >= 0 ? 'text-green-600' : 'text-red-600';
                    @endphp
                    <tr>
                        <td class="border px-4 py-2">{{ $portfolioStock->stock->name }}</td>
                        <td class="border px-4 py-2">{{ $portfolioStock->stock->ticker }}</td>
                        <td class="border px-4 py-2">{{ $portfolioStock->quantity }}</td>
                        <td class="border px-4 py-2">${{ number_format($portfolioStock->average_price, 2) }}</td>
                        <td class="border px-4 py-2">${{ number_format($currentPrice, 2) }}</td>
                        <td class="border px-4 py-2">${{ number_format($totalValue, 2) }}</td>
                        <td class="border px-4 py-2 {{ $changeClass }}">{{ number_format($change, 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
