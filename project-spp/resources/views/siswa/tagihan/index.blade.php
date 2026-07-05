<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tagihan SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .row-fade { transition: all 0.5s ease; overflow: hidden; }
        .row-fade.removing { opacity: 0; transform: translateX(30px); }
    </style>
    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body class="bg-gray-50 min-h-screen">

@include('siswa.partials.sidebar')

<main style="margin-left:210px">
@include('siswa.partials.topbar', ['title' => 'Tagihan SPP'])

<div class="p-6 space-y-5">

    {{-- Progres --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-sm font-bold text-gray-700">Progres Pembayaran SPP</h2>
            <span class="text-sm font-bold {{ $persentase>=100?'text-green-600':($persentase>=50?'text-blue-600':'text-red-500') }}">
                {{ $persentase }}%
            </span>
        </div>
        <div class="h-3 bg-gray-100 rounded-full overflow-hidden mb-2">
            <div class="h-full rounded-full {{ $persentase>=100?'bg-green-500':($persentase>=50?'bg-blue-500':'bg-red-400') }}"
                 style="width:{{ min($persentase,100) }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>{{ $jumlahLunas }} dari {{ $jumlahTotal }} tagihan lunas</span>
            <span>Dibayar: Rp {{ number_format($totalDibayar,0,',','.') }} / Rp {{ number_format($totalTagihan,0,',','.') }}</span>
        </div>
    </div>

    {{-- Banner sukses --}}
    <div id="pay-banner" class="hidden p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-green-700">Pembayaran berhasil!</p>
            <p class="text-xs text-green-600 mt-0.5">Tagihan telah lunas dan masuk ke Riwayat Pembayaran.</p>
        </div>
        <a href="{{ route('siswa.riwayat.index') }}"
           class="ml-auto text-xs font-semibold text-white px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700">
            Lihat Riwayat
        </a>
    </div>

    @if(session('error'))
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    {{-- Empty state: semua lunas --}}
    @if ($bills->isEmpty())
        <div class="bg-white rounded-xl border border-green-200 shadow-sm px-6 py-14 text-center">
            <div class="text-5xl mb-4">🎉</div>
            <h2 class="text-base font-bold text-green-700 mb-2">Semua Tagihan Lunas!</h2>
            <p class="text-sm text-gray-400 mb-5">Tidak ada tagihan yang perlu dibayar saat ini.</p>
            <a href="{{ route('siswa.riwayat.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-white px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700">
                Lihat Riwayat Pembayaran
            </a>
        </div>

    @else

        {{-- Tabel tagihan belum lunas --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Tagihan Belum Lunas</h2>
                    <p class="text-xs text-gray-400 mt-0.5" id="tagihan-count">{{ $jumlahBelumLunas }} tagihan perlu dibayar</p>
                </div>
                <a href="{{ route('siswa.riwayat.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                    Riwayat Pembayaran →
                </a>
            </div>

            <table class="w-full text-sm" id="tagihan-table">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Periode</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Nominal</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Jatuh Tempo</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Status</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tagihan-body" class="divide-y divide-gray-50">
                    @foreach ($bills as $bill)
                    <tr class="hover:bg-gray-50 row-fade {{ $bill->status==='overdue'?'bg-red-50/30':'' }}"
                        id="row-{{ $bill->id }}"
                        data-bill-id="{{ $bill->id }}">
                        <td class="px-5 py-3.5 font-semibold text-gray-800 text-xs">{{ $bill->period_label }}</td>
                        <td class="px-4 py-3.5 text-xs font-semibold text-gray-700">
                            Rp {{ number_format($bill->amount,0,',','.') }}
                        </td>
                        <td class="px-4 py-3.5 text-xs {{ $bill->status==='overdue'?'text-red-500 font-semibold':'text-gray-400' }}">
                            {{ $bill->due_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            @switch($bill->status)
                                @case('pending')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">⏳ Diproses</span>
                                    @break
                                @case('overdue')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">! Jatuh Tempo</span>
                                    @break
                                @default
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Belum Bayar</span>
                            @endswitch
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Bayar --}}
                                <button onclick="openSnap({{ $bill->id }})"
                                        id="pay-btn-{{ $bill->id }}"
                                        class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 transition">
                                    {{ $bill->status === 'pending' ? 'Lanjutkan' : 'Bayar Sekarang' }}
                                </button>

                                {{-- Tombol Batal (hanya jika pending) --}}
                                @if ($bill->status === 'pending')
                                <button onclick="cancelPayment({{ $bill->id }})"
                                        id="cancel-btn-{{ $bill->id }}"
                                        class="text-xs font-semibold text-red-500 hover:text-red-700 px-2 py-1.5 rounded-lg border border-red-200 hover:border-red-300 transition">
                                    Batal
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400" id="footer-count">{{ $jumlahBelumLunas }} tagihan belum lunas</span>
                <span class="text-xs text-gray-500">
                    Sisa: <span class="font-bold text-red-600" id="sisa-nominal">
                        Rp {{ number_format($totalTagihan - $totalDibayar,0,',','.') }}
                    </span>
                </span>
            </div>
        </div>

    @endif

</div>
</main>

<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const BASE    = '{{ url("siswa/tagihan") }}';
let paidCount = {{ $jumlahLunas }};
let totalCount= {{ $jumlahTotal }};
let pendingCount = {{ $jumlahBelumLunas }};

// ── Buka Snap popup ───────────────────────────────────────────────────────────
async function openSnap(billId) {
    const btn  = document.getElementById('pay-btn-' + billId);
    const orig = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Memuat...';
    btn.style.opacity = '.6';

    try {
        const res  = await fetch(`${BASE}/${billId}/bayar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();

        if (!res.ok) {
            alert(data.message || 'Gagal memulai pembayaran.');
            resetBtn(billId, orig);
            return;
        }

        window.snap.pay(data.snap_token, {

            onSuccess: async function(result) {
                console.log('Snap onSuccess', result);
                // Langsung cek status ke server (tidak tunggu callback)
                await handlePaymentSuccess(billId);
            },

            onPending: function(result) {
                console.log('Snap onPending', result);
                alert('Pembayaran sedang diproses. Selesaikan sesuai instruksi.');
                location.reload();
            },

            onError: function(result) {
                console.error('Snap onError', result);
                alert('Pembayaran gagal. Silakan coba lagi.');
                resetBtn(billId, orig);
            },

            onClose: function() {
                console.log('Snap closed');
                resetBtn(billId, orig);
            },

        });

    } catch(e) {
        console.error(e);
        alert('Kesalahan jaringan.');
        resetBtn(billId, orig);
    }
}

// ── Check status ke server setelah Snap onSuccess ────────────────────────────
async function handlePaymentSuccess(billId) {
    try {
        // Cek status ke server (server cek ke Midtrans API langsung)
        const res  = await fetch(`${BASE}/${billId}/status`, {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        const data = await res.json();

        if (data.status === 'settlement' || data.status === 'capture') {
            // Sukses — hapus baris dari tabel
            removeRow(billId);
            showSuccessBanner();
        } else {
            // Status masih pending — reload saja
            location.reload();
        }
    } catch(e) {
        // Fallback jika check gagal — reload
        location.reload();
    }
}

// ── Hapus baris dengan animasi ────────────────────────────────────────────────
function removeRow(billId) {
    const row = document.getElementById('row-' + billId);
    if (!row) return;

    row.classList.add('removing');

    setTimeout(() => {
        row.remove();
        pendingCount--;

        // Update counter
        const countEl = document.getElementById('tagihan-count');
        const footerEl = document.getElementById('footer-count');
        if (countEl) countEl.textContent = pendingCount + ' tagihan perlu dibayar';
        if (footerEl) footerEl.textContent = pendingCount + ' tagihan belum lunas';

        // Jika semua baris sudah hilang → reload untuk tampil empty state
        const tbody = document.getElementById('tagihan-body');
        if (tbody && tbody.querySelectorAll('tr').length === 0) {
            setTimeout(() => location.reload(), 1000);
        }
    }, 500);
}

// ── Tampilkan banner sukses ───────────────────────────────────────────────────
function showSuccessBanner() {
    const banner = document.getElementById('pay-banner');
    if (banner) banner.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Batalkan transaksi pending ────────────────────────────────────────────────
async function cancelPayment(billId) {
    if (!confirm('Batalkan transaksi ini?')) return;

    const btn  = document.getElementById('cancel-btn-' + billId);
    if (btn) { btn.disabled = true; btn.textContent = 'Membatalkan...'; }

    try {
        const res  = await fetch(`${BASE}/${billId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();

        if (res.ok) {
            alert(data.message || 'Transaksi dibatalkan.');
            location.reload();
        } else {
            alert(data.message || 'Gagal membatalkan.');
            if (btn) { btn.disabled = false; btn.textContent = 'Batal'; }
        }
    } catch(e) {
        alert('Kesalahan jaringan.');
        if (btn) { btn.disabled = false; btn.textContent = 'Batal'; }
    }
}

// ── Helper reset tombol ───────────────────────────────────────────────────────
function resetBtn(billId, text) {
    const btn = document.getElementById('pay-btn-' + billId);
    if (btn) {
        btn.disabled = false;
        btn.textContent = text;
        btn.style.opacity = '1';
    }
}
</script>
</body>
</html>