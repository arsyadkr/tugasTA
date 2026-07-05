<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Riwayat Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('siswa.partials.sidebar')
<main style="margin-left:210px">
@include('siswa.partials.topbar',['title'=>'Riwayat Pembayaran'])
<div class="p-6 space-y-5">

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400">Total yang sudah dibayarkan</p>
        <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalDibayar,0,',','.') }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-800">Semua Riwayat Pembayaran</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">No</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Periode</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Metode</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Nominal</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Tgl Bayar</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Bukti</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5 text-xs text-gray-400">{{ $payments->firstItem()+$loop->index }}</td>
                    <td class="px-4 py-3.5 text-xs font-semibold text-gray-800">{{ $p->bill->period_label??'-' }}</td>
                    <td class="px-4 py-3.5 text-xs text-gray-600">{{ $p->payment_type_label }}</td>
                    <td class="px-4 py-3.5 text-xs font-bold text-green-600">Rp {{ number_format($p->amount,0,',','.') }}</td>
                    <td class="px-4 py-3.5 text-xs text-gray-500">{{ $p->paid_at?->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3.5 text-center">
                        <a href="{{ route('siswa.bukti.print',$p) }}"
                           class="text-xs text-blue-600 hover:text-blue-800 font-semibold">
                            🖨 Cetak PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada riwayat pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $payments->links() }}</div>
    </div>
</div>
</main>
</body>
</html>