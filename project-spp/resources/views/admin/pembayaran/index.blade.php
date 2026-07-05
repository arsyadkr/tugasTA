<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('admin.partials.sidebar')
<main style="margin-left:220px">
@include('admin.partials.topbar',['title'=>'Data Pembayaran'])
<div class="p-6 space-y-5">

    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-400">Total Pemasukan Keseluruhan</p>
            <p class="text-2xl font-bold text-green-600 mt-0.5">Rp {{ number_format($totalPemasukan,0,',','.') }}</p>
        </div>
        <div class="text-right text-sm text-gray-400">{{ $payments->total() }} transaksi</div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Jurusan</label>
                <select name="major_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    @foreach($majors as $m)<option value="{{ $m->id }}" {{ request('major_id')==$m->id?'selected':'' }}>{{ $m->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Kelas</label>
                <select name="class_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    @foreach($classes as $c)<option value="{{ $c->id }}" {{ request('class_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Bulan</label>
                <select name="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i=>$m)
                    <option value="{{ $i+1 }}" {{ request('month')==$i+1?'selected':'' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tahun</label>
                <input type="number" name="year" value="{{ request('year',now()->year) }}" min="2020"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-blue-600">Filter</button>
            <a href="{{ route('admin.pembayaran.index') }}" class="px-4 py-2 text-sm text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Siswa</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Kelas / Jurusan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Periode</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Metode</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Nominal</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Tgl Bayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 text-xs">{{ $p->student->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $p->student->nis ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        <p>{{ $p->student->schoolClass->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $p->student->major->name ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-700">{{ $p->bill->period_label ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $p->payment_type_label }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-green-600">Rp {{ number_format($p->amount,0,',','.') }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $p->paid_at?->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">Tidak ada data pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $payments->links() }}</div>
    </div>
</div>
</main>
</body>
</html>