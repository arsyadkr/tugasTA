<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — SPP SMK Muhammadiyah 2 Tangerang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            min-height: 100vh;
            background-image: url('{{ asset("images/bg-sekolah.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        /* Overlay gelap tipis supaya teks lebih terbaca */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(
                to right,
                rgba(0,0,0,0.55) 0%,
                rgba(0,0,0,0.25) 50%,
                rgba(0,0,0,0.5) 100%
            );
            z-index: 0;
        }

        .content-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 40px 5%;
            gap: 40px;
        }

        /* ── Sisi kiri: info sekolah ── */
        .left-side {
            flex: 1;
            max-width: 520px;
            color: #fff;
        }

        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 12px;
            font-weight: 600;
            color: #FFF176;
            letter-spacing: .5px;
            margin-bottom: 28px;
        }

        .school-badge span {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #FDD835;
            flex-shrink: 0;
        }

        .left-side h1 {
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 16px;
            color: #fff;
        }

        .left-side h1 em {
            font-style: normal;
            color: #FDD835;
        }

        .left-side p {
            font-size: 15px;
            line-height: 1.75;
            color: rgba(255,255,255,0.82);
            margin-bottom: 32px;
            max-width: 420px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            color: rgba(255,255,255,0.9);
        }

        .feature-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(253,216,53,0.2);
            border: 1px solid rgba(253,216,53,0.35);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* ── Sisi kanan: box login ── */
        .login-box {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 248, 200, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(253,216,53,0.4);
            box-shadow: 0 25px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
            padding: 40px 36px;
            flex-shrink: 0;
        }

        .login-box .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .logo-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #F9A825, #F57F17);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(249,168,37,0.4);
        }

        .login-box h2 {
            font-size: 20px;
            font-weight: 800;
            color: #4A3000;
        }

        .login-box .subtitle {
            font-size: 12px;
            color: #8B6914;
            margin-top: 2px;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #F9A825, #F57F17);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
            margin-bottom: 24px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #5C3D00;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #E8C84A;
            border-radius: 12px;
            font-size: 13.5px;
            background: rgba(255,255,255,0.8);
            color: #3A2A00;
            outline: none;
            transition: all .2s;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #F9A825;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(249,168,37,0.2);
        }

        .error-text { font-size: 11.5px; color: #c0392b; margin-top: 4px; }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #F9A825, #F57F17);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all .2s;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(249,168,37,0.4);
            letter-spacing: .3px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #FFB300, #E65100);
            box-shadow: 0 6px 20px rgba(249,168,37,0.5);
            transform: translateY(-1px);
        }

        .btn-login:disabled {
            opacity: .7;
            transform: none;
            cursor: not-allowed;
        }

        .switch-link {
            text-align: center;
            margin-top: 20px;
            font-size: 12.5px;
            color: #7A5C00;
        }

        .switch-link a {
            color: #E65100;
            font-weight: 700;
            text-decoration: none;
        }

        .switch-link a:hover { text-decoration: underline; }

        .flash-success {
            background: #E8F5E9;
            border: 1px solid #A5D6A7;
            color: #2E7D32;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12.5px;
            margin-bottom: 16px;
        }

        .flash-error {
            background: #FFEBEE;
            border: 1px solid #FFCDD2;
            color: #C62828;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12.5px;
            margin-bottom: 16px;
        }

        .divider {
            height: 1px;
            background: rgba(200,160,0,0.2);
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .content-wrap { flex-direction: column; justify-content: center; padding: 24px 5%; }
            .left-side { max-width: 100%; text-align: center; }
            .left-side p { margin: 0 auto 24px; }
            .feature-list { display: none; }
            .login-box { max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="content-wrap">

    <!-- ── SISI KIRI ── -->
    <div class="left-side">

        <div class="school-badge">
            <span></span>
            SMK Muhammadiyah 2 Tangerang
        </div>

        <h1>
            Sistem Pembayaran<br>
            <em>SPP Digital</em><br>
            Terpercaya
        </h1>

        <p>
            Platform pembayaran SPP modern untuk SMK Muhammadiyah 2 Tangerang.
            Kelola tagihan, pantau pembayaran, dan cetak laporan dengan mudah,
            cepat, dan aman secara digital.
        </p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#FDD835" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                Pembayaran online via QRIS, Transfer Bank & E-Wallet
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#FDD835" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                Riwayat tagihan dan bukti pembayaran digital
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#FDD835" stroke-width="2">
                        <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
                Cetak kartu ujian UTS & UAS otomatis
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#FDD835" stroke-width="2">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                Laporan keuangan lengkap & ekspor Excel
            </div>
        </div>

    </div>

    <!-- ── SISI KANAN: BOX LOGIN ADMIN ── -->
    <div class="login-box">

        <div class="logo-wrap">
            <div class="logo-icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <div class="login-box" style="padding:0;background:none;box-shadow:none;border:none;">
                    <h2 style="margin:0">SPP PINTAR</h2>
                    <p class="subtitle">SMK Muhammadiyah 2 Tangerang</p>
                </div>
            </div>
        </div>

        <div class="role-badge">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Login Admin
        </div>

        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login.admin.post') }}" novalidate>
            @csrf

            <div style="margin-bottom:16px">
                <label for="login">Email Admin</label>
                <input type="email" id="login" name="login"
                       value="{{ old('login') }}"
                       placeholder="admin@smkmuh2tangerang.sch.id"
                       autocomplete="email" autofocus>
                @error('login')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:4px">
                <label for="password">Password</label>
                <div style="position:relative">
                    <input type="password" id="password" name="password"
                           placeholder="Masukkan password"
                           autocomplete="current-password"
                           style="padding-right:50px">
                    <button type="button" onclick="togglePwd()" tabindex="-1"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:11px;color:#8B6914;font-weight:600;">
                        <span id="pwd-label">Lihat</span>
                    </button>
                </div>
                @error('password')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" id="btn-submit" class="btn-login">
                Masuk sebagai Admin
            </button>

        </form>

        <div class="divider"></div>

        <div class="switch-link">
            Bukan admin?
            <a href="{{ route('login.siswa') }}">Login sebagai Siswa</a>
        </div>

    </div>

</div>

<script>
function togglePwd() {
    const i = document.getElementById('password');
    const l = document.getElementById('pwd-label');
    i.type = i.type === 'password' ? 'text' : 'password';
    l.textContent = i.type === 'password' ? 'Lihat' : 'Sembunyikan';
}
document.querySelector('form').addEventListener('submit', function() {
    const b = document.getElementById('btn-submit');
    b.disabled = true;
    b.textContent = 'Memproses...';
});
</script>

</body>
</html>