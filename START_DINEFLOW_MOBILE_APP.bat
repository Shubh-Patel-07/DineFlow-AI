@echo off
title DineFlow AI - Mobile Application Metro Bundler
color 0A

echo ======================================================================
echo           DINEFLOW AI : REACT NATIVE MOBILE APPLICATION
echo ======================================================================
echo.
echo [1/3] Navigating to React Native Expo project...
cd /d "%~dp0delisty-food-delivery-react-native-template-2025-07-14-15-46-09-utc\App\delisty"

echo [2/3] Detecting Wi-Fi IP for direct mobile connection...
set NODE_OPTIONS=--openssl-legacy-provider

for /f "tokens=*" %%a in ('powershell -NoProfile -Command "(Get-NetIPAddress -InterfaceAlias 'Wi-Fi' -AddressFamily IPv4 -ErrorAction SilentlyContinue).IPAddress"') do set WIFI_IP=%%a
if defined WIFI_IP (
    echo Detected Wi-Fi IP: %WIFI_IP%
    set REACT_NATIVE_PACKAGER_HOSTNAME=%WIFI_IP%
) else (
    echo Wi-Fi IP not found, using default network.
)
echo.

echo [3/3] Starting Expo Server & Web App...
echo.
echo ======================================================================
echo  1. PC BROWSER (Mobile View):  http://localhost:19006
echo  2. PHONE BROWSER:             http://%WIFI_IP%:19006
echo ======================================================================
echo.

npx.cmd expo start --web --host lan

pause
