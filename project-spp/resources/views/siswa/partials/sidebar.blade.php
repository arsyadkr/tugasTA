{{-- resources/views/siswa/partials/sidebar.blade.php --}}
<style>
    :root{--sw-s:210px;--green:#22c55e;}
    .s-sidebar{width:var(--sw-s);background:#fff;border-right:1px solid #EDEEF2;height:100vh;position:fixed;top:0;left:0;display:flex;flex-direction:column;z-index:40;}
    .s-nav{display:flex;align-items:center;gap:9px;padding:8px 14px;border-radius:10px;font-size:13px;font-weight:500;color:#6B7280;text-decoration:none;transition:all .15s;}
    .s-nav:hover{background:#F3F4F6;color:#111827;}
    .s-nav.active{background:#F0FDF4;color:#16a34a;font-weight:600;}
    .s-nav svg{width:16px;height:16px;opacity:.65;flex-shrink:0;}
    .s-nav.active svg{opacity:1;}
</style>
<aside class="s-sidebar">
    <div style="padding:16px 20px;border-bottom:1px solid #EDEEF2;display:flex;align-items:center;gap:10px;">
        <div style="width:28px;height:28px;border-radius:8px;background:#22c55e;display:flex;align-items:center;justify-content:center;">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <span style="font-weight:700;font-size:14px;color:#1F2937">SPP<span style="color:#22c55e">Hub</span></span>
    </div>

    <nav style="flex:1;padding:12px;display:flex;flex-direction:column;gap:2px;">
        <div style="font-size:10px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:.05em;padding:4px 14px;margin-top:8px;margin-bottom:4px;">Menu</div>

        <a href="{{ route('siswa.dashboard') }}" class="s-nav {{ request()->routeIs('siswa.dashboard')?'active':'' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>

        <a href="{{ route('siswa.tagihan.index') }}" class="s-nav {{ request()->routeIs('siswa.tagihan*')?'active':'' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Tagihan SPP
        </a>

        <a href="{{ route('siswa.riwayat.index') }}" class="s-nav {{ request()->routeIs('siswa.riwayat*')?'active':'' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Riwayat Pembayaran
        </a>

        <a href="{{ route('siswa.special-payments.index') }}" class="s-nav {{ request()->routeIs('siswa.special-payments*')?'active':'' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Pembayaran Khusus
        </a>

        <div style="font-size:10px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:.05em;padding:4px 14px;margin-top:12px;margin-bottom:4px;">Akun</div>

        <a href="{{ route('password.change.show') }}" class="s-nav {{ request()->routeIs('password.change*')?'active':'' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Ganti Password
        </a>
    </nav>

    <div style="padding:12px 16px;border-top:1px solid #EDEEF2;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:34px;height:34px;border-radius:50%;background:#22c55e;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name,0,2)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:12px;font-weight:600;color:#1F2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
                <p style="font-size:10px;color:#9CA3AF;">Siswa</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button title="Keluar" style="color:#9CA3AF;background:none;border:none;cursor:pointer;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>