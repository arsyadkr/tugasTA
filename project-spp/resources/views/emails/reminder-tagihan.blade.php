<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Tagihan SPP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #1e3a8a, #3b82f6); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; }
        .header p { color: #bfdbfe; font-size: 13px; margin-top: 4px; }
        .badge { display: inline-block; background: #fbbf24; color: #1e3a8a; font-size: 13px; font-weight: 700; padding: 6px 16px; border-radius: 99px; margin-top: 14px; }
        .body { padding: 28px 24px; }
        .greeting { font-size: 15px; color: #374151; margin-bottom: 16px; }
        .info-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 18px 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #dbeafe; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #6b7280; }
        .info-row .value { font-weight: 600; color: #1e3a8a; }
        .amount-box { background: #1e3a8a; border-radius: 10px; padding: 18px; text-align: center; margin: 20px 0; }
        .amount-box p { color: #bfdbfe; font-size: 13px; }
        .amount-box h2 { color: #fff; font-size: 26px; font-weight: 700; margin-top: 4px; }
        .warning { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; padding: 14px 18px; margin: 20px 0; display: flex; align-items: flex-start; gap: 12px; }
        .warning-icon { font-size: 20px; flex-shrink: 0; }
        .warning p { font-size: 13px; color: #92400e; line-height: 1.5; }
        .cta { text-align: center; margin: 24px 0; }
        .cta a { display: inline-block; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; text-decoration: none; padding: 13px 32px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .footer { background: #f9fafb; padding: 18px 24px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { font-size: 12px; color: #9ca3af; line-height: 1.6; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <h1>🔔 Reminder Tagihan SPP</h1>
        <p>SMK Muhammadiyah 2 Tangerang</p>
        <div class="badge">
            ⏰ Jatuh tempo {{ $daysLeft }} hari lagi
        </div>
    </div>

    <!-- Body -->
    <div class="body">

        <p class="greeting">
            Yth. Bapak/Ibu Orang Tua / Wali Murid dari <strong>{{ $student->name }}</strong>,
        </p>

        <p style="font-size:14px;color:#6b7280;line-height:1.7;margin-bottom:20px;">
            Kami ingin mengingatkan bahwa tagihan SPP putra/putri Bapak/Ibu akan segera jatuh tempo.
            Mohon segera melakukan pembayaran agar tidak terkena denda keterlambatan.
        </p>

        <!-- Info Siswa -->
        <div class="info-box">
            <div class="info-row">
                <span class="label">Nama Siswa</span>
                <span class="value">{{ $student->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">NIS</span>
                <span class="value">{{ $student->nis }}</span>
            </div>
            <div class="info-row">
                <span class="label">Kelas</span>
                <span class="value">{{ $student->schoolClass->name ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Periode SPP</span>
                <span class="value">{{ $bill->period_label }}</span>
            </div>
            <div class="info-row">
                <span class="label">Jatuh Tempo</span>
                <span class="value" style="color:#dc2626;">{{ $bill->due_date->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <!-- Nominal -->
        <div class="amount-box">
            <p>Jumlah yang harus dibayarkan</p>
            <h2>Rp {{ number_format($bill->amount, 0, ',', '.') }}</h2>
        </div>

        <!-- Warning -->
        <div class="warning">
            <span class="warning-icon">⚠️</span>
            <p>
                Pembayaran dapat dilakukan melalui portal siswa SPPHub dengan berbagai metode:
                <strong>QRIS, GoPay, OVO, DANA, Transfer Bank (BCA/BNI/BRI/Mandiri)</strong>, dan Kartu Kredit.
                Pembayaran akan diproses secara otomatis setelah konfirmasi.
            </p>
        </div>

        <!-- CTA -->
        <div class="cta">
            <a href="{{ config('app.url') }}/login/siswa">
                Bayar Sekarang →
            </a>
        </div>

        <p style="font-size:13px;color:#9ca3af;text-align:center;">
            Login menggunakan NIS siswa: <strong>{{ $student->nis }}</strong>
        </p>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            Email ini dikirim otomatis oleh Sistem SPP SMK Muhammadiyah 2 Tangerang.<br>
            Jl. Raden Fatah No. 100, Tangerang, Banten &nbsp;|&nbsp; Telp: 085717414288
        </p>
        <p style="margin-top:8px;">Jika sudah melakukan pembayaran, abaikan email ini.</p>
    </div>

</div>
</body>
</html>