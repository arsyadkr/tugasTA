<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pembayaran Khusus — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('admin.partials.sidebar')
<main style="margin-left:220px">
@include('admin.partials.topbar', ['title' => 'Pembayaran Khusus'])
<div class="p-6 space-y-5">

    {{-- 3 Summary Cards per jenis --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Kunjungan Industri (Kelas 10) --}}
        <div class="rounded-xl p-5 text-white" style="background:linear-gradient(135deg,#667eea,#764ba2)">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-200 uppercase tracking-wide">Kelas 10</p>
                    <h3 class="text-base font-bold mt-0.5">Kunjungan Industri</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="flex gap-4 mt-4 text-sm">
                <div><p class="text-purple-200 text-xs">Total</p><p class="font-bold text-lg">{{ $summary['kunjungan_industri']['total'] }}</p></div>
                <div><p class="text-purple-200 text-xs">Lunas</p><p class="font-bold text-lg">{{ $summary['kunjungan_industri']['paid'] }}</p></div>
                <div><p class="text-purple-200 text-xs">Belum</p><p class="font-bold text-lg">{{ $summary['kunjungan_industri']['unpaid'] }}</p></div>
            </div>
            <a href="{{ route('admin.extra.index', ['type' => 'kunjungan_industri']) }}"
               class="inline-block mt-3 text-xs font-semibold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition">
                Lihat Tagihan →
            </a>
        </div>

        {{-- GTS (Kelas 11) --}}
        <div class="rounded-xl p-5 text-white" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-pink-200 uppercase tracking-wide">Kelas 11</p>
                    <h3 class="text-base font-bold mt-0.5">GTS (Go To School)</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
            </div>
            <div class="flex gap-4 mt-4 text-sm">
                <div><p class="text-pink-200 text-xs">Total</p><p class="font-bold text-lg">{{ $summary['gts']['total'] }}</p></div>
                <div><p class="text-pink-200 text-xs">Lunas</p><p class="font-bold text-lg">{{ $summary['gts']['paid'] }}</p></div>
                <div><p class="text-pink-200 text-xs">Belum</p><p class="font-bold text-lg">{{ $summary['gts']['unpaid'] }}</p></div>
            </div>
            <a href="{{ route('admin.extra.index', ['type' => 'gts']) }}"
               class="inline-block mt-3 text-xs font-semibold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition">
                Lihat Tagihan →
            </a>
        </div>

        {{-- PKL (Kelas 12) --}}
        <div class="rounded-xl p-5 text-white" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-100 uppercase tracking-wide">Kelas 12</p>
                    <h3 class="text-base font-bold mt-0.5">PKL (Praktek Kerja Lapangan)</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="flex gap-4 mt-4 text-sm">
                <div><p class="text-blue-100 text-xs">Total</p><p class="font-bold text-lg">{{ $summary['pkl']['total'] }}</p></div>
                <div><p class="text-blue-100 text-xs">Lunas</p><p class="font-bold text-lg">{{ $summary['pkl']['paid'] }}</p></div>
                <div><p class="text-blue-100 text-xs">Belum</p><p class="font-bold text-lg">{{ $summary['pkl']['unpaid'] }}</p></div>
            </div>
            <a href="{{ route('admin.extra.index', ['type' => 'pkl']) }}"
               class="inline-block mt-3 text-xs font-semibold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition">
                Lihat Tagihan →
            </a>
        </div>
    </div>

    {{-- Filter + Tombol Buat --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Jenis Pembayaran</label>
                <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="kunjungan_industri" {{ request('type')==='kunjungan_industri'?'selected':'' }}>Kunjungan Industri (Kls 10)</option>
                    <option value="gts" {{ request('type')==='gts'?'selected':'' }}>GTS (Kls 11)</option>
                    <option value="pkl" {{ request('type')==='pkl'?'selected':'' }}>PKL (Kls 12)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Belum Lunas</option>
                    <option value="paid" {{ request('status')==='paid'?'selected':'' }}>Sudah Lunas</option>
                    <option value="all" {{ request('status')==='all'?'selected':'' }}>Semua</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Kelas</label>
                <select name="class_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700">Filter</button>
            <a href="{{ route('admin.extra.index') }}" class="px-4 py-2 text-sm text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50">Reset</a>
            <div class="ml-auto flex gap-2">
                <a href="{{ route('admin.extra.payments') }}"
                   class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-green-600 hover:bg-green-700 flex items-center gap-1.5">
                    Riwayat Pembayaran
                </a>
                <a href="{{ route('admin.extra.create') }}"
                   class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700 flex items-center gap-1.5">
                    + Generate Tagihan
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))<div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>@endif

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Siswa</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Kelas</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Jenis</th>
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
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $bill->student->schoolClass->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $colors = ['kunjungan_industri'=>'bg-purple-100 text-purple-700','gts'=>'bg-pink-100 text-pink-700','pkl'=>'bg-blue-100 text-blue-700'];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $colors[$bill->type] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $bill->type_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-700">Rp {{ number_format($bill->amount,0,',','.') }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $bill->due_date->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        @switch($bill->status)
                            @case('paid')<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">✓ Lunas</span>@break
                            @case('pending')<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">Diproses</span>@break
                            @case('overdue')<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Jatuh Tempo</span>@break
                            @default<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Belum Bayar</span>
                        @endswitch
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($bill->status !== 'paid')
                        <form method="POST" action="{{ route('admin.extra.destroy', $bill) }}"
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
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">Tidak ada data tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $bills->links() }}</div>
    </div>

</div>
</main>
</body>
</html>