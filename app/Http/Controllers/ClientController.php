<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        // Dobijamo trenutno ulogovanog korisnika
        $user = Auth::user();

        // Filtriramo klijente samo za trenutnog korisnika
        $clients = Client::where('user_id', $user->id)->get();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'naziv_firme' => 'required',
            'adresa' => 'required',
            'postanski_broj_mjesto_drzava' => 'required',
            'pdv_broj' => 'required',
            'email' => 'required|email',
            'kontakt_telefon' => 'required',
        ]);

        // Dobijamo trenutno ulogovanog korisnika
        $user = Auth::user();

        // Kreiramo klijenta i povezujemo ga s trenutnim korisnikom
        Client::create(array_merge($request->all(), ['user_id' => $user->id]));

        return redirect()->route('clients.index')->with('success', 'Klijent kreiran.');
    }

    public function edit(Client $client)
    {
        $user = Auth::user();
        if ($client->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $user = Auth::user();
        if ($client->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'naziv_firme' => 'required',
            'adresa' => 'required',
            'postanski_broj_mjesto_drzava' => 'required',
            'pdv_broj' => 'required',
            'email' => 'required|email',
            'kontakt_telefon' => 'required',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Klijent ažuriran.');
    }

    public function destroy(Client $client)
    {
        $user = Auth::user();
        if ($client->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $client->delete();

            return redirect()->route('clients.index')->with('success', 'Klijent obrisan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Došlo je do greške prilikom brisanja klijenta: '.$e->getMessage());
        }
    }
}
