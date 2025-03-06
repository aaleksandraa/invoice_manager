@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Moj profil - Uređivanje</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 shadow-md">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 shadow-md">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('company-profile.update') }}" class="bg-white p-6 rounded-lg shadow-lg">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="mb-4">
                <label for="company_name" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-building mr-2 text-gray-500"></i> Naziv firme
                </label>
                <input id="company_name" type="text" name="company_name" value="{{ old('company_name', $companyProfile->company_name) }}" class="w-full border p-2 rounded-lg @error('company_name') border-red-500 @enderror">
                @error('company_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i> Adresa
                </label>
                <input id="address" type="text" name="address" value="{{ old('address', $companyProfile->address) }}" class="w-full border p-2 rounded-lg @error('address') border-red-500 @enderror">
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="postal_code_city_country" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-globe mr-2 text-gray-500"></i> Poštanski broj/Mjesto/Država
                </label>
                <input id="postal_code_city_country" type="text" name="postal_code_city_country" value="{{ old('postal_code_city_country', $companyProfile->postal_code_city_country) }}" class="w-full border p-2 rounded-lg @error('postal_code_city_country') border-red-500 @enderror">
                @error('postal_code_city_country')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="tax_number" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-file-invoice mr-2 text-gray-500"></i> PDV broj
                </label>
                <input id="tax_number" type="text" name="tax_number" value="{{ old('tax_number', $companyProfile->tax_number) }}" class="w-full border p-2 rounded-lg @error('tax_number') border-red-500 @enderror">
                @error('tax_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-envelope mr-2 text-gray-500"></i> Email
                </label>
                <input id="email" type="email" name="email" value="{{ old('email', $companyProfile->email) }}" class="w-full border p-2 rounded-lg @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-phone mr-2 text-gray-500"></i> Telefon
                </label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $companyProfile->phone) }}" class="w-full border p-2 rounded-lg @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="bank_name" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-university mr-2 text-gray-500"></i> Naziv banke
                </label>
                <input id="bank_name" type="text" name="bank_name" value="{{ old('bank_name', $companyProfile->bank_name) }}" class="w-full border p-2 rounded-lg @error('bank_name') border-red-500 @enderror">
                @error('bank_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="account_number" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-credit-card mr-2 text-gray-500"></i> Broj računa
                </label>
                <input id="account_number" type="text" name="account_number" value="{{ old('account_number', $companyProfile->account_number) }}" class="w-full border p-2 rounded-lg @error('account_number') border-red-500 @enderror">
                @error('account_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="iban" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-barcode mr-2 text-gray-500"></i> IBAN (opciono)
                </label>
                <input id="iban" type="text" name="iban" value="{{ old('iban', $companyProfile->iban) }}" class="w-full border p-2 rounded-lg @error('iban') border-red-500 @enderror">
                @error('iban')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
            <div class="mb-4">
                <label for="swift" class="block text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-exchange-alt mr-2 text-gray-500"></i> SWIFT (opciono)
                </label>
                <input id="swift" type="text" name="swift" value="{{ old('swift', $companyProfile->swift) }}" class="w-full border p-2 rounded-lg @error('swift') border-red-500 @enderror">
                @error('swift')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @endif
            </div>
        </div>
        <div class="mt-6 text-center">
            <button type="submit" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                Spremi promjene
            </button>
        </div>
    </form>
@endsection