<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa — Sistem SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">

<nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Data Siswa</a>
        <span class="font-semibold text-gray-800">Edit: {{ $student->name }}</span>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Keluar</button>
    </form>
</nav>

<div class="max-w-3xl mx-auto px-4 py-8">

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg text-sm">
            {{ session('warning') }}
        </div>
    @endif

    {{-- Form edit --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-lg font-semibold text-gray-800">Edit Data Siswa</h1>
                <p class="text-sm text-gray-400 mt-0.5">NIS: {{ $student->nis }}</p>
            </div>
            @if ($student->user?->must_change_password)
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                    Belum ganti password
                </span>
            @else
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    Akun aktif
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.students.update', $student) }}" novalidate>
            @csrf
            @method('PUT')

            @include('admin.students._form')

            <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Batal
                </a>
            </div>
        </form>

    </div>

    {{-- Reset password panel --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-1">Reset Password</h2>
        <p class="text-sm text-gray-500 mb-3">
            Reset password siswa ke NIS (<code class="font-mono bg-gray-100 px-1 rounded">{{ $student->nis }}</code>).
            Siswa akan diminta ganti password saat login berikutnya.
        </p>
        <form method="POST" action="{{ route('admin.students.reset-password', $student) }}"
              onsubmit="return confirm('Reset password {{ addslashes($student->name) }} ke NIS?')">
            @csrf
            <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                Reset Password ke NIS
            </button>
        </form>
    </div>

</div>
</body>
</html>
