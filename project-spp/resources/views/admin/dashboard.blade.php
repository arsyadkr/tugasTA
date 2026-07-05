<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Sistem SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root { --sidebar-w: 220px; --blue: #4F6EF7; }
        body { background: #F5F6FA; }

        .sidebar {
            width: var(--sidebar-w);
            background: #fff;
            border-right: 1px solid #EDEEF2;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 40;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 16px; border-radius: 10px;
            font-size: 13.5px; font-weight: 500; color: #6B7280;
            cursor: pointer; transition: all .15s; text-decoration: none;
        }
        .nav-item:hover { background: #F3F4F6; color: #111827; }
        .nav-item.active { background: #EEF2FF; color: var(--blue); font-weight: 600; }
        .nav-item svg { width: 17px; height: 17px; opacity: .65; flex-shrink: 0; }
        .nav-item.active svg { opacity: 1; }

        .stat-card {
            border-radius: 16px; padding: 20px; color: #fff;
            position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute;
            top: -20px; right: -20px;
            width: 100px; height: 100px;
            background: rgba(255,255,255,.1); border-radius: 50%;
        }
        .card { background: #fff; border-radius: 16px; border: 1px solid #EDEEF2; }
        .progress-bar { height: 6px; border-radius: 99px; background: #F3F4F6; }
        .progress-fill { height: 100%; border-radius: 99px; transition: width .6s ease; }
        .tbl-row:hover { background: #F9FAFB; }
        .avatar {
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .donut { transform: rotate(-90deg); }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="px-5 py-5 flex items-center gap-2.5 border-b border-gray-100">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:var(--blue)">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <span class="font-bold text-gray-800 text-[15px]">SPP<span style="color:var(--blue)">Hub</span></span>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest px-3 mb-2">Menu</p>

        <a href="{{ route('admin.dashboard') }}" class="nav-item active">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>

        <a href="{{ route('admin.students.index') }}" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            Data Siswa
        </a>

        <a href="{{ route('admin.tagihan.index') }}" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Tagihan SPP
        </a>

        <a href="{{ route('admin.pembayaran.index') }}" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Pembayaran
        </a>

        <a href="{{ route('admin.laporan.index') }}" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Laporan
        </a>

        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest px-3 mt-5 mb-2">Lainnya</p>
        <a href="#" class="nav-item" style="opacity:.45;pointer-events:none">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
            Pengaturan
        </a>
    </nav>

    <div class="px-4 py-4 border-t border-gray-100">
        <div class="flex items-center gap-3">
            <div class="avatar w-9 h-9" style="background:var(--blue)">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-gray-400">Administrator</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button title="Keluar" class="text-gray-400 hover:text-red-500 transition">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- MAIN -->
<main style="margin-left: var(--sidebar-w); min-height: 100vh;">

    <!-- Topbar -->
    <div class="bg-white border-b border-gray-100 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30">
        <div>
            <h1 class="text-[15px] font-bold text-gray-800">Dashboard</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('admin.students.create') }}"
           class="flex items-center gap-1.5 text-xs font-semibold text-white px-4 py-2 rounded-lg"
           style="background:var(--blue)">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Siswa
        </a>
    </div>

    <div class="p-6 space-y-5">

        <!-- STAT CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card" style="background:linear-gradient(135deg,#4F6EF7,#818cf8)">
                <p class="text-[11px] text-blue-100 mb-1">Total Siswa</p>
                <p class="text-3xl font-extrabold">{{ number_format($totalSiswa) }}</p>
                <p class="text-[11px] text-blue-200 mt-1">siswa terdaftar</p>
                <svg class="absolute bottom-3 right-3 opacity-20" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#f97316)">
                <p class="text-[11px] text-amber-100 mb-1">Total Tagihan</p>
                <p class="text-3xl font-extrabold">{{ number_format($totalTagihan) }}</p>
                <p class="text-[11px] text-amber-200 mt-1">tagihan digenerate</p>
                <svg class="absolute bottom-3 right-3 opacity-20" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="stat-card" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
                <p class="text-[11px] text-green-100 mb-1">Sudah Lunas</p>
                <p class="text-3xl font-extrabold">{{ number_format($tagihanLunas) }}</p>
                <p class="text-[11px] text-green-200 mt-1">tagihan terbayar</p>
                <svg class="absolute bottom-3 right-3 opacity-20" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-card" style="background:linear-gradient(135deg,#f43f5e,#e11d48)">
                <p class="text-[11px] text-rose-100 mb-1">Belum Lunas</p>
                <p class="text-3xl font-extrabold">{{ number_format($tagihanBelumLunas) }}</p>
                <p class="text-[11px] text-rose-200 mt-1">perlu perhatian</p>
                <svg class="absolute bottom-3 right-3 opacity-20" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <!-- ROW 2: Donut + Tabel Siswa -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Donut -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-gray-800">Status Tagihan</h2>
                    <span class="text-xs text-gray-400">{{ now()->year }}</span>
                </div>
                @php
                    $total     = $totalTagihan ?: 1;
                    $paidPct   = round($tagihanLunas / $total * 100);
                    $r = 52; $circ = 2 * M_PI * $r;
                    $paidDash   = ($paidPct / 100) * $circ;
                    $unpaidPct  = 100 - $paidPct;
                    $unpaidDash = ($unpaidPct / 100) * $circ;
                @endphp
                <div class="flex justify-center my-3">
                    <div class="relative">
                        <svg width="140" height="140" viewBox="0 0 140 140" class="donut">
                            <circle cx="70" cy="70" r="{{ $r }}" fill="none" stroke="#F3F4F6" stroke-width="14"/>
                            <circle cx="70" cy="70" r="{{ $r }}" fill="none" stroke="#22c55e" stroke-width="14"
                                stroke-dasharray="{{ $paidDash }} {{ $circ - $paidDash }}" stroke-linecap="round"/>
                            <circle cx="70" cy="70" r="{{ $r }}" fill="none" stroke="#fbbf24" stroke-width="14"
                                stroke-dasharray="{{ $unpaidDash }} {{ $circ - $unpaidDash }}"
                                stroke-dashoffset="{{ -$paidDash }}" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-extrabold text-gray-800">{{ $paidPct }}%</span>
                            <span class="text-[10px] text-gray-400">Lunas</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>Lunas</span>
                        <span class="font-semibold">{{ number_format($tagihanLunas) }} tagihan</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>Belum Lunas</span>
                        <span class="font-semibold">{{ number_format($tagihanBelumLunas) }} tagihan</span>
                    </div>
                    <div class="progress-bar mt-3">
                        <div class="progress-fill bg-green-500" style="width:{{ $paidPct }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Tabel Siswa Terbaru -->
            <div class="card lg:col-span-2 overflow-hidden">
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Siswa Terbaru</h2>
                    <a href="{{ route('admin.students.index') }}" class="text-xs font-semibold" style="color:var(--blue)">Lihat Semua</a>
                </div>
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Siswa</th>
                            <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Kelas</th>
                            <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Tagihan</th>
                            <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $avatarColors = ['#4F6EF7','#22c55e','#f59e0b','#a855f7','#f43f5e']; @endphp
                        @foreach ($siswaTerbaru as $siswa)
                        <tr class="tbl-row border-b border-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="avatar w-8 h-8"
                                         style="background:{{ $avatarColors[$loop->index % 5] }}">
                                        {{ strtoupper(substr($siswa->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-800">{{ $siswa->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $siswa->nis }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $siswa->schoolClass->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-semibold text-gray-700">{{ $siswa->bills_count }}</span>
                                <span class="text-gray-400"> tagihan</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($siswa->user?->must_change_password)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-700">Baru</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <!-- ROW 3: Rekap Kelas + Pembayaran Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            <!-- Rekap per kelas -->
            <div class="card p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-4">Rekap Per Kelas</h2>
                <div class="space-y-4">
                    @php $barColors = ['#4F6EF7','#22c55e','#f59e0b','#a855f7','#f43f5e']; @endphp
                    @foreach ($rekapKelas as $kelas)
                    @php
                        $maxSiswa = $rekapKelas->max('students_count') ?: 1;
                        $pct = round($kelas->students_count / $maxSiswa * 100);
                        $col = $barColors[$loop->index % 5];
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-semibold text-gray-700">{{ $kelas->name }}</span>
                            <span class="text-gray-400">{{ $kelas->students_count }} siswa</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:{{ $pct }}%; background:{{ $col }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pembayaran terbaru -->
            <div class="card overflow-hidden">
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Pembayaran Terbaru</h2>
                    <span class="text-xs text-gray-400">{{ now()->translatedFormat('M Y') }}</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($pembayaranTerbaru as $payment)
                    <div class="flex items-center gap-3 px-5 py-3.5">
                        <div class="avatar w-9 h-9" style="background:#22c55e">
                            {{ strtoupper(substr($payment->student->name ?? 'NA', 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 truncate">{{ $payment->student->name ?? '-' }}</p>
                            <p class="text-[10px] text-gray-400">
                                {{ $payment->bill->month_label ?? '' }} {{ $payment->bill->year ?? '' }}
                                &middot; {{ $payment->payment_type_label }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-green-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-gray-400">{{ $payment->paid_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-10 text-center text-xs text-gray-400">Belum ada pembayaran.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- ROW 4: Bar Chart Pemasukan Bulanan -->
        <div class="card p-5">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold text-gray-800">Pemasukan Bulanan</h2>
                <span class="text-xs text-gray-400">{{ now()->year }}</span>
            </div>
            <div class="flex items-end gap-2 h-28">
                @php
                    $maxVal = $pemasukanBulanan->max('total') ?: 1;
                    $namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                @endphp
                @for ($m = 1; $m <= 12; $m++)
                    @php
                        $item   = $pemasukanBulanan->firstWhere('month', $m);
                        $val    = $item?->total ?? 0;
                        $h      = max(4, round(($val / $maxVal) * 100));
                        $isNow  = $m === now()->month;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <p class="text-[9px] text-gray-400">{{ $val > 0 ? round($val/1000).'k' : '' }}</p>
                        <div class="w-full rounded-t-md" style="height:{{ $h }}%; min-height:4px; background:{{ $isNow ? '#4F6EF7' : '#E0E7FF' }}"></div>
                        <p class="text-[9px] {{ $isNow ? 'font-bold text-blue-600' : 'text-gray-400' }}">{{ $namaBulan[$m-1] }}</p>
                    </div>
                @endfor
            </div>
            <div class="mt-4 flex flex-wrap gap-6 text-xs border-t border-gray-100 pt-4">
                <div>
                    <p class="text-[10px] text-gray-400">Total {{ now()->year }}</p>
                    <p class="font-bold text-gray-800 text-base">Rp {{ number_format($pemasukanBulanan->sum('total'), 0, ',', '.') }}</p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div>
                    <p class="text-[10px] text-gray-400">Bulan Ini</p>
                    <p class="font-bold text-green-600 text-base">Rp {{ number_format($pemasukanBulanan->firstWhere('month', now()->month)?->total ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div>
                    <p class="text-[10px] text-gray-400">Rata-rata/Bulan</p>
                    @php $avg = $pemasukanBulanan->where('total', '>', 0)->avg('total') ?? 0 @endphp
                    <p class="font-bold text-gray-700 text-base">Rp {{ number_format($avg, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

    </div>
</main>

</body>
</html>