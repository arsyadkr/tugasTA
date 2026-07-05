<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa — Sistem SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">

<nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Data Siswa</a>
        <span class="font-semibold text-gray-800">Tambah Siswa</span>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Keluar</button>
    </form>
</nav>

<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

        <h1 class="text-lg font-semibold text-gray-800 mb-6">Tambah Siswa Baru</h1>

        <form method="POST" action="{{ route('admin.students.store') }}" novalidate>
            @csrf

            @include('admin.students._form')

            <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Simpan Siswa
                </button>
                <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Batal
                </a>
            </div>
        </form>

    </div>
</div>
</body>
</html>
