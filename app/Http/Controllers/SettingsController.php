<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $smtpSettings = SmtpSetting::where('user_id', $user->id)->first();

        return view('settings.index', compact('user', 'smtpSettings'));
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

    public function updateSmtp(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'from_email' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'encryption' => 'required|in:tls,ssl,none',
        ]);

        $smtpSettings = SmtpSetting::where('user_id', $user->id)->first();

        // If password is not provided and settings exist, keep the old password
        if (! $request->filled('smtp_password') && $smtpSettings) {
            unset($validated['smtp_password']);
        }

        if ($smtpSettings) {
            $smtpSettings->update($validated);
        } else {
            $validated['user_id'] = $user->id;
            SmtpSetting::create($validated);
        }

        return redirect()->route('settings.index')->with('success', 'SMTP podešavanja su uspješno sačuvana.');
    }
}
