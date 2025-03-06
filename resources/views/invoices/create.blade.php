@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Nova faktura</h1>

    <!-- Prikaz poruka o uspjehu, greškama ili upozorenjima -->
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

    <form method="POST" action="{{ route('invoices.store') }}">
        @csrf
        <div class="mb-4">
            <label for="klijent_id" class="block text-gray-700">Klijent</label>
            <select id="klijent_id" name="klijent_id" class="w-full border p-2 rounded @error('klijent_id') border-red-500 @enderror">
                <option value="">Odaberite klijenta</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ old('klijent_id') == $client->id ? 'selected' : '' }}>
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
            <input id="broj_fakture" type="text" name="broj_fakture" value="{{ $broj_fakture }}" readonly class="w-full border p-2 rounded bg-gray-100 @error('broj_fakture') border-red-500 @enderror">
            @error('broj_fakture')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="datum_izdavanja" class="block text-gray-700">Datum izdavanja</label>
            <input id="datum_izdavanja" type="date" name="datum_izdavanja" value="{{ old('datum_izdavanja', now()->format('Y-m-d')) }}" class="w-full border p-2 rounded @error('datum_izdavanja') border-red-500 @enderror">
            @error('datum_izdavanja')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="opis_posla" class="block text-gray-700">Opis posla</label>
            <textarea id="opis_posla" name="opis_posla" class="w-full border p-2 rounded @error('opis_posla') border-red-500 @enderror">{{ old('opis_posla') }}</textarea>
            @error('opis_posla')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="kolicina" class="block text-gray-700">Količina</label>
            <input id="kolicina" type="number" name="kolicina" value="{{ old('kolicina', 1) }}" class="w-full border p-2 rounded @error('kolicina') border-red-500 @enderror">
            @error('kolicina')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="cijena" class="block text-gray-700">Cijena</label>
            <input id="cijena" type="number" step="0.01" name="cijena" value="{{ old('cijena') }}" class="w-full border p-2 rounded @error('cijena') border-red-500 @enderror">
            @error('cijena')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="valuta" class="block text-gray-700">Valuta</label>
            <select id="valuta" name="valuta" class="w-full border p-2 rounded @error('valuta') border-red-500 @enderror">
                <option value="BAM" {{ old('valuta', 'BAM') == 'BAM' ? 'selected' : '' }}>BAM</option>
                <option value="EUR" {{ old('valuta') == 'EUR' ? 'selected' : '' }}>EUR</option>
            </select>
            @error('valuta')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Kreiraj</button>
    </form>
@endsection