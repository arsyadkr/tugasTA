<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password — Sistem SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

<div class="w-full max-w-md">

    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Ganti Password</h1>
        <p class="text-gray-500 text-sm mt-1">Demi keamanan, ganti password default Anda sekarang</p>
    </div>

    {{-- Warning Banner --}}
    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg text-sm flex gap-2">
        <span>⚠️</span>
        <span>Password default Anda adalah NIS. Segera ganti untuk melindungi akun Anda.</span>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Info user --}}
        <div class="mb-5 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
            Login sebagai: <strong>{{ auth()->user()->name }}</strong>
            <span class="text-gray-400">({{ auth()->user()->role === 'admin' ? 'Admin' : 'Siswa' }})</span>
        </div>

        <form method="POST" action="{{ route('password.change.update') }}">
            @csrf

            {{-- Password Baru --}}
            <div class="mb-5">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password Baru
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                    autofocus
                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                           {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Konfirmasi Password Baru
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password"
                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300"
                >
                @error('password_confirmation')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg
                       transition duration-150 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Simpan Password Baru
            </button>

        </form>

        <div class="mt-4 text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 underline">
                    Keluar dari akun ini
                </button>
            </form>
        </div>

    </div>

</div>

</body>
</html>
