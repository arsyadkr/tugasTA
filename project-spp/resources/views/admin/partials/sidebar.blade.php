{{-- resources/views/admin/partials/sidebar.blade.php --}}
<style>
    :root { --sidebar-w: 220px; --blue: #4F6EF7; }
    .sidebar { width: var(--sidebar-w); background: #fff; border-right: 1px solid #EDEEF2; height: 100vh; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; z-index: 40; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 500; color: #6B7280; text-decoration: none; transition: all .15s; }
    .nav-item:hover { background: #F3F4F6; color: #111827; }
    .nav-item.active { background: #EEF2FF; color: var(--blue); font-weight: 600; }
    .nav-item svg { width: 16px; height: 16px; opacity: .65; flex-shrink: 0; }
    .nav-item.active svg { opacity: 1; }
    .nav-section { font-size: 10px; font-weight: 600; color: #9CA3AF; text-transform: uppercase; letter-spacing: .05em; padding: 4px 16px; margin-top: 12px; margin-bottom: 4px; }
</style>

<aside class="sidebar">
    {{-- Logo --}}
    <div style="padding:16px 20px; border-bottom:1px solid #EDEEF2; display:flex; align-items:center; gap:10px;">
        <div style="width:30px;height:30px;border-radius:8px;background:var(--blue);display:flex;align-items:center;justify-content:center;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <span style="font-weight:700;font-size:14px;color:#1F2937">SPP<span style="color:var(--blue)">Hub</span></span>
    </div>

    {{-- Navigation --}}
    <nav style="flex:1; padding:12px; overflow-y:auto; display:flex; flex-direction:column; gap:2px;">

        <p class="nav-section">Menu</p>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('admin.students.index') }}"
           class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Data Siswa
        </a>

        <a href="{{ route('admin.tagihan.index') }}"
           class="nav-item {{ request()->routeIs('admin.tagihan*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Tagihan SPP
        </a>

        <a href="{{ route('admin.pembayaran.index') }}"
           class="nav-item {{ request()->routeIs('admin.pembayaran*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Pembayaran
        </a>

        <a href="{{ route('admin.laporan.index') }}"
           class="nav-item {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Laporan
        </a>

        <p class="nav-section">Kegiatan</p>

        <a href="{{ route('admin.special-payments.index', ['type' => 'kunjungan_industri']) }}"
           class="nav-item {{ request()->routeIs('admin.special-payments*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Pembayaran Khusus
        </a>

        <a href="{{ route('admin.reminders.index') }}"
           class="nav-item {{ request()->routeIs('admin.reminders*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            Reminder Tagihan
        </a>

    </nav>

    {{-- User info --}}
    <div style="padding:12px 16px; border-top:1px solid #EDEEF2;">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:34px;height:34px;border-radius:50%;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div style="flex:1; min-width:0;">
                <p style="font-size:12px;font-weight:600;color:#1F2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth()->user()->name }}
                </p>
                <p style="font-size:10px;color:#9CA3AF;">Administrator</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button title="Keluar" style="color:#9CA3AF;background:none;border:none;cursor:pointer;padding:4px;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>