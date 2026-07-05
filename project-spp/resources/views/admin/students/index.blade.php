<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa — Sistem SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">

<nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600">← Dashboard</a>
        <span class="font-semibold text-gray-800">Data Siswa</span>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Keluar</button>
    </form>
</nav>

<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Data Siswa</h1>
            <p class="text-sm text-gray-400 mt-0.5">Total {{ $students->total() }} siswa terdaftar</p>
        </div>
        <a href="{{ route('admin.students.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Tambah Siswa
        </a>
    </div>

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

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">NIS</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Kelas</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Jurusan</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($students as $student)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono text-gray-700">{{ $student->nis }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $student->name }}</div>
                            <div class="text-xs text-gray-400">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $student->schoolClass->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $student->major->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if ($student->user?->must_change_password)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    Belum ganti password
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('admin.students.edit', $student) }}"
                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>

                                <form method="POST"
                                      action="{{ route('admin.students.reset-password', $student) }}"
                                      onsubmit="return confirm('Reset password {{ addslashes($student->name) }} ke NIS?')">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">
                                        Reset PW
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('admin.students.destroy', $student) }}"
                                      onsubmit="return confirm('Hapus siswa {{ addslashes($student->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">
                            Belum ada data siswa.
                            <a href="{{ route('admin.students.create') }}" class="text-blue-600 hover:underline ml-1">Tambah sekarang</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($students->hasPages())
        <div class="mt-4">{{ $students->links() }}</div>
    @endif

</div>
</body>
</html>
