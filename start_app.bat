@echo off
title ALPHV Project Launcher
echo ===================================================
echo     ALPHV TECHNICAL TEST - ENVIRONMENT LAUNCHER
echo ===================================================
echo.
echo Make sure XAMPP (Apache and MySQL) is running!
echo.
pause

echo.
echo [1/3] Booting Laravel Backend API...
start "ALPHV Backend" cmd /c "cd alphv-backend && php artisan serve"

echo [2/3] Booting Frontend Server...
start "ALPHV Frontend" cmd /c "cd alphv-frontend && php -S localhost:5500"

echo [3/3] Opening Web Browser...
timeout /t 2 /nobreak > nul
start http://localhost:5500/login.html

echo.
echo All systems go! You can safely close this master window.
echo (Leave the two new terminal windows open to keep servers running)
echo.
pause