<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('admin.partials.sidebar')
<main style="margin-left:220px">
@include('admin.partials.topbar',['title'=>'Laporan Pemasukan'])
<div class="p-6 space-y-5">

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tahun</label>
                <input type="number" name="year" value="{{ $year }}" min="2020"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
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
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-blue-600">Tampilkan</button>
            <a href="{{ route('admin.laporan.export', request()->query()) }}"
               class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-green-600 hover:bg-green-700 flex items-center gap-1.5">
                ↓ Export Excel
            </a>
        </form>
    </div>

    {{-- Rekap bulanan --}}
    @php $namaBulan=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']; @endphp
    <div class="grid grid-cols-3 lg:grid-cols-6 gap-3">
        @for($m=1;$m<=12;$m++)
        @php $r=$rekapBulanan->get($m); @endphp
        <div class="bg-white rounded-xl border {{ $m==now()->month&&$year==now()->year?'border-blue-300 ring-1 ring-blue-200':'border-gray-200' }} p-3">
            <p class="text-xs font-semibold text-gray-400">{{ $namaBulan[$m-1] }}</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $r?'Rp '.number_format($r['total'],0,',','.'):'—' }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $r?$r['count'].' trx':'0 trx' }}</p>
        </div>
        @endfor
    </div>

    {{-- Total --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-400">Total Pemasukan {{ $year }}</p>
            <p class="text-3xl font-bold text-green-600 mt-1">Rp {{ number_format($payments->sum('amount'),0,',','.') }}</p>
        </div>
        <div class="text-right">
            <p class="text-xl font-bold text-gray-700">{{ $payments->count() }}</p>
            <p class="text-xs text-gray-400">total transaksi</p>
        </div>
    </div>

    {{-- Tabel detail --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-800">Detail Transaksi {{ $year }}</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">No</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Tgl Bayar</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Siswa</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Kelas</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Periode</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Metode</th>
                    <th class="text-right px-4 py-2.5 text-xs font-semibold text-gray-400 uppercase">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $i=>$p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $i+1 }}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $p->paid_at?->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <p class="text-xs font-semibold text-gray-800">{{ $p->student->name??'-' }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $p->student->nis??'-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $p->student->schoolClass->name??'-' }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-700">{{ $p->bill->period_label??'-' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $p->payment_type_label }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-green-600 text-right">Rp {{ number_format($p->amount,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400 text-sm">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
            @if($payments->isNotEmpty())
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="6" class="px-4 py-3 text-xs font-bold text-gray-700 text-right">TOTAL</td>
                    <td class="px-4 py-3 text-sm font-bold text-green-600 text-right">Rp {{ number_format($payments->sum('amount'),0,',','.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
</main>
</body>
</html>