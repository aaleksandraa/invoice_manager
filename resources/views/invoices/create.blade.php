@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Kreiraj fakturu</h1>
    <form method="POST" action="{{ route('invoices.store') }}" class="bg-white p-6 rounded shadow-md">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Klijent</label>
            <select name="klijent_id" class="w-full border p-2 rounded">
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->naziv_firme }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Broj fakture</label>
            <input type="text" name="broj_fakture" value="{{ $broj_fakture }}" class="w-full border p-2 rounded" readonly>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Datum izdavanja</label>
            <input type="date" name="datum_izdavanja" value="{{ now()->format('Y-m-d') }}" class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Opis posla</label>
            <textarea name="opis_posla" class="w-full border p-2 rounded"></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Količina</label>
            <input type="number" name="kolicina" value="1" class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Cijena</label>
            <input type="number" name="cijena" step="0.01" class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Valuta</label>
            <select name="valuta" class="w-full border p-2 rounded">
                <option value="BAM">BAM</option>
                <option value="EUR">EUR</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Spremi</button>
    </form>
@endsection