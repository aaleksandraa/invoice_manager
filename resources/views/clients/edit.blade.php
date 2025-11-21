@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Uredi klijenta</h1>
    <form method="POST" action="{{ route('clients.update', $client) }}" class="bg-white p-6 rounded shadow-md">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="naziv_firme" class="block text-gray-700 font-semibold mb-2">Naziv firme</label>
            <input type="text" name="naziv_firme" id="naziv_firme" value="{{ old('naziv_firme', $client->naziv_firme) }}" class="w-full border p-2 rounded @error('naziv_firme') border-red-500 @enderror" required>
            @error('naziv_firme')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="adresa" class="block text-gray-700 font-semibold mb-2">Adresa</label>
            <input type="text" name="adresa" id="adresa" value="{{ old('adresa', $client->adresa) }}" class="w-full border p-2 rounded @error('adresa') border-red-500 @enderror" required>
            @error('adresa')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="postanski_broj_mjesto_drzava" class="block text-gray-700 font-semibold mb-2">Poštanski broj, mjesto, država</label>
            <input type="text" name="postanski_broj_mjesto_drzava" id="postanski_broj_mjesto_drzava" value="{{ old('postanski_broj_mjesto_drzava', $client->postanski_broj_mjesto_drzava) }}" class="w-full border p-2 rounded @error('postanski_broj_mjesto_drzava') border-red-500 @enderror" required>
            @error('postanski_broj_mjesto_drzava')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="pdv_broj" class="block text-gray-700 font-semibold mb-2">PDV broj</label>
            <input type="text" name="pdv_broj" id="pdv_broj" value="{{ old('pdv_broj', $client->pdv_broj) }}" class="w-full border p-2 rounded @error('pdv_broj') border-red-500 @enderror" required>
            @error('pdv_broj')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}" class="w-full border p-2 rounded @error('email') border-red-500 @enderror" required>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="kontakt_telefon" class="block text-gray-700 font-semibold mb-2">Kontakt telefon</label>
            <input type="text" name="kontakt_telefon" id="kontakt_telefon" value="{{ old('kontakt_telefon', $client->kontakt_telefon) }}" class="w-full border p-2 rounded @error('kontakt_telefon') border-red-500 @enderror" required>
            @error('kontakt_telefon')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">Ažuriraj klijenta</button>
        <a href="{{ route('clients.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 ml-2">Odustani</a>
    </form>
@endsection
