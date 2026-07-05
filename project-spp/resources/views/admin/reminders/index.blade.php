<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reminder Tagihan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('admin.partials.sidebar')
<main style="margin-left:220px">
@include('admin.partials.topbar', ['title' => 'Reminder Tagihan'])
<div class="p-6 space-y-5">

    @if(session('success'))<div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>@endif

    {{-- Statistik --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-xs text-gray-400">Total Reminder</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-xs text-gray-400">Berhasil</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['sent']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-xs text-gray-400">Gagal</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($stats['failed']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-xs text-gray-400">Via WhatsApp</p>
            <p class="text-2xl font-bold text-green-500 mt-1">{{ number_format($stats['wa']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-xs text-gray-400">Via Email</p>
            <p class="text-2xl font-bold text-blue-500 mt-1">{{ number_format($stats['email']) }}</p>
        </div>
    </div>

    {{-- Tagihan mendekati jatuh tempo --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Tagihan Mendekati Jatuh Tempo</h2>
                <p class="text-xs text-gray-400 mt-0.5">7 hari ke depan — bisa dikirim reminder manual</p>
            </div>
            <form method="POST" action="{{ route('admin.reminders.send-bulk') }}">
                @csrf
                <input type="hidden" name="days" value="7">
                <button type="submit"
                        onclick="return confirm('Kirim reminder ke semua tagihan jatuh tempo 7 hari lagi?')"
                        class="px-4 py-2 text-xs font-semibold text-white rounded-lg bg-green-600 hover:bg-green-700 flex items-center gap-1.5">
                    📤 Kirim Semua Reminder
                </button>
            </form>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Siswa</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Periode</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Nominal</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Jatuh Tempo</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Kontak Ortu</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Kirim</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($upcoming as $bill)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 text-xs">{{ $bill->student->name }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $bill->student->nis }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-700">{{ $bill->period_label }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-700">Rp {{ number_format($bill->amount,0,',','.') }}</td>
                    <td class="px-4 py-3">
                        @php $diff = now()->diffInDays($bill->due_date, false) @endphp
                        <p class="text-xs font-semibold {{ $diff <= 3 ? 'text-red-600' : 'text-amber-600' }}">
                            {{ $bill->due_date->format('d M Y') }}
                        </p>
                        <p class="text-[10px] text-gray-400">{{ $diff }} hari lagi</p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-0.5">
                            @if($bill->student->parent_phone)
                                <span class="text-[10px] text-green-600">📱 {{ $bill->student->parent_phone }}</span>
                            @else
                                <span class="text-[10px] text-gray-300">📱 —</span>
                            @endif
                            @if($bill->student->parent_email)
                                <span class="text-[10px] text-blue-600">✉️ {{ $bill->student->parent_email }}</span>
                            @else
                                <span class="text-[10px] text-gray-300">✉️ —</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($bill->student->parent_phone || $bill->student->parent_email)
                        <form method="POST" action="{{ route('admin.reminders.send', $bill) }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700">
                                Kirim
                            </button>
                        </form>
                        @else
                        <span class="text-[10px] text-gray-300">No kontak</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">Tidak ada tagihan jatuh tempo 7 hari ke depan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Log Reminder --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-800">Log Pengiriman Reminder</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Waktu</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Siswa</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Tagihan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Channel</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <p class="text-xs font-semibold text-gray-800">{{ $log->student->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $log->student->nis ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700">{{ $log->bill->period_label ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($log->channel === 'whatsapp')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">📱 WhatsApp</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">✉️ Email</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($log->status === 'sent')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">✓ Terkirim</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700" title="{{ $log->error_message }}">✗ Gagal</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada log reminder.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $logs->links() }}</div>
    </div>

</div>
</main>
</body>
</html>