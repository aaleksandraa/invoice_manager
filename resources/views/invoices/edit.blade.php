@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Uredi fakturu</h1>

    <!-- Prikaz poruka -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="bg-yellow-100 text-yellow-700 p-4 rounded mb-4">
            {{ session('warning') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('invoices.update', $invoice) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="klijent_id" class="block text-gray-700">Klijent</label>
            <select id="klijent_id" name="klijent_id" class="w-full border p-2 rounded @error('klijent_id') border-red-500 @enderror">
                <option value="">Odaberite klijenta</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ old('klijent_id', $invoice->klijent_id) == $client->id ? 'selected' : '' }}>
                        {{ $client->naziv_firme }}
                    </option>
                @endforeach
            </select>
            @error('klijent_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="broj_fakture" class="block text-gray-700">Broj fakture</label>
            <input id="broj_fakture" type="text" name="broj_fakture" value="{{ old('broj_fakture', $invoice->broj_fakture) }}" class="w-full border p-2 rounded @error('broj_fakture') border-red-500 @enderror" placeholder="#1/2026">
            <p class="text-gray-500 text-sm mt-1">Format: #broj/godina (npr. #1/2026)</p>
            @error('broj_fakture')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="datum_izdavanja" class="block text-gray-700">Datum izdavanja</label>
            <input id="datum_izdavanja" type="text" name="datum_izdavanja" value="{{ old('datum_izdavanja', $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '') }}" class="w-full border p-2 rounded flatpickr-input @error('datum_izdavanja') border-red-500 @enderror" readonly>
            @error('datum_izdavanja')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="opis_posla" class="block text-gray-700">Opis posla</label>
            <textarea id="opis_posla" name="opis_posla" class="w-full border p-2 rounded @error('opis_posla') border-red-500 @enderror">{{ old('opis_posla', $invoice->opis_posla) }}</textarea>
            @error('opis_posla')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="kolicina" class="block text-gray-700">Količina</label>
            <input id="kolicina" type="number" name="kolicina" value="{{ old('kolicina', $invoice->kolicina) }}" class="w-full border p-2 rounded @error('kolicina') border-red-500 @enderror">
            @error('kolicina')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="cijena" class="block text-gray-700">Cijena</label>
            <input id="cijena" type="number" step="0.01" name="cijena" value="{{ old('cijena', $invoice->cijena) }}" class="w-full border p-2 rounded @error('cijena') border-red-500 @enderror">
            @error('cijena')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="valuta" class="block text-gray-700">Valuta</label>
            <select id="valuta" name="valuta" class="w-full border p-2 rounded @error('valuta') border-red-500 @enderror">
                <option value="BAM" {{ old('valuta', $invoice->valuta) == 'BAM' ? 'selected' : '' }}>BAM</option>
                <option value="EUR" {{ old('valuta', $invoice->valuta) == 'EUR' ? 'selected' : '' }}>EUR</option>
            </select>
            @error('valuta')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Ažuriraj</button>
    </form>

    <script>
        // Inicijalizacija Flatpickr za datum_izdavanja
        flatpickr('#datum_izdavanja', {
            dateFormat: 'd.m.Y',
            defaultDate: "{{ old('datum_izdavanja', $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : now()->format('d.m.Y')) }}",
            onChange: function(selectedDates, dateStr, instance) {
                const isoDate = selectedDates[0] ? selectedDates[0].toISOString().split('T')[0] : '';
                document.getElementById('datum_izdavanja').value = dateStr;
                document.getElementById('datum_izdavanja').setAttribute('data-iso', isoDate);
            }
        });

        // Ažuriranje forme prije slanja da se pošalje ISO format
        document.querySelector('form').addEventListener('submit', function(e) {
            const dateInput = document.getElementById('datum_izdavanja');
            const isoDate = dateInput.getAttribute('data-iso');
            if (isoDate) {
                dateInput.value = isoDate;
            }
        });
    </script>
@endsection
