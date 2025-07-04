@echo off
cd /d "D:\web\TA\perpustakaan"

REM Menggunakan path PHP yang benar
set PHP_PATH=C:\laragon\bin\php\php-8.3.21-Win32-vs16-x64\php.exe

echo [%date% %time%] Menggunakan PHP: %PHP_PATH% > storage/logs/queue-worker-debug.log
%PHP_PATH% artisan queue:work
