<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clients = Client::all();
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

        Client::create($request->all());
        return redirect()->route('clients.index')->with('success', 'Klijent dodan.');
    }
}