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
}