@echo off
title BLUESKY Transactions - Startup
color 0B
echo.
echo  ======================================
echo   BLUESKY TRANSACTIONS - Startup
echo  ======================================
echo.

:: Check if XAMPP MySQL is running
echo  [1/4] Checking MySQL connection...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "SELECT 1;" >nul 2>&1
IF ERRORLEVEL 1 (
    echo  [!] MySQL is not accessible - Please start XAMPP first!
    echo  [!] Open XAMPP Control Panel and start MySQL
    echo.
    pause
    exit /b 1
)
echo  [OK] MySQL connected

:: Create the database if it doesn't exist
echo  [2/4] Setting up the database...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS bluesky_transactions CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1
echo  [OK] Database ready

:: Run migrations and seeders
echo  [3/5] Running migrations and seeders...
php artisan migrate --seed --force
echo  [OK] Database migrated and seeded

:: Clear caches and fix storage symlink
echo  [4/5] Clearing caches and fixing storage link...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
IF EXIST "public\storage" (
    rmdir "public\storage" 2>nul || del /f "public\storage" 2>nul
)
php artisan storage:link
echo  [OK] Caches cleared and storage linked

:: Get local network IP address
echo  [5/5] Starting server on all network interfaces...
echo.
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr /v "127.0.0.1"') do (
    set RAW_IP=%%a
)
:: Trim leading space from IP
set LOCAL_IP=%RAW_IP: =%

echo  ======================================
echo   Application available at:
echo.
echo   Local  : http://localhost:8000
if defined LOCAL_IP (
    echo   Network: http://%LOCAL_IP%:8000
    echo.
    echo   Acces depuis telephone / autre appareil:
    echo   Connecte-toi au meme WiFi puis ouvre:
    echo   http://%LOCAL_IP%:8000
)
echo  ======================================
echo.
start http://localhost:8000
php artisan serve --host=0.0.0.0 --port=8000
