<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa — SPP SMK Muhammadiyah 2 Tangerang</title>
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

        .feature-list { display: flex; flex-direction: column; gap: 12px; }

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

        /* Box siswa — warna sedikit lebih hangat/hijau-kuning */
        .login-box {
            width: 100%;
            max-width: 420px;
            background: rgba(240, 255, 220, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(139,195,74,0.4);
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
            background: linear-gradient(135deg, #8BC34A, #558B2F);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(139,195,74,0.4);
        }

        .login-box h2 {
            font-size: 20px;
            font-weight: 800;
            color: #1B5E20;
        }

        .login-box .subtitle {
            font-size: 12px;
            color: #388E3C;
            margin-top: 2px;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #8BC34A, #558B2F);
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
            color: #1B5E20;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #A5D6A7;
            border-radius: 12px;
            font-size: 13.5px;
            background: rgba(255,255,255,0.85);
            color: #1B5E20;
            outline: none;
            transition: all .2s;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #66BB6A;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(139,195,74,0.2);
        }

        .hint-text {
            font-size: 11px;
            color: #558B2F;
            margin-top: 4px;
        }

        .error-text { font-size: 11.5px; color: #c0392b; margin-top: 4px; }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #8BC34A, #558B2F);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all .2s;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(139,195,74,0.4);
            letter-spacing: .3px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #9CCC65, #33691E);
            box-shadow: 0 6px 20px rgba(139,195,74,0.5);
            transform: translateY(-1px);
        }

        .btn-login:disabled { opacity: .7; transform: none; cursor: not-allowed; }

        .switch-link {
            text-align: center;
            margin-top: 20px;
            font-size: 12.5px;
            color: #2E7D32;
        }

        .switch-link a {
            color: #1B5E20;
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

        .flash-warning {
            background: #FFFDE7;
            border: 1px solid #FFF176;
            color: #F57F17;
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
            background: rgba(139,195,74,0.25);
            margin: 20px 0;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
        }

        .remember-wrap input { width: auto; }
        .remember-wrap label { margin: 0; font-size: 12.5px; color: #388E3C; font-weight: 500; }

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
            Portal Siswa<br>
            <em>Pembayaran SPP</em><br>
            Mudah & Cepat
        </h1>

        <p>
            Bayar SPP kapan saja dan di mana saja melalui platform digital
            SMK Muhammadiyah 2 Tangerang. Tidak perlu antri, cukup beberapa
            klik dari smartphone atau komputer Anda.
        </p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#FDD835" stroke-width="2">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                Bayar via GoPay, OVO, DANA, QRIS & Transfer Bank
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#FDD835" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                Notifikasi langsung setelah pembayaran berhasil
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
                        <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                Download bukti pembayaran dalam format PDF
            </div>
        </div>

    </div>

    <!-- ── SISI KANAN: BOX LOGIN SISWA ── -->
    <div class="login-box">

        <div class="logo-wrap">
            <div class="logo-icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <h2>SPP PINTAR</h2>
                <p class="subtitle">SMK Muhammadiyah 2 Tangerang</p>
            </div>
        </div>

        <div class="role-badge">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
            Portal Siswa
        </div>

        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="flash-warning">{{ session('warning') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login.siswa.post') }}" novalidate>
            @csrf

            <div style="margin-bottom:16px">
                <label for="login">NIS (Nomor Induk Siswa)</label>
                <input type="text" id="login" name="login"
                       value="{{ old('login') }}"
                       placeholder="Contoh: 2024001"
                       autocomplete="username"
                       inputmode="numeric"
                       autofocus>
                <p class="hint-text">Password default = NIS Anda. Ganti setelah login pertama.</p>
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
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:11px;color:#388E3C;font-weight:600;">
                        <span id="pwd-label">Lihat</span>
                    </button>
                </div>
                @error('password')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="remember-wrap">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya</label>
            </div>

            <button type="submit" id="btn-submit" class="btn-login">
                Masuk ke Portal Siswa
            </button>

        </form>

        <div class="divider"></div>

        <div class="switch-link">
            Login sebagai admin?
            <a href="{{ route('login.admin') }}">Login Admin</a>
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