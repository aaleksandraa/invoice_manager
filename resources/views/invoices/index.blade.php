@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <!-- Naslov i dugme na mobilnim uređajima -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <h1 class="text-2xl font-bold mb-4 sm:mb-0">Lista faktura</h1>
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                <a href="{{ route('invoices.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 mb-4 sm:mb-0">Nova faktura</a>
                <!-- Polje za pretragu -->
                <input type="text" id="searchInput" class="border p-2 rounded w-full sm:w-64" placeholder="Pretraži fakture..." onkeyup="filterInvoices()">
            </div>
        </div>
        
    </div>

    

    <!-- Tabelarni prikaz za desktop (sm i veći) -->
    <div class="hidden sm:block overflow-x-auto" id="invoiceTable">
        <table class="min-w-full bg-white shadow-md rounded">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="p-3 text-left">Broj fakture</th>
                    <th class="p-3 text-left">Datum</th>
                    <th class="p-3 text-left">Klijent</th>
                    <th class="p-3 text-left">Iznos</th>
                    <th class="p-3 text-left">Plaćeno</th>
                    <th class="p-3 text-left">Akcije</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr class="border-b">
                        <td class="p-3">{{ $invoice->broj_fakture }}</td>
                        <td class="p-3">
                            {{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}
                        </td>
                        <td class="p-3">{{ $invoice->client->naziv_firme }}</td>
                        <td class="p-3">
                            @if ($invoice->valuta === 'BAM')
                                {{ number_format($invoice->cijena, 2) }} KM
                            @else
                                {{ number_format($invoice->cijena, 2) }} EUR
                            @endif
                        </td>
                        <td class="p-3">
                            <span class="inline-block w-4 h-4 rounded-full {{ $invoice->placeno ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        </td>
                        <td class="p-3 space-x-2">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-500 hover:underline">Prikaži</a>
                            <a href="{{ route('invoices.view-pdf', $invoice) }}" class="text-blue-500 hover:underline">PDF</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Prikaz za mobilne uređaje (manje od sm) -->
    <div class="block sm:hidden" id="invoiceCards">
        @foreach ($invoices as $invoice)
            <div class="bg-white p-4 rounded shadow-md mb-4">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <p class="font-semibold">{{ $invoice->broj_fakture }}</p>
                        <p class="text-gray-600">
                            {{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}
                        </p>
                    </div>
                </div>
                <p class="mb-2">{{ $invoice->client->naziv_firme }}</p>
                <div class="flex items-center mb-2">
                    <p class="mr-2">
                        @if ($invoice->valuta === 'BAM')
                            {{ number_format($invoice->cijena, 2) }} KM
                        @else
                            {{ number_format($invoice->cijena, 2) }} EUR
                        @endif
                    </p>
                    <span class="inline-block w-4 h-4 rounded-full {{ $invoice->placeno ? 'bg-green-500' : 'bg-red-500' }}"></span>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-500 hover:underline">Prikaži</a>
                    <a href="{{ route('invoices.view-pdf', $invoice) }}" class="text-blue-500 hover:underline">PDF</a>
                </div>
            </div>
        @endforeach
    </div>

    <p class="mt-4"><strong>Ukupno uplaćeno:</strong> {{ number_format($totalPaid, 2) }} KM</p>

    <!-- JavaScript za pretragu -->
    <script>
        function filterInvoices() {
            // Dobijanje unosa iz search polja
            const input = document.getElementById('searchInput').value.toLowerCase();

            // Filtriranje tabele (desktop prikaz)
            const tableRows = document.querySelectorAll('#invoiceTable tbody tr');
            tableRows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                let match = false;
                for (let i = 0; i < cells.length; i++) {
                    if (cells[i].textContent.toLowerCase().includes(input)) {
                        match = true;
                        break;
                    }
                }
                row.style.display = match ? '' : 'none';
            });

            // Filtriranje kartica (mobilni prikaz)
            const cards = document.querySelectorAll('#invoiceCards > div');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(input) ? '' : 'none';
            });
        }
    </script>
@endsection