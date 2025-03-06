@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Moj profil</h1>

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

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-building mr-2 text-gray-500"></i> Naziv firme:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->company_name ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i> Adresa:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->address ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-globe mr-2 text-gray-500"></i> Poštanski broj/Mjesto/Država:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->postal_code_city_country ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-file-invoice mr-2 text-gray-500"></i> PDV broj:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->tax_number ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-envelope mr-2 text-gray-500"></i> Email:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->email ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-phone mr-2 text-gray-500"></i> Telefon:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->phone ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-university mr-2 text-gray-500"></i> Naziv banke:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->bank_name ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-credit-card mr-2 text-gray-500"></i> Broj računa:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->account_number ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-barcode mr-2 text-gray-500"></i> IBAN:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->iban ?? 'Nije postavljeno' }}</p>
            </div>
            <div class="mb-4">
                <p class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-exchange-alt mr-2 text-gray-500"></i> SWIFT:
                </p>
                <p class="text-gray-800 text-sm sm:text-base">{{ $companyProfile->swift ?? 'Nije postavljeno' }}</p>
            </div>
        </div>        
    </div>
    <!-- Dugme "Uredi profil" sa animacijom -->
    <div class="p-6 text-left">
        <a href="{{ route('company-profile.edit') }}" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out transform hover:-translate-y-1 shadow-md hover:shadow-lg">
            Uredi profil
        </a>
    </div>
@endsection