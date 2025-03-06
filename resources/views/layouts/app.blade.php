<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-orange-700 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('invoices.index') }}" class="text-lg font-bold">Invoice Manager</a>
            <div class="space-x-4">
                <a href="{{ route('invoices.index') }}" class="hover:underline">Fakture</a>
                <a href="{{ route('clients.index') }}" class="hover:underline">Klijenti</a>
                <a href="{{ route('invoices.payments') }}" class="hover:underline">Plaćanja</a>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="hover:underline">
                    Odjava
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </nav>
    <div class="container mx-auto p-4">
        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>