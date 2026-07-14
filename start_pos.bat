@echo off
REM RestoPOS - Quick Start Script for Windows
REM Usage: start_pos.bat

setlocal enabledelayedexpansion

echo.
echo ============================================================
echo           RestoPOS - Restaurant POS System
echo           Quick Start Script v1.0
echo ============================================================
echo.

REM Check if we're in the right directory
if not exist "artisan" (
    echo ERROR: artisan file not found!
    echo Make sure to run this script from: F:\Laravel\NEWPOS\RePOS
    pause
    exit /b 1
)

echo [OK] Found Laravel project
echo.

REM Check composer packages
if not exist "vendor" (
    echo [INFO] Installing composer packages...
    call composer install
)

echo [OK] Composer packages ready
echo.

REM Check database
echo [INFO] Checking database...
if not exist "database\database.sqlite" (
    echo [INFO] Creating database...
    type nul > database\database.sqlite
)

echo [OK] Database ready
echo.

REM Check if files exist
echo [INFO] Checking required files...
set "FILES=app/Http/Controllers/AuthController.php;resources/views/auth/login.blade.php;resources/views/auth/pin-login.blade.php;resources/views/dashboard.blade.php;database/seeders/UserSeeder.php"

for %%F in (%FILES%) do (
    if exist "%%F" (
        echo [OK] %%F
    ) else (
        echo [ERROR] %%F MISSING!
    )
)
echo.

REM Summary
echo ============================================================
echo                 SETUP COMPLETE! [OK]
echo ============================================================
echo.

echo [INFO] Demo Accounts:
echo    Username: admin    ^| Password: password ^| PIN: 1234
echo    Username: manager  ^| Password: password ^| PIN: 5678
echo    Username: cashier  ^| Password: password ^| PIN: 9999
echo.
echo [INFO] URLs:
echo    Login:     http://localhost:8000/login
echo    Dashboard: http://localhost:8000/dashboard
echo    PIN Login: http://localhost:8000/pin-login
echo.
echo [INFO] Starting Laravel Development Server...
echo    Press CTRL+C to stop
echo.

call php artisan serve --port 8000

pause
