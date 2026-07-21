@echo off
title DineFlow AI POS Auto-Launcher
color 0A
echo ========================================================
echo        Starting DineFlow AI POS Services...
echo ========================================================
echo.

:: 1. Start XAMPP MySQL Database
echo [1/3] Starting MySQL Database Server...
start /B C:\xampp\mysql\bin\mysqld.exe --standalone

:: Wait 2 seconds
timeout /t 2 /nobreak >nul

:: 2. Change directory to project
cd /d "d:\DineFlow AI"

:: 3. Open browser after 2 seconds
echo [2/3] Opening DineFlow POS in browser...
start http://localhost:8000/login

:: 4. Start Laravel Server
echo [3/3] Starting Laravel Development Server (Host: 0.0.0.0:8000)...
echo.
echo ========================================================
echo   DineFlow AI POS is LIVE!
echo   - Laptop Access: http://localhost:8000
echo.
echo   (Keep this window minimized while using the system)
echo ========================================================
echo.

C:\xampp\php\php.exe artisan serve --host=0.0.0.0 --port=8000
pause
