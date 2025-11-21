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

    <!-- General Settings -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Opća podešavanja</h2>
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

    <!-- Payment Reminder Settings -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Podsetnici za plaćanje</h2>
        <form method="POST" action="{{ route('settings.update-reminders') }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="reminder_enabled" id="reminder_enabled" {{ $user->reminder_enabled ? 'checked' : '' }} class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="reminder_enabled" class="text-gray-700 font-semibold text-sm sm:text-base flex items-center">
                        <i class="fas fa-bell mr-2 text-gray-500"></i> Omogući automatske podsetnice za plaćanje
                    </label>
                </div>
                
                <div class="mb-4">
                    <label for="reminder_interval" class="block text-gray-700 font-semibold mb-2">Interval podsetnika</label>
                    <select name="reminder_interval" id="reminder_interval" class="w-full border p-2 rounded @error('reminder_interval') border-red-500 @enderror">
                        <option value="5" {{ old('reminder_interval', $user->reminder_interval) == 5 ? 'selected' : '' }}>Svakih 5 dana</option>
                        <option value="10" {{ old('reminder_interval', $user->reminder_interval) == 10 ? 'selected' : '' }}>Svakih 10 dana</option>
                    </select>
                    <p class="text-gray-600 text-sm mt-1">Prvi podsetnik šalje se nakon {{ $user->reminder_interval }} dana, drugi nakon {{ $user->reminder_interval * 2 }} dana</p>
                    @error('reminder_interval')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 text-center">
                <button type="submit" class="inline-block bg-purple-500 text-white px-6 py-2 rounded-lg hover:bg-purple-600 transition duration-300 ease-in-out transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                    Sačuvaj podešavanja podsetnika
                </button>
            </div>
        </form>
    </div>

    <!-- SMTP Settings -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-4 text-gray-800">SMTP podešavanja</h2>
        <form method="POST" action="{{ route('settings.update-smtp') }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="smtp_host" class="block text-gray-700 font-semibold mb-2">SMTP Server/Host</label>
                    <input type="text" name="smtp_host" id="smtp_host" value="{{ old('smtp_host', $smtpSettings->smtp_host ?? '') }}" 
                           class="w-full border p-2 rounded @error('smtp_host') border-red-500 @enderror" required>
                    @error('smtp_host')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="smtp_port" class="block text-gray-700 font-semibold mb-2">SMTP Port</label>
                    <input type="number" name="smtp_port" id="smtp_port" value="{{ old('smtp_port', $smtpSettings->smtp_port ?? 587) }}" 
                           class="w-full border p-2 rounded @error('smtp_port') border-red-500 @enderror" required>
                    @error('smtp_port')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="smtp_username" class="block text-gray-700 font-semibold mb-2">SMTP Username</label>
                    <input type="text" name="smtp_username" id="smtp_username" value="{{ old('smtp_username', $smtpSettings->smtp_username ?? '') }}" 
                           class="w-full border p-2 rounded @error('smtp_username') border-red-500 @enderror" required>
                    @error('smtp_username')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="smtp_password" class="block text-gray-700 font-semibold mb-2">SMTP Password</label>
                    <input type="password" name="smtp_password" id="smtp_password" value="" 
                           class="w-full border p-2 rounded @error('smtp_password') border-red-500 @enderror" 
                           placeholder="{{ $smtpSettings ? '••••••••' : '' }}">
                    @if($smtpSettings)
                        <p class="text-gray-600 text-sm mt-1">Ostavite prazno da zadržite trenutnu lozinku</p>
                    @endif
                    @error('smtp_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="from_email" class="block text-gray-700 font-semibold mb-2">From Email Address</label>
                    <input type="email" name="from_email" id="from_email" value="{{ old('from_email', $smtpSettings->from_email ?? '') }}" 
                           class="w-full border p-2 rounded @error('from_email') border-red-500 @enderror" required>
                    @error('from_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="from_name" class="block text-gray-700 font-semibold mb-2">From Name</label>
                    <input type="text" name="from_name" id="from_name" value="{{ old('from_name', $smtpSettings->from_name ?? '') }}" 
                           class="w-full border p-2 rounded @error('from_name') border-red-500 @enderror" required>
                    @error('from_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="encryption" class="block text-gray-700 font-semibold mb-2">Encryption Type</label>
                    <select name="encryption" id="encryption" class="w-full border p-2 rounded @error('encryption') border-red-500 @enderror" required>
                        <option value="tls" {{ old('encryption', $smtpSettings->encryption ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('encryption', $smtpSettings->encryption ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ old('encryption', $smtpSettings->encryption ?? 'tls') == 'none' ? 'selected' : '' }}>None</option>
                    </select>
                    @error('encryption')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 text-center">
                <button type="submit" class="inline-block bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition duration-300 ease-in-out transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                    Sačuvaj SMTP podešavanja
                </button>
            </div>
        </form>
    </div>
@endsection