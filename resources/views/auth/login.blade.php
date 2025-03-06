<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center min-h-screen p-4 sm:p-0">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="flex justify-center mb-6">
            <img src="https://wizionar.com/wp-content/uploads/2023/09/wizionarLogoAsset-7@2x.png" alt="Wizionar Logo" class="w-32 sm:w-40">
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-8 text-center text-gray-800">Prijava</h1>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-5 relative">
                <label for="email" class="block text-gray-700 text-sm sm:text-base font-medium mb-2">Email adresa</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                           class="w-full pl-10 pr-4 py-2 sm:py-3 border rounded-lg text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs sm:text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6 relative">
                <label for="password" class="block text-gray-700 text-sm sm:text-base font-medium mb-2">Lozinka</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password" type="password" name="password" required 
                           class="w-full pl-10 pr-4 py-2 sm:py-3 border rounded-lg text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                </div>
                @error('password')
                    <p class="text-red-500 text-xs sm:text-sm mt-2">{{ $message }}</p>
                @enderror
                <a href="{{ route('password.request') }}" class="block text-blue-500 text-xs sm:text-sm mt-2 hover:underline text-right">Zaboravili ste lozinku?</a>
            </div>
            <div class="flex items-center justify-center">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 sm:py-3 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto text-sm sm:text-base">
                    Prijavi se
                </button>
            </div>
        </form>
    </div>
</body>
</html>