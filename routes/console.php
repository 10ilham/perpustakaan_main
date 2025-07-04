<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Register Scheduled Tasks
|--------------------------------------------------------------------------
|
| Di Laravel 12, jadwal tugas didefinisikan di console.php, bukan di Kernel.php
| seperti pada versi Laravel sebelumnya.
|
*/

// Menjalankan command pengecekan dan pengiriman notifikasi pada pukul 07:00 pagi setiap hari
Schedule::command('app:send-pengembalian-reminders')
    ->dailyAt('07:00')
    //->cron('0 7 * * *') // Setiap hari pada pukul 07:00
    ->appendOutputTo(storage_path('logs/scheduler-pengembalian-reminders.log'))
    ->description('Pengiriman notifikasi pengembalian pagi hari');

// Menjalankan command notifikasi setiap jam
Schedule::command('app:send-pengembalian-reminders')
    //->everyTwoHours() menjalankan setiap 2 jam
    ->hourly()
    ->appendOutputTo(storage_path('logs/scheduler-pengembalian-reminders.log'))
    ->description('Pengiriman notifikasi pengembalian setiap jam');

// Menjalankan command notifikasi setiap 30 menit
// Schedule::command('app:send-pengembalian-reminders')
//     ->everyThirtyMinutes()
//     ->appendOutputTo(storage_path('logs/scheduler-pengembalian-reminders.log'))
//     ->description('Pengiriman notifikasi pengembalian setiap 30 menit');

// Menjalankan command test notifikasi setiap menit untuk keperluan testing
// Schedule::command('app:test-notification')
//     ->everyMinute()
//     ->appendOutputTo(storage_path('logs/scheduler-test-notification.log'))
//     ->description('Pengujian pengiriman notifikasi');

// Menjalankan notifikasi admin untuk peminjaman baru setiap 1 menit (untuk keperluan testing)
// Schedule::command('app:send-pengingat-notifikasi-admin')
//     ->everyMinute()
//     ->appendOutputTo(storage_path('logs/admin-notification.log'))
//     ->description('Pengiriman notifikasi peminjaman baru ke admin');

// Menjalankan notifikasi anggota untuk peminjaman manual setiap 1 menit (untuk keperluan testing)
// Schedule::command('app:send-notifikasi-peminjaman-manual')
//     ->everyMinute()
//     ->appendOutputTo(storage_path('logs/peminjaman-manual-notification.log'))
//     ->description('Pengiriman notifikasi peminjaman manual ke anggota');
