<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tagihan SPP — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('admin.partials.sidebar')
<main style="margin-left:220px">
@include('admin.partials.topbar', ['title' => 'Tagihan SPP'])
<div class="p-6 space-y-5">

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400">Total Tagihan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($summary['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400">Sudah Lunas</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($summary['paid']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400">Belum Lunas</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($summary['unpaid']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400">Total Terkumpul</p>
            <p class="text-lg font-bold text-blue-600 mt-1">Rp {{ number_format($summary['nominal'],0,',','.') }}</p>
        </div>
    </div>

    {{-- Tab filter cepat --}}
    <div class="flex gap-2">
        <a href="{{ route('admin.tagihan.index') }}"
           class="px-4 py-2 text-sm font-semibold rounded-lg border transition
                  {{ !request('status') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Belum Lunas
        </a>
        <a href="{{ route('admin.tagihan.index', ['status' => 'paid']) }}"
           class="px-4 py-2 text-sm font-semibold rounded-lg border transition
                  {{ request('status') === 'paid' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Sudah Lunas
        </a>
        <a href="{{ route('admin.tagihan.index', ['status' => 'all']) }}"
           class="px-4 py-2 text-sm font-semibold rounded-lg border transition
                  {{ request('status') === 'all' ? 'bg-gray-700 text-white border-gray-700' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            Semua
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div>
                <label class="block text-xs text-gray-500 mb-1">Jurusan</label>
                <select name="major_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Jurusan</option>
                    @foreach($majors as $m)
                        <option value="{{ $m->id }}" {{ request('major_id')==$m->id?'selected':'' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Kelas</label>
                <select name="class_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tahun</label>
                <input type="number" name="year" value="{{ request('year', now()->year) }}" min="2020" max="2099"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700">
                Filter
            </button>
            <a href="{{ route('admin.tagihan.index') }}" class="px-4 py-2 text-sm text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50">
                Reset
            </a>
            <div class="ml-auto">
                <a href="{{ route('admin.tagihan.create') }}"
                   class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700 flex items-center gap-1.5">
                    + Buat Tagihan
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">
                @if(!request('status') || request('status') === '')
                    Tagihan Belum Lunas
                @elseif(request('status') === 'paid')
                    Tagihan Sudah Lunas
                @else
                    Semua Tagihan
                @endif
            </h2>
            <span class="text-xs text-gray-400">{{ $bills->total() }} tagihan</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Siswa</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Kelas / Jurusan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Periode</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Nominal</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Jatuh Tempo</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bills as $bill)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 text-xs">{{ $bill->student->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $bill->student->nis ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        <p>{{ $bill->student->schoolClass->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $bill->student->major->name ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-700">{{ $bill->period_label }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-700">Rp {{ number_format($bill->amount,0,',','.') }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $bill->due_date->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        @switch($bill->status)
                            @case('paid')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">✓ Lunas</span>
                                @break
                            @case('pending')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">Diproses</span>
                                @break
                            @case('overdue')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Jatuh Tempo</span>
                                @break
                            @default
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Belum Bayar</span>
                        @endswitch
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($bill->status !== 'paid')
                        <form method="POST" action="{{ route('admin.tagihan.destroy', $bill) }}"
                              onsubmit="return confirm('Hapus tagihan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                        </form>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">
                        @if(!request('status'))
                            Tidak ada tagihan yang belum lunas. 🎉
                        @else
                            Tidak ada data tagihan.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $bills->links() }}</div>
    </div>

</div>
</main>
</body>
</html>