<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — {{ $student->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root { --sidebar-w: 210px; --right-w: 260px; --blue: #4F6EF7; --green: #22c55e; --amber: #f59e0b; --rose: #f43f5e; }
        body { background: #F5F6FA; }
        .sidebar { width: var(--sidebar-w); background: #fff; border-right: 1px solid #EDEEF2; height: 100vh; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; z-index: 40; }
        .nav-item { display: flex; align-items: center; gap: 9px; padding: 8px 14px; border-radius: 10px; font-size: 13px; font-weight: 500; color: #6B7280; text-decoration: none; transition: all .15s; }
        .nav-item:hover { background: #F3F4F6; color: #111827; }
        .nav-item.active { background: #F0FDF4; color: #16a34a; font-weight: 600; }
        .nav-item svg { width: 16px; height: 16px; opacity: .65; flex-shrink: 0; }
        .nav-item.active svg { opacity: 1; }
        .right-panel { width: var(--right-w); background: #fff; border-left: 1px solid #EDEEF2; height: 100vh; position: fixed; top: 0; right: 0; overflow-y: auto; z-index: 40; }
        .stat-card { border-radius: 14px; padding: 16px; color: #fff; position: relative; overflow: hidden; }
        .stat-card::after { content: ''; position: absolute; top: -16px; right: -16px; width: 80px; height: 80px; background: rgba(255,255,255,.12); border-radius: 50%; }
        .card { background: #fff; border-radius: 16px; border: 1px solid #EDEEF2; }
        .progress-track { height: 8px; border-radius: 99px; background: #F3F4F6; }
        .progress-fill { height: 100%; border-radius: 99px; transition: width .7s ease; }
        .bill-row { transition: background .1s; }
        .bill-row:hover { background: #F9FAFB; }
        .avatar { border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; flex-shrink: 0; }
        .donut { transform: rotate(-90deg); }
        .cal-day { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: default; }
        .cal-day.today { background: var(--blue); color: #fff; font-weight: 700; }
        .cal-day.paid  { background: #DCFCE7; color: #16a34a; font-weight: 600; }
        ::-webkit-scrollbar { width: 3px; }
        ::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 99px; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="px-5 py-4 flex items-center gap-2.5 border-b border-gray-100">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--blue)">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <span class="font-bold text-gray-800 text-sm">SPP<span style="color:var(--blue)">Hub</span></span>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-0.5">
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest px-3 mb-2">Menu</p>

        <a href="{{ route('siswa.dashboard') }}" class="nav-item active">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>

        <a href="{{ route('siswa.tagihan.index') }}" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Tagihan SPP
        </a>

        <a href="{{ route('siswa.riwayat.index') }}" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Riwayat Bayar
        </a>

        <a href="{{ route('siswa.kartu.index') }}" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Kartu Ujian
        </a>

        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest px-3 mt-4 mb-2">Akun</p>
        <a href="{{ route('password.change.show') }}" class="nav-item">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Ganti Password
        </a>
    </nav>

    <div class="px-4 py-4 border-t border-gray-100">
        <div class="flex items-center gap-2.5">
            <div class="avatar w-9 h-9 text-xs" style="background:var(--blue)">
                {{ strtoupper(substr($student->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">{{ $student->name }}</p>
                <p class="text-[10px] text-gray-400">Siswa</p>
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

<!-- RIGHT PANEL -->
<div class="right-panel px-4 py-5 space-y-5">

    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-bold text-gray-700">{{ now()->translatedFormat('F Y') }}</h3>
        </div>
        @php
            $daysInMonth = now()->daysInMonth;
            $firstDay    = now()->startOfMonth()->dayOfWeek;
            $paidDays    = $bills->where('status','paid')
                                 ->where('month', now()->month)
                                 ->where('year', now()->year)
                                 ->pluck('due_date')
                                 ->map(fn($d) => $d->day)
                                 ->toArray();
        @endphp
        <div class="grid grid-cols-7 gap-0.5">
            @foreach(['M','S','S','R','K','J','S'] as $d)
                <div class="cal-day text-[10px] text-gray-400 font-semibold">{{ $d }}</div>
            @endforeach
            @for($i = 1; $i < $firstDay; $i++)<div></div>@endfor
            @for($d = 1; $d <= $daysInMonth; $d++)
                <div class="cal-day text-[11px] {{ $d == now()->day ? 'today' : (in_array($d, $paidDays) ? 'paid' : 'text-gray-600') }}">{{ $d }}</div>
            @endfor
        </div>
        <div class="flex gap-3 mt-2 text-[10px] text-gray-400">
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Hari ini</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-200"></span>Sudah bayar</span>
        </div>
    </div>

    <div class="border-t border-gray-100"></div>

    <div>
        <h3 class="text-xs font-bold text-gray-700 mb-3">Tagihan Mendatang</h3>
        <div class="space-y-2.5">
            @forelse ($tagihanBelumLunas->take(4) as $bill)
            <div class="flex items-center gap-2.5 p-2.5 rounded-xl border border-gray-100 hover:border-blue-100 transition">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $bill->status === 'overdue' ? 'bg-red-100' : 'bg-blue-50' }}">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="{{ $bill->status === 'overdue' ? '#ef4444' : '#4F6EF7' }}" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-gray-700">{{ $bill->period_label }}</p>
                    <p class="text-[10px] {{ $bill->status === 'overdue' ? 'text-red-500' : 'text-gray-400' }}">Jatuh tempo {{ $bill->due_date->format('d M') }}</p>
                </div>
                <p class="text-[11px] font-bold text-gray-800">Rp {{ number_format($bill->amount/1000, 0) }}k</p>
            </div>
            @empty
            <div class="text-center py-4">
                <p class="text-xs text-green-600 font-semibold">✓ Semua tagihan lunas!</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="border-t border-gray-100"></div>

    <div>
        <h3 class="text-xs font-bold text-gray-700 mb-3">Riwayat Pembayaran</h3>
        <div class="space-y-2.5">
            @forelse ($riwayatBayar as $payment)
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-gray-700">{{ $payment->bill->period_label ?? '-' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $payment->paid_at?->format('d M Y') }}</p>
                </div>
                <p class="text-[11px] font-bold text-green-600">Rp {{ number_format($payment->amount/1000, 0) }}k</p>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-2">Belum ada pembayaran.</p>
            @endforelse
        </div>
    </div>

</div>

<!-- MAIN -->
<main style="margin-left:var(--sidebar-w); margin-right:var(--right-w); min-height:100vh;">

    <div class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between sticky top-0 z-30">
        <div>
            <h1 class="text-sm font-bold text-gray-800">Selamat datang, {{ explode(' ', $student->name)[0] }} 👋</h1>
            <p class="text-[11px] text-gray-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="avatar w-8 h-8 text-xs" style="background:var(--blue)">
                {{ strtoupper(substr($student->name, 0, 2)) }}
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-xs font-semibold text-gray-700">{{ $student->name }}</p>
                <p class="text-[10px] text-gray-400">{{ $student->schoolClass->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="p-5 space-y-5">

        @if (session('success'))
            <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="p-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl text-sm">{{ session('warning') }}</div>
        @endif

        @if ($tagihanOverdue->isNotEmpty())
        <div class="p-3.5 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-red-700">{{ $tagihanOverdue->count() }} tagihan telah jatuh tempo!</p>
                <p class="text-[11px] text-red-500">Segera lakukan pembayaran untuk menghindari sanksi administrasi.</p>
            </div>
        </div>
        @endif

        @if ($bills->isNotEmpty())
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#4F6EF7,#818cf8)">
                <p class="text-[10px] text-blue-100 mb-1">Total Tagihan</p>
                <p class="text-2xl font-extrabold">{{ $bills->count() }}</p>
                <p class="text-[10px] text-blue-200 mt-0.5">bulan</p>
                <svg class="absolute bottom-2 right-2 opacity-20" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="stat-card" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
                <p class="text-[10px] text-green-100 mb-1">Sudah Lunas</p>
                <p class="text-2xl font-extrabold">{{ $tagihanLunas->count() }}</p>
                <p class="text-[10px] text-green-200 mt-0.5">bulan</p>
                <svg class="absolute bottom-2 right-2 opacity-20" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#f97316)">
                <p class="text-[10px] text-amber-100 mb-1">Belum Lunas</p>
                <p class="text-2xl font-extrabold">{{ $tagihanBelumLunas->count() }}</p>
                <p class="text-[10px] text-amber-200 mt-0.5">bulan</p>
                <svg class="absolute bottom-2 right-2 opacity-20" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-card" style="background:linear-gradient(135deg,#a855f7,#7c3aed)">
                <p class="text-[10px] text-purple-100 mb-1">Total Dibayar</p>
                <p class="text-lg font-extrabold leading-tight">Rp {{ number_format($totalDibayar/1000, 0) }}k</p>
                <p class="text-[10px] text-purple-200 mt-0.5">dari Rp {{ number_format($totalTagihanNominal/1000, 0) }}k</p>
                <svg class="absolute bottom-2 right-2 opacity-20" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-gray-800">Progres SPP</h2>
                    <span class="text-xs text-gray-400">{{ now()->year }}</span>
                </div>
                @php
                    $r = 50; $circ = 2 * M_PI * $r;
                    $paidDash = ($persentase / 100) * $circ;
                @endphp
                <div class="flex justify-center my-3">
                    <div class="relative">
                        <svg width="130" height="130" viewBox="0 0 130 130" class="donut">
                            <circle cx="65" cy="65" r="{{ $r }}" fill="none" stroke="#F3F4F6" stroke-width="13"/>
                            <circle cx="65" cy="65" r="{{ $r }}" fill="none"
                                stroke="{{ $persentase >= 100 ? '#22c55e' : ($persentase >= 50 ? '#4F6EF7' : '#f59e0b') }}"
                                stroke-width="13"
                                stroke-dasharray="{{ $paidDash }} {{ $circ - $paidDash }}"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-extrabold text-gray-800">{{ $persentase }}%</span>
                            <span class="text-[10px] text-gray-400">Terbayar</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 mt-1">
                    <div class="flex justify-between text-xs">
                        <span class="flex items-center gap-1.5 text-gray-500"><span class="w-2 h-2 rounded-full bg-green-500"></span>Dibayar</span>
                        <span class="font-semibold text-gray-700">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="flex items-center gap-1.5 text-gray-500"><span class="w-2 h-2 rounded-full bg-gray-200"></span>Sisa</span>
                        <span class="font-semibold text-red-500">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="card p-5 lg:col-span-2">
                <h2 class="text-sm font-bold text-gray-800 mb-4">Informasi Siswa</h2>
                <div class="flex items-start gap-4">
                    <div class="avatar w-14 h-14 text-lg flex-shrink-0" style="background:var(--blue)">
                        {{ strtoupper(substr($student->name, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-gray-800">{{ $student->name }}</h3>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">NIS: {{ $student->nis }}</p>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-3 mt-4">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Kelas</p>
                                <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $student->schoolClass->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Jurusan</p>
                                <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $student->major->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Tahun Ajaran</p>
                                <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $student->schoolClass->academic_year ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Status</p>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $student->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shortcut buttons --}}
                <div class="flex gap-2 mt-5 pt-4 border-t border-gray-100">
                    <a href="{{ route('siswa.tagihan.index') }}"
                       class="flex-1 text-center text-xs font-semibold text-white py-2 rounded-lg transition"
                       style="background:#4F6EF7">
                        Lihat Tagihan
                    </a>
                    <a href="{{ route('siswa.riwayat.index') }}"
                       class="flex-1 text-center text-xs font-semibold text-white py-2 rounded-lg transition bg-green-600 hover:bg-green-700">
                        Riwayat Bayar
                    </a>
                    <a href="{{ route('siswa.kartu.index') }}"
                       class="flex-1 text-center text-xs font-semibold text-white py-2 rounded-lg transition bg-purple-600 hover:bg-purple-700">
                        Kartu Ujian
                    </a>
                </div>
            </div>

        </div>

        @if ($bills->isEmpty())
        <div class="card px-6 py-14 text-center">
            <div class="text-4xl mb-3">📋</div>
            <h2 class="text-sm font-bold text-gray-700 mb-1">Belum Ada Tagihan</h2>
            <p class="text-xs text-gray-400">Tagihan SPP akan muncul di sini setelah admin menginputkan data.</p>
        </div>
        @else
        <div class="card overflow-hidden">
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-800">Detail Tagihan SPP</h2>
                <a href="{{ route('siswa.tagihan.index') }}" class="text-xs font-semibold" style="color:#4F6EF7">Bayar Tagihan →</a>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-5 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Periode</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Nominal</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Jatuh Tempo</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Dibayar Via</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                        <th class="text-center px-4 py-2.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($bills as $bill)
                    <tr class="bill-row {{ $bill->status === 'overdue' ? 'bg-red-50/30' : '' }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $bill->status === 'paid' ? 'bg-green-100' : ($bill->status === 'overdue' ? 'bg-red-100' : 'bg-blue-50') }}">
                                    <span class="text-sm">{{ $bill->status === 'paid' ? '✓' : ($bill->status === 'overdue' ? '!' : '·') }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">{{ $bill->month_label }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $bill->year }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-xs font-semibold text-gray-700">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-xs {{ $bill->status === 'overdue' ? 'text-red-500 font-semibold' : 'text-gray-400' }}">{{ $bill->due_date->format('d M Y') }}</td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">{{ $bill->successfulPayment?->payment_type_label ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            @switch($bill->status)
                                @case('paid')<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">✓ Lunas</span>@break
                                @case('pending')<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">⏳ Diproses</span>@break
                                @case('overdue')<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">✕ Jatuh Tempo</span>@break
                                @default<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Belum Bayar</span>
                            @endswitch
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if ($bill->status === 'paid')
                                <a href="{{ route('siswa.riwayat.index') }}" class="text-[11px] text-green-600 font-semibold hover:underline">✓ Lunas</a>
                            @elseif ($bill->status === 'pending')
                                <span class="text-[11px] text-blue-400">Menunggu...</span>
                            @else
                                <a href="{{ route('siswa.tagihan.index') }}"
                                   class="text-[11px] font-semibold text-white px-3 py-1.5 rounded-lg inline-block"
                                   style="background:#4F6EF7">
                                    Bayar
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">{{ $tagihanLunas->count() }} lunas &middot; {{ $tagihanBelumLunas->count() }} belum lunas</span>
                <div class="flex items-center gap-4 text-xs">
                    <span class="text-gray-500">Total: <span class="font-bold text-gray-800">Rp {{ number_format($totalTagihanNominal, 0, ',', '.') }}</span></span>
                    @if ($sisaTagihan > 0)
                    <span class="text-gray-500">Sisa: <span class="font-bold text-red-600">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span></span>
                    @else
                    <span class="text-green-600 font-semibold">🎉 Semua tagihan lunas!</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</main>

</body>
</html>