@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Dodaj klijenta</h1>
    <form method="POST" action="{{ route('clients.store') }}" class="bg-white p-6 rounded shadow-md">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Naziv firme</label>
            <input type="text" name="naziv_firme" class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Adresa</label>
            <input type="text" name="adresa" class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Poštanski broj, mjesto, država</label>
            <input type="text" name="postanski_broj_mjesto_drzava" class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">PDV broj</label>
            <input type="text" name="pdv_broj" class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" class="w-full border p-2 rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Kontakt telefon</label>
            <input type="text" name="kontakt_telefon" class="w-full border p-2 rounded">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Spremi</button>
    </form>
@endsection