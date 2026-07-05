<?php

use App\Http\Controllers\Admin\BillController as AdminBillController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReminderController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SpecialPaymentController as AdminSpecialPaymentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Siswa\BillController as SiswaBillController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\HistoryController;
use App\Http\Controllers\Siswa\PaymentController;
use App\Http\Controllers\Siswa\ReceiptController;
use App\Http\Controllers\Siswa\SpecialPaymentController as SiswaSpecialPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login.siswa'));

// ── PUBLIC ────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login/admin', [AuthController::class, 'showLoginAdmin'])->name('login.admin');
    Route::post('/login/admin', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.admin.post');
    Route::get('/login/siswa', [AuthController::class, 'showLoginSiswa'])->name('login.siswa');
    Route::post('/login/siswa', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.siswa.post');
    Route::get('/login', fn() => redirect()->route('login.siswa'))->name('login');
});

// ── AUTHENTICATED ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ganti-password', [AuthController::class, 'showChangePassword'])->name('password.change.show');
    Route::post('/ganti-password', [AuthController::class, 'changePassword'])->name('password.change.update');

    // ── ADMIN ─────────────────────────────────────────────────────────────────
    Route::middleware(['must.change.password', 'role:admin'])
        ->prefix('admin')->name('admin.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

            // Data Siswa
            Route::resource('students', StudentController::class);
            Route::post('students/{student}/reset-password', [StudentController::class, 'resetPassword'])
                ->name('students.reset-password');

            // Tagihan SPP
            Route::get('/tagihan', [AdminBillController::class, 'index'])->name('tagihan.index');
            Route::get('/tagihan/create', [AdminBillController::class, 'create'])->name('tagihan.create');
            Route::post('/tagihan', [AdminBillController::class, 'store'])->name('tagihan.store');
            Route::delete('/tagihan/{bill}', [AdminBillController::class, 'destroy'])->name('tagihan.destroy');

            // Pembayaran
            Route::get('/pembayaran', [AdminPaymentController::class, 'index'])->name('pembayaran.index');

            // Laporan
            Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
            Route::get('/laporan/export', [ReportController::class, 'export'])->name('laporan.export');

            // Notifikasi
            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
            Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

            // Pembayaran Khusus (KI / GTS / PKL)
            Route::get('/special-payments', [AdminSpecialPaymentController::class, 'index'])->name('special-payments.index');
            Route::get('/special-payments/create', [AdminSpecialPaymentController::class, 'create'])->name('special-payments.create');
            Route::post('/special-payments', [AdminSpecialPaymentController::class, 'store'])->name('special-payments.store');
            Route::delete('/special-payments/{specialPayment}', [AdminSpecialPaymentController::class, 'destroy'])->name('special-payments.destroy');

            // Reminder Tagihan
            Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
            Route::post('/reminders/{bill}/send', [ReminderController::class, 'send'])->name('reminders.send');
            Route::post('/reminders/send-bulk', [ReminderController::class, 'sendBulk'])->name('reminders.send-bulk');
        });

    // ── SISWA ─────────────────────────────────────────────────────────────────
    Route::middleware(['must.change.password', 'role:student'])
        ->prefix('siswa')->name('siswa.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');

            // Tagihan & Pembayaran
            Route::get('/tagihan', [SiswaBillController::class, 'index'])->name('tagihan.index');
            Route::post('/tagihan/{bill}/bayar', [PaymentController::class, 'initiate'])->name('tagihan.bayar');
            Route::get('/tagihan/{bill}/status', [PaymentController::class, 'checkStatus'])->name('tagihan.status');
            Route::post('/tagihan/{bill}/cancel', [PaymentController::class, 'cancel'])->name('tagihan.cancel');

            // Riwayat Pembayaran
            Route::get('/riwayat', [HistoryController::class, 'index'])->name('riwayat.index');

            // Cetak Bukti PDF
            Route::get('/bukti/{payment}', [ReceiptController::class, 'print'])->name('bukti.print');

            // Pembayaran Khusus (KI / GTS / PKL)
            Route::get('/special-payments', [SiswaSpecialPaymentController::class, 'index'])->name('special-payments.index');
            Route::post('/special-payments/initiate', [SiswaSpecialPaymentController::class, 'initiate'])->name('special-payments.initiate');
            Route::post('/special-payments/status', [SiswaSpecialPaymentController::class, 'checkStatus'])->name('special-payments.status');
        });
});

// ── MIDTRANS WEBHOOK ──────────────────────────────────────────────────────────
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])->name('midtrans.callback');
