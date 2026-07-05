{{-- resources/views/siswa/partials/topbar.blade.php --}}
<div style="background:#fff;border-bottom:1px solid #EDEEF2;padding:12px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:30;">
    <h1 style="font-size:15px;font-weight:700;color:#1F2937;">{{ $title ?? 'Dashboard' }}</h1>
    <div style="font-size:12px;color:#6B7280;">{{ auth()->user()->name }}</div>
</div>