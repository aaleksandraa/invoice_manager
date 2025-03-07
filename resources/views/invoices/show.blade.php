@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Faktura {{ $invoice->broj_fakture }}</h1>
    <div class="bg-white p-6 rounded shadow-md mb-4">
        <p><strong>Klijent:</strong> {{ $invoice->client->naziv_firme }}</p>
        <p><strong>Datum:</strong> {{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}</p>
        <p><strong>Opis:</strong> {{ $invoice->opis_posla }}</p>
        <p><strong>Iznos:</strong>
            @if ($invoice->valuta === 'BAM')
                {{ number_format($invoice->cijena, 2) }} KM
            @else
                {{ number_format($invoice->cijena, 2) }} EUR / {{ number_format($invoice->bam_amount, 2) }} KM
            @endif
        </p>
        <p><strong>Status:</strong>
            <span class="inline-block w-4 h-4 rounded-full {{ $invoice->placeno ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
            {{ $invoice->placeno ? 'Plaćeno' : 'Neplaćeno' }}
        </p>
        @if ($invoice->placeno)
            <p><strong>Datum plaćanja:</strong> {{ $invoice->datum_placanja ? $invoice->datum_placanja->format('d.m.Y') : '-' }}</p>
            @if ($invoice->valuta === 'EUR')
                <p><strong>Uplaćeno:</strong> {{ number_format($invoice->uplaceni_iznos_eur, 2) }} EUR / {{ number_format($invoice->paid_bam_amount, 2) }} KM</p>
            @endif
        @endif
    </div>

    <form method="POST" action="{{ route('invoices.update', $invoice) }}" class="bg-white p-6 rounded shadow-md">
        @csrf
        @method('PUT')
        <div class="mb-4 flex items-center">
            <input type="checkbox" name="placeno" value="1" class="mr-2" {{ $invoice->placeno ? 'checked' : '' }}>
            <label class="text-gray-700">Plaćeno</label>
        </div>
        @if ($invoice->valuta === 'EUR')
            <div class="mb-4">
                <label class="block text-gray-700">Uplaćeni iznos (EUR)</label>
                <input type="number" name="uplaceni_iznos_eur" step="0.01" value="{{ $invoice->uplaceni_iznos_eur }}" class="w-full border p-2 rounded">
            </div>
        @endif
        <div class="mb-4">
            <label class="block text-gray-700">Datum plaćanja</label>
            <input type="text" name="datum_placanja" id="datum_placanja" value="{{ $invoice->datum_placanja ? $invoice->datum_placanja->format('d.m.Y') : '' }}" class="w-full border p-2 rounded flatpickr-input" readonly>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Ažuriraj</button>
    </form>

    <!-- Flatpickr biblioteka -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Inicijalizacija Flatpickr za datum_placanja
        flatpickr('#datum_placanja', {
            dateFormat: 'd.m.Y',
            defaultDate: "{{ $invoice->datum_placanja ? $invoice->datum_placanja->format('d.m.Y') : '' }}",
            onChange: function(selectedDates, dateStr, instance) {
                const isoDate = selectedDates[0] ? selectedDates[0].toISOString().split('T')[0] : '';
                document.getElementById('datum_placanja').value = dateStr;
                document.getElementById('datum_placanja').setAttribute('data-iso', isoDate);
            }
        });

        // Ažuriranje forme prije slanja da se pošalje ISO format
        document.querySelector('form').addEventListener('submit', function(e) {
            const dateInput = document.getElementById('datum_placanja');
            const isoDate = dateInput.getAttribute('data-iso');
            if (isoDate) {
                dateInput.value = isoDate;
            } else if (!dateInput.value) {
                dateInput.value = ''; // Ako je polje prazno, pošalji prazan string
            }
        });
    </script>
@endsection