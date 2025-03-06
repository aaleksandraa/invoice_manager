@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Lista klijenata</h1>
        <a href="{{ route('clients.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">Novi klijent</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="p-3 text-left">Naziv firme</th>
                    <th class="p-3 text-left">Adresa</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Telefon</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                    <tr class="border-b">
                        <td class="p-3">{{ $client->naziv_firme }}</td>
                        <td class="p-3">{{ $client->postanski_broj_mjesto_drzava }}</td>
                        <td class="p-3">{{ $client->email }}</td>
                        <td class="p-3">{{ $client->kontakt_telefon }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection