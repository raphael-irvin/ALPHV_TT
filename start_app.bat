@echo off
title ALPHV Project Launcher
color 0A

echo.
echo  =====================================================
echo      ALPHV TECHNICAL TEST - ENVIRONMENT LAUNCHER
echo  =====================================================
echo.
echo  PRE-SETUP (Manual Steps Required):
echo.
echo    [1] Open XAMPP Control Panel
echo    [2] Click START next to Apache
echo    [3] Click START next to MySQL
echo    [4] Open phpMyAdmin and create a database
echo        named exactly:  alphv_db
echo.
echo  Once all three steps are done, press any key.
echo  =====================================================
pause > nul

:: -------------------------------------------------------
:: Resolve the root directory of this .bat file
:: -------------------------------------------------------
set ROOT=%~dp0
set BACKEND=%ROOT%alphv-backend
set FRONTEND=%ROOT%alphv-frontend

:: -------------------------------------------------------
:: PHASE 1: SETUP
:: -------------------------------------------------------

cd /d "%BACKEND%"

:: Check for Composer dependencies and install if missing
echo.
echo  [SETUP 1/5] Checking Composer dependencies...
if not exist "vendor\" (
    echo  Installing... this may take a minute.
    composer install --no-interaction --quiet
    echo  [OK] Dependencies installed.
) else (
    echo  [OK] Vendor folder found. Skipping.
)

:: Check for .env file and create if missing
echo.
echo  [SETUP 2/5] Checking environment file...
if not exist ".env" (
    echo  No .env found. Copying from .env.example...
    copy ".env.example" ".env" > nul
    echo.
    echo  !! ACTION REQUIRED:
    echo     Open alphv-backend\.env and set these values:
    echo.
    echo       DB_CONNECTION=mysql
    echo       DB_HOST=127.0.0.1
    echo       DB_PORT=3306
    echo       DB_DATABASE=alphv_db
    echo       DB_USERNAME=root
    echo       DB_PASSWORD=
    echo.
    echo  Save the file, then press any key to continue.
    pause > nul
) else (
    echo  [OK] .env file found. Skipping.
)

:: Generate app key and run migrations
echo.
echo  [SETUP 3/5] Generating application key...
php artisan key:generate --ansi --no-interaction
echo  [OK] App key is set.

echo.
echo  [SETUP 4/5] Running database migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo.
    echo  [ERROR] Migration failed!
    echo  Double-check that:
    echo    - XAMPP MySQL is running
    echo    - alphv_db database exists in phpMyAdmin
    echo    - .env DB credentials are correct
    echo.
    pause
    exit /b 1
)
echo  [OK] Tables are ready.

:: Seed the admin account
echo.
echo  [SETUP 5/5] Seeding admin account...
php artisan db:seed --force
echo  [OK] Admin account ready (admin@alphv.com / admin)

:: -------------------------------------------------------
:: PHASE 2: LAUNCH SERVERS
:: -------------------------------------------------------

echo.
echo  =====================================================
echo  Setup complete. Starting servers...
echo  =====================================================

:: Start the Laravel backend server
echo.
echo  [LAUNCH 1/2] Starting Laravel Backend on port 8000...
start "ALPHV Backend [DO NOT CLOSE]" cmd /k "cd /d "%BACKEND%" && php artisan serve"

:: Start the PHP built-in server for the frontend
echo  [LAUNCH 2/2] Starting Frontend Server on port 5500...
start "ALPHV Frontend [DO NOT CLOSE]" cmd /k "cd /d "%FRONTEND%" && php -S localhost:5500"

:: Wait a moment for servers to boot up
echo.
echo  Waiting for servers to boot...
timeout /t 3 /nobreak > nul

:: Open the default browser to the login page
echo  Opening browser...
start http://localhost:5500/login.html

echo.
echo  =====================================================
echo   ALL SYSTEMS GO!
echo   Login: admin@alphv.com  ^|  Password: admin
echo   Keep both server windows open while testing.
echo  =====================================================
echo.
pause