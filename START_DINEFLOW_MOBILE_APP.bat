@echo off
title DineFlow AI - Mobile Application (Expo SDK 58)
color 0A

echo ======================================================================
echo           DINEFLOW AI : REACT NATIVE MOBILE APPLICATION (SDK 58)
echo ======================================================================
echo.
echo [1/3] Navigating to React Native Expo project...
cd /d "%~dp0delisty-food-delivery-react-native-template-2025-07-14-15-46-09-utc\App\delisty"

echo [2/3] Detecting Wi-Fi IP for direct mobile connection...
for /f "tokens=*" %%a in ('powershell -NoProfile -Command "(Get-NetIPAddress -InterfaceAlias 'Wi-Fi' -AddressFamily IPv4 -ErrorAction SilentlyContinue).IPAddress"') do set WIFI_IP=%%a
if defined WIFI_IP (
    echo Detected Wi-Fi IP: %WIFI_IP%
    set REACT_NATIVE_PACKAGER_HOSTNAME=%WIFI_IP%
) else (
    echo Wi-Fi IP not found, using default network.
    set WIFI_IP=localhost
)
echo.

echo [3/3] Starting Modern Expo Metro Server for iPhone & Android (SDK 58)...
echo.
echo ======================================================================
echo  1. IPHONE (EXPO GO SDK 58):    Scan terminal QR code OR enter:
echo                                 exp://%WIFI_IP%:8081
echo.
echo  2. PHONE BROWSER (Safari):     http://%WIFI_IP%:8081
echo  3. PC BROWSER (Mobile View):   http://localhost:8081
echo ======================================================================
echo.

npx.cmd expo start --host lan

pause
