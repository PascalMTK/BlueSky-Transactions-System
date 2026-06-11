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

echo  [5/5] Starting development server...
echo.
echo  ======================================
echo   Application available at:
echo   http://localhost:8000
echo.
echo  
start http://localhost:8000
php artisan serve
