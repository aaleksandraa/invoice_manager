<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'send_payment_email' => 'boolean',
        ]);

        $user->update([
            'send_payment_email' => $request->has('send_payment_email'),
        ]);

        return redirect()->route('settings.index')->with('success', 'Podešavanja su uspješno ažurirana.');
    }
}