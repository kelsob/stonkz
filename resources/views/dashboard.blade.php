<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Simulation Game') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
            <!-- News Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-2">Latest News</h3>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>News Item 1</li>
                        <li>News Item 2</li>
                        <li>News Item 3</li>
                    </ul>
                </div>
            </div>

            <!-- Volatile Stocks Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-2">Volatile Stocks</h3>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Stock 1</li>
                        <li>Stock 2</li>
                        <li>Stock 3</li>
                    </ul>
                </div>
            </div>

            <!-- Portfolio Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-2">Your Portfolio</h3>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Portfolio Item 1</li>
                        <li>Portfolio Item 2</li>
                        <li>Portfolio Item 3</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
