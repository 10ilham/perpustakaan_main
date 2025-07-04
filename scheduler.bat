@echo off
cd /d "D:\web\TA\perpustakaan"

REM Menggunakan path PHP yang benar
set PHP_PATH=C:\laragon\bin\php\php-8.3.21-Win32-vs16-x64\php.exe

echo [%date% %time%] Menggunakan PHP: %PHP_PATH% >> storage/logs/scheduler-debug.log

:loop
echo [%date% %time%] Menjalankan scheduler... >> storage/logs/scheduler-debug.log
%PHP_PATH% artisan schedule:run >> storage/logs/scheduler.log
timeout /t 60 > nul
goto loop
