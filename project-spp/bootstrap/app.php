<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'                => \App\Http\Middleware\RoleMiddleware::class,
            'must.change.password' => \App\Http\Middleware\MustChangePassword::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Kirim reminder setiap hari jam 07:00 pagi
        // Cek tagihan yang jatuh tempo 7 hari lagi
        $schedule->command('spp:send-reminders --days=7')
            ->dailyAt('07:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Opsional: kirim lagi H-3 sebagai pengingat kedua
        $schedule->command('spp:send-reminders --days=3')
            ->dailyAt('07:00')
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
