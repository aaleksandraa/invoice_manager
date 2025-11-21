@extends('layouts.app')

@section('content')
<div class="mb-4">
    <!-- Naslov i dugme na mobilnim uređajima -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
        <h1 class="text-2xl font-bold mb-4 sm:mb-0">Lista faktura</h1>
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
            <a href="{{ route('invoices.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 mb-4 sm:mb-0">Nova faktura</a>
            <!-- Filter datuma -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2 mb-4 sm:mb-0">
                <label for="startDate" class="text-gray-700">Od:</label>
                <input type="text" id="startDate" class="border p-2 rounded w-full sm:w-32 flatpickr-input" placeholder="01.01.2025." readonly>
                <label for="endDate" class="text-gray-700">Do:</label>
                <input type="text" id="endDate" class="border p-2 rounded w-full sm:w-32 flatpickr-input" placeholder="01.12.2025." readonly>
            </div>
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
                    <th class="p-3 text-left cursor-pointer" data-sort="broj_fakture" onclick="sortTable('broj_fakture')">
                        Broj fakture <span class="inline-block ml-1">↑↓</span>
                    </th>
                    <th class="p-3 text-left cursor-pointer" data-sort="datum_izdavanja" onclick="sortTable('datum_izdavanja')">
                        Datum <span class="inline-block ml-1">↑↓</span>
                    </th>
                    <th class="p-3 text-left cursor-pointer" data-sort="client" onclick="sortTable('client')">
                        Klijent <span class="inline-block ml-1">↑↓</span>
                    </th>
                    <th class="p-3 text-left cursor-pointer" data-sort="cijena" onclick="sortTable('cijena')">
                        Iznos <span class="inline-block ml-1">↑↓</span>
                    </th>
                    <th class="p-3 text-left cursor-pointer" data-sort="placeno" onclick="sortTable('placeno')">
                        Plaćeno <span class="inline-block ml-1">↑↓</span>
                    </th>
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
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-black hover:text-gray-700" title="Vidi"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('invoices.edit', $invoice) }}" class="text-black hover:text-gray-700" title="Uredi"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" style="display:inline;" onsubmit="return confirm('Jeste li sigurni da želite obrisati ovu fakturu?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-black hover:text-gray-700" title="Obriši"><i class="fas fa-trash"></i></button>
                            </form>
                            <a href="{{ route('invoices.view-pdf', $invoice) }}" class="text-black hover:text-gray-700" title="Pregled PDF"><i class="fas fa-file-pdf"></i></a>
                            <form action="{{ route('invoices.send-invoice', $invoice) }}" method="POST" style="display:inline;" onsubmit="return confirm('Jeste li sigurni da želite poslati fakturu emailom klijentu?');">
                                @csrf
                                <button type="submit" class="text-black hover:text-gray-700" title="Pošalji fakturu"><i class="fas fa-paper-plane"></i></button>
                            </form>
                            <form action="{{ route('invoices.send-email', $invoice) }}" method="POST" style="display:inline;" onsubmit="return confirm('Jeste li sigurni da želite poslati opomenu klijentu?');">
                                @csrf
                                <button type="submit" class="text-black hover:text-gray-700" title="Pošalji opomenu"><i class="fas fa-bell"></i></button>
                            </form>
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
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <a href="{{ route('invoices.show', $invoice) }}" class="text-black hover:text-gray-700"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('invoices.edit', $invoice) }}" class="text-black hover:text-gray-700"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" style="display:inline;" onsubmit="return confirm('Jeste li sigurni da želite obrisati ovu fakturu?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-black hover:text-gray-700"><i class="fas fa-trash"></i></button>
                    </form>
                    <a href="{{ route('invoices.view-pdf', $invoice) }}" class="text-black hover:text-gray-700"><i class="fas fa-file-pdf"></i></a>
                    <form action="{{ route('invoices.send-invoice', $invoice) }}" method="POST" style="display:inline;" onsubmit="return confirm('Jeste li sigurni da želite poslati fakturu emailom klijentu?');">
                        @csrf
                        <button type="submit" class="text-black hover:text-gray-700"><i class="fas fa-paper-plane"></i></button>
                    </form>
                    <form action="{{ route('invoices.send-email', $invoice) }}" method="POST" style="display:inline;" onsubmit="return confirm('Jeste li sigurni da želite poslati opomenu klijentu?');">
                        @csrf
                        <button type="submit" class="text-black hover:text-gray-700"><i class="fas fa-bell"></i></button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <p class="mt-4"><strong>Ukupno uplaćeno:</strong> {{ number_format($totalPaid, 2) }} KM</p>

    <!-- JavaScript za pretragu, sortiranje i filter datuma -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let sortDirection = {};
        let lastSortedColumn = null;

        // Inicijalizacija Flatpickr
        flatpickr('#startDate', {
            dateFormat: 'd.m.Y',
            onChange: function(selectedDates, dateStr, instance) {
                filterByDate();
            }
        });
        flatpickr('#endDate', {
            dateFormat: 'd.m.Y',
            onChange: function(selectedDates, dateStr, instance) {
                filterByDate();
            }
        });

        function sortTable(column) {
            const tableRows = document.querySelectorAll('#invoiceTable tbody tr');
            const cards = document.querySelectorAll('#invoiceCards > div');

            if (!sortDirection[column]) sortDirection[column] = 'asc';
            else sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';

            if (lastSortedColumn && lastSortedColumn !== column) {
                const lastHeader = document.querySelector(`[data-sort="${lastSortedColumn}"]`);
                if (lastHeader) lastHeader.classList.remove('bg-gray-300');
            }

            const header = document.querySelector(`[data-sort="${column}"]`);
            if (header) header.classList.toggle('bg-gray-300', true);

            const rowsArray = Array.from(tableRows);
            rowsArray.sort((a, b) => {
                let valueA, valueB;
                switch (column) {
                    case 'broj_fakture':
                        valueA = a.cells[0].textContent.trim();
                        valueB = b.cells[0].textContent.trim();
                        return sortDirection[column] === 'asc' ? valueA.localeCompare(valueB) : valueB.localeCompare(valueA);
                    case 'datum_izdavanja':
                        valueA = a.cells[1].textContent.trim() === '-' ? '' : a.cells[1].textContent.trim();
                        valueB = b.cells[1].textContent.trim() === '-' ? '' : b.cells[1].textContent.trim();
                        return sortDirection[column] === 'asc' ? new Date(valueA || '1970-01-01') - new Date(valueB || '1970-01-01') : new Date(valueB || '1970-01-01') - new Date(valueA || '1970-01-01');
                    case 'client':
                        valueA = a.cells[2].textContent.trim();
                        valueB = b.cells[2].textContent.trim();
                        return sortDirection[column] === 'asc' ? valueA.localeCompare(valueB) : valueB.localeCompare(valueA);
                    case 'cijena':
                        valueA = parseFloat(a.cells[3].textContent.replace(/[^0-9.]/g, ''));
                        valueB = parseFloat(b.cells[3].textContent.replace(/[^0-9.]/g, ''));
                        return sortDirection[column] === 'asc' ? valueA - valueB : valueB - valueA;
                    case 'placeno':
                        valueA = a.cells[4].querySelector('span').classList.contains('bg-green-500');
                        valueB = b.cells[4].querySelector('span').classList.contains('bg-green-500');
                        return sortDirection[column] === 'asc' ? (valueA === valueB ? 0 : valueA ? -1 : 1) : (valueA === valueB ? 0 : valueA ? 1 : -1);
                }
            });

            rowsArray.forEach(row => {
                document.querySelector('#invoiceTable tbody').appendChild(row);
            });

            const cardsArray = Array.from(cards);
            cardsArray.sort((a, b) => {
                let valueA, valueB;
                switch (column) {
                    case 'broj_fakture':
                        valueA = a.querySelector('p.font-semibold').textContent.trim();
                        valueB = b.querySelector('p.font-semibold').textContent.trim();
                        return sortDirection[column] === 'asc' ? valueA.localeCompare(valueB) : valueB.localeCompare(valueA);
                    case 'datum_izdavanja':
                        valueA = a.querySelector('p.text-gray-600').textContent.trim() === '-' ? '' : a.querySelector('p.text-gray-600').textContent.trim();
                        valueB = b.querySelector('p.text-gray-600').textContent.trim() === '-' ? '' : b.querySelector('p.text-gray-600').textContent.trim();
                        return sortDirection[column] === 'asc' ? new Date(valueA || '1970-01-01') - new Date(valueB || '1970-01-01') : new Date(valueB || '1970-01-01') - new Date(valueA || '1970-01-01');
                    case 'client':
                        valueA = a.querySelectorAll('p')[1].textContent.trim();
                        valueB = b.querySelectorAll('p')[1].textContent.trim();
                        return sortDirection[column] === 'asc' ? valueA.localeCompare(valueB) : valueB.localeCompare(valueA);
                    case 'cijena':
                        valueA = parseFloat(a.querySelector('p.mr-2').textContent.replace(/[^0-9.]/g, ''));
                        valueB = parseFloat(b.querySelector('p.mr-2').textContent.replace(/[^0-9.]/g, ''));
                        return sortDirection[column] === 'asc' ? valueA - valueB : valueB - valueA;
                    case 'placeno':
                        valueA = a.querySelector('span').classList.contains('bg-green-500');
                        valueB = b.querySelector('span').classList.contains('bg-green-500');
                        return sortDirection[column] === 'asc' ? (valueA === valueB ? 0 : valueA ? -1 : 1) : (valueA === valueB ? 0 : valueA ? 1 : -1);
                }
            });

            cardsArray.forEach(card => {
                document.getElementById('invoiceCards').appendChild(card);
            });

            lastSortedColumn = column;
        }

        function filterByDate() {
            const startDateInput = document.getElementById('startDate').value;
            const endDateInput = document.getElementById('endDate').value;

            let startDate = startDateInput ? new Date(startDateInput.split('.').reverse().join('-')) : null;
            let endDate = endDateInput ? new Date(endDateInput.split('.').reverse().join('-')) : null;

            if (startDate && !isNaN(startDate)) startDate = startDate.toISOString().split('T')[0];
            if (endDate && !isNaN(endDate)) endDate = endDate.toISOString().split('T')[0];

            const tableRows = document.querySelectorAll('#invoiceTable tbody tr');
            const cards = document.querySelectorAll('#invoiceCards > div');

            tableRows.forEach(row => {
                const dateCell = row.cells[1].textContent.trim();
                const date = dateCell === '-' ? null : new Date(dateCell.split('.').reverse().join('-')).toISOString().split('T')[0];
                const isVisible = (!startDate || !date || date >= startDate) && (!endDate || !date || date <= endDate);
                row.style.display = isVisible ? '' : 'none';
            });

            cards.forEach(card => {
                const dateText = card.querySelector('p.text-gray-600').textContent.trim();
                const date = dateText === '-' ? null : new Date(dateText.split('.').reverse().join('-')).toISOString().split('T')[0];
                const isVisible = (!startDate || !date || date >= startDate) && (!endDate || !date || date <= endDate);
                card.style.display = isVisible ? '' : 'none';
            });

            // Ponovno primijeni pretragu ako je uneseno nešto u searchInput
            filterInvoices();
        }

        function filterInvoices() {
            const input = document.getElementById('searchInput').value.toLowerCase();

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
                row.style.display = match && (row.style.display !== 'none' || row.style.display === '') ? '' : 'none';
            });

            const cards = document.querySelectorAll('#invoiceCards > div');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(input) && (card.style.display !== 'none' || card.style.display === '') ? '' : 'none';
            });
        }
    </script>
@endsection