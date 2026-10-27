@echo off
title DineFlow AI - Mobile Application Metro Bundler
color 0A

echo ======================================================================
echo           DINEFLOW AI : REACT NATIVE MOBILE APPLICATION
echo ======================================================================
echo.
echo [1/3] Navigating to React Native Expo project...
cd /d "%~dp0delisty-food-delivery-react-native-template-2025-07-14-15-46-09-utc\App\delisty"

echo [2/3] Configuring Node.js environment & OpenSSL support...
set NODE_OPTIONS=--openssl-legacy-provider
node -v
echo.

echo [3/3] Starting Expo Development Server...
echo.
echo ======================================================================
echo  SCAN THE QR CODE WITH EXPO GO ON YOUR ANDROID OR IPHONE TO TEST!
echo  Press 'w' in terminal to open in Web Browser (http://localhost:19006)
echo  Press 'a' in terminal to open in Android Emulator
echo ======================================================================
echo.

npm.cmd start

pause
