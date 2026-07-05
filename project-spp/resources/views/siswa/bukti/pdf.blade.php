<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Pembayaran SPP</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'DejaVu Sans',Arial,sans-serif;font-size:11px;color:#111;background:#fff;}
        .page{width:148mm;min-height:210mm;padding:12mm;}
        .header{text-align:center;border-bottom:2px solid #1e3a5f;padding-bottom:5mm;margin-bottom:5mm;}
        .school{font-size:14px;font-weight:bold;color:#1e3a5f;}
        .address{font-size:9px;color:#6b7280;margin-top:1mm;}
        .title{font-size:13px;font-weight:bold;color:#fff;background:#1e3a5f;padding:2mm 6mm;display:inline-block;border-radius:2mm;margin-top:3mm;}
        .badge{display:inline-block;background:#dcfce7;color:#15803d;font-size:10px;font-weight:bold;padding:1.5mm 4mm;border-radius:2mm;margin:4mm 0;}
        table.info{width:100%;border-collapse:collapse;margin-bottom:4mm;}
        table.info td{padding:2mm 0;font-size:10px;}
        table.info td:first-child{color:#6b7280;width:40mm;}
        table.info td:nth-child(2){width:5mm;}
        table.info td:last-child{font-weight:bold;}
        .amount-box{border:1.5px solid #1e3a5f;border-radius:2mm;padding:4mm 6mm;margin:5mm 0;}
        .amount-label{font-size:9px;color:#6b7280;}
        .amount-value{font-size:18px;font-weight:bold;color:#1e3a5f;margin-top:1mm;}
        .footer{margin-top:10mm;display:flex;justify-content:space-between;align-items:flex-end;}
        .sig-line{width:35mm;border-top:1px solid #374151;margin-bottom:1mm;}
        .sig-label{font-size:8px;color:#6b7280;text-align:center;}
        .watermark{position:fixed;bottom:15mm;right:10mm;font-size:50px;color:#1e3a5f;opacity:.04;font-weight:bold;transform:rotate(-30deg);}
    </style>
</head>
<body>
<div class="page">
    <div class="watermark">LUNAS</div>
    <div class="header">
        <div class="school">SMK Sistem Informasi</div>
        <div class="address">Jl. Pendidikan No. 1 · Telp. (021) 000-0000</div>
        <div class="title">BUKTI PEMBAYARAN SPP</div>
    </div>
    <div class="badge">&#10003; Pembayaran Terverifikasi</div>
    <table class="info">
        <tr><td>Nama Siswa</td><td>:</td><td>{{ $student->name }}</td></tr>
        <tr><td>NIS</td><td>:</td><td>{{ $student->nis }}</td></tr>
        <tr><td>Kelas</td><td>:</td><td>{{ $student->schoolClass->name??'-' }}</td></tr>
        <tr><td>Jurusan</td><td>:</td><td>{{ $student->major->name??'-' }}</td></tr>
        <tr><td>Periode SPP</td><td>:</td><td>{{ $bill->period_label }}</td></tr>
    </table>
    <table class="info">
        <tr><td>No. Order</td><td>:</td><td>{{ $payment->order_id }}</td></tr>
        <tr><td>Metode Bayar</td><td>:</td><td>{{ $payment->payment_type_label }}</td></tr>
        <tr><td>Tgl Pembayaran</td><td>:</td><td>{{ $payment->paid_at?->format('d F Y H:i') }}</td></tr>
        <tr><td>Status</td><td>:</td><td><span style="color:#15803d;font-weight:bold;">LUNAS</span></td></tr>
    </table>
    <div class="amount-box">
        <div class="amount-label">Jumlah yang Dibayarkan</div>
        <div class="amount-value">Rp {{ number_format($payment->amount,0,',','.') }}</div>
    </div>
    <div class="footer">
        <div>
            <div style="font-size:8px;color:#9ca3af;">ID: {{ $payment->transaction_id??'-' }}</div>
            <div style="font-size:8px;color:#9ca3af;margin-top:1mm;">Dicetak: {{ $tanggal }}</div>
        </div>
        <div style="text-align:center;">
            <div class="sig-line"></div>
            <div class="sig-label">Bendahara Sekolah</div>
        </div>
    </div>
    <p style="font-size:8px;color:#d1d5db;margin-top:8mm;text-align:center;">Dokumen ini dicetak otomatis oleh Sistem SPP. Simpan sebagai bukti pembayaran Anda.</p>
</div>
</body>
</html>