<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/" wire:navigate>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" fill="none" stroke="currentColor" class="w-12 h-12">
                        <line x1="0" y1="22" x2="40" y2="22" stroke="lightgray" stroke-width="1.5" stroke-dasharray="2.5 2.5" />
                        <path d="M5 8 L11 20 L17 16 L23 28 L29 24 L35 36" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 36 L11 24 L17 28 L23 16 L29 20 L35 8" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>                
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
