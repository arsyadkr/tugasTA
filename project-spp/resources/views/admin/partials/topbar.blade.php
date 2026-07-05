{{-- resources/views/admin/partials/topbar.blade.php --}}
{{-- Gunakan: @include('admin.partials.topbar', ['title' => 'Judul Halaman']) --}}

<div style="background:#fff;border-bottom:1px solid #EDEEF2;padding:12px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:30;">
    <h1 style="font-size:15px;font-weight:700;color:#1F2937;">{{ $title ?? 'Dashboard' }}</h1>

    {{-- Notifikasi Bell --}}
    <div style="position:relative;" id="notif-wrap">
        <button onclick="toggleNotif()" style="position:relative;background:none;border:none;cursor:pointer;padding:6px;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#6B7280" stroke-width="2">
                <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            {{-- Badge unread count --}}
            <span id="notif-badge"
                  style="display:none;position:absolute;top:2px;right:2px;background:#f43f5e;color:#fff;font-size:9px;font-weight:700;border-radius:99px;width:16px;height:16px;display:flex;align-items:center;justify-content:center;line-height:1;">
                0
            </span>
        </button>

        {{-- Dropdown Notifikasi --}}
        <div id="notif-panel"
             style="display:none;position:absolute;right:0;top:44px;width:320px;background:#fff;border:1px solid #EDEEF2;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:100;overflow:hidden;">
            <div style="padding:12px 16px;border-bottom:1px solid #EDEEF2;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;font-weight:700;color:#1F2937;">Notifikasi</span>
                <button onclick="markAllRead()" style="font-size:11px;color:#4F6EF7;background:none;border:none;cursor:pointer;font-weight:600;">
                    Tandai semua dibaca
                </button>
            </div>
            <div id="notif-list" style="max-height:360px;overflow-y:auto;">
                <div style="padding:24px;text-align:center;font-size:12px;color:#9CA3AF;">Memuat...</div>
            </div>
        </div>
    </div>
</div>

<script>
let notifOpen = false;

function toggleNotif() {
    notifOpen = !notifOpen;
    document.getElementById('notif-panel').style.display = notifOpen ? 'block' : 'none';
    if (notifOpen) loadNotifications();
}

// Tutup dropdown jika klik di luar
document.addEventListener('click', function(e) {
    if (!document.getElementById('notif-wrap').contains(e.target)) {
        notifOpen = false;
        document.getElementById('notif-panel').style.display = 'none';
    }
});

async function loadNotifications() {
    try {
        const res  = await fetch('{{ route("admin.notifications.index") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await res.json();

        // Update badge
        const badge = document.getElementById('notif-badge');
        if (data.unread_count > 0) {
            badge.style.display = 'flex';
            badge.textContent   = data.unread_count > 9 ? '9+' : data.unread_count;
        } else {
            badge.style.display = 'none';
        }

        // Render list
        const list = document.getElementById('notif-list');
        if (data.notifications.length === 0) {
            list.innerHTML = '<div style="padding:24px;text-align:center;font-size:12px;color:#9CA3AF;">Tidak ada notifikasi.</div>';
            return;
        }

        list.innerHTML = data.notifications.map(n => `
            <div onclick="markRead('${n.id}', this)"
                 style="padding:12px 16px;border-bottom:1px solid #F9FAFB;cursor:pointer;background:${n.read_at ? '#fff' : '#EFF6FF'};transition:background .15s;">
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:50%;background:#DCFCE7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:12px;font-weight:${n.read_at ? '400' : '600'};color:#1F2937;line-height:1.4;">${n.data.message || '-'}</p>
                        <p style="font-size:10px;color:#9CA3AF;margin-top:2px;">${n.created_at}</p>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (e) {
        document.getElementById('notif-list').innerHTML = '<div style="padding:16px;text-align:center;font-size:12px;color:#EF4444;">Gagal memuat.</div>';
    }
}

async function markRead(id, el) {
    el.style.background = '#fff';
    await fetch(`{{ url('admin/notifications') }}/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    });
    loadNotifications();
}

async function markAllRead() {
    await fetch('{{ route("admin.notifications.read-all") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    });
    loadNotifications();
}

// Auto-poll unread count setiap 30 detik
async function pollBadge() {
    try {
        const res  = await fetch('{{ route("admin.notifications.index") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await res.json();
        const badge = document.getElementById('notif-badge');
        if (data.unread_count > 0) {
            badge.style.display = 'flex';
            badge.textContent   = data.unread_count > 9 ? '9+' : data.unread_count;
        } else {
            badge.style.display = 'none';
        }
    } catch {}
}

pollBadge();
setInterval(pollBadge, 30000);
</script>