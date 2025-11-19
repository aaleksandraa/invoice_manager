<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile ?? new CompanyProfile(['user_id' => $user->id]);

        return view('company-profiles.index', compact('companyProfile'));
    }

    public function edit()
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile ?? new CompanyProfile(['user_id' => $user->id]);

        return view('company-profiles.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile ?? new CompanyProfile(['user_id' => $user->id]);

        \Log::info('CompanyProfile before update:', ['companyProfile' => $companyProfile->toArray(), 'user_id' => $user->id]);

        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code_city_country' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:34',
            'swift' => 'nullable|string|max:11',
        ]);

        $companyProfile->fill($request->all()); // Ispunimo podatke
        $isSaved = $companyProfile->save(); // Eksplicitno sačuvaj u bazu
        \Log::info('CompanyProfile after update:', ['companyProfile' => $companyProfile->toArray(), 'saved' => $isSaved]);

        return redirect()->route('company-profile.index')->with('success', 'Profil firme ažuriran.');
    }
}
