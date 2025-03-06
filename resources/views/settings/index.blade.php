@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Podesavanja</h1>

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
        <form method="POST" action="{{ route('settings.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @csrf
            @method('PUT')
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="send_payment_email" id="send_payment_email" {{ $user->send_payment_email ? 'checked' : '' }} class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="send_payment_email" class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                    <i class="fas fa-envelope mr-2 text-gray-500"></i> Pošalji e-mail prilikom plaćanja fakture
                </label>
                <p class="text-gray-600 text-sm ml-2"> (Hvala vam što izmirujete vaše obaveze)</p>
            </div>
            <div class="mt-6 text-center sm:col-span-2">
                <button type="submit" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                    Sačuvaj podešavanja
                </button>
            </div>
        </form>
    </div>
@endsection