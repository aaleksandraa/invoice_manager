<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    public function updateReminders(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'reminder_enabled' => 'boolean',
            'reminder_interval' => 'required|integer|in:5,10',
        ]);

        // Handle checkbox: if not present, it means unchecked
        $validated['reminder_enabled'] = $request->has('reminder_enabled');

        $user->update($validated);

        return redirect()->route('settings.index')->with('success', 'Podešavanja podsetnika su uspješno sačuvana.');
    }

    public function testSmtp(Request $request)
    {
        $user = Auth::user();
        $smtpSettings = SmtpSetting::where('user_id', $user->id)->first();

        if (! $smtpSettings) {
            return response()->json([
                'success' => false,
                'message' => 'SMTP podešavanja nisu konfigurisana. Molimo prvo sačuvajte SMTP podešavanja.',
            ]);
        }

        try {
            // Configure SMTP for this user
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $smtpSettings->smtp_host);
            Config::set('mail.mailers.smtp.port', $smtpSettings->smtp_port);
            Config::set('mail.mailers.smtp.username', $smtpSettings->smtp_username);
            Config::set('mail.mailers.smtp.password', $smtpSettings->smtp_password);
            Config::set('mail.mailers.smtp.encryption', $smtpSettings->encryption === 'none' ? null : $smtpSettings->encryption);
            Config::set('mail.from.address', $smtpSettings->from_email);
            Config::set('mail.from.name', $smtpSettings->from_name);

            // Send test email to the logged-in user
            Mail::raw('Ovo je testni email za provjeru SMTP konekcije. Ako vidite ovu poruku, vaša SMTP konfiguracija radi ispravno.', function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Test SMTP Konekcije - Invoice Manager');
            });

            Log::info('SMTP test email sent successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test email je uspješno poslan na adresu: '.$user->email.'. Molimo provjerite svoj inbox.',
            ]);
        } catch (\Exception $e) {
            Log::error('SMTP test failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Greška pri slanju test emaila: '.$e->getMessage(),
            ]);
        }
    }
}
