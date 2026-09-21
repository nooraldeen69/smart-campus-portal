@echo off
chcp 65001 >nul
title Smart Campus Portal
cd /d "%~dp0"

rem ---- find PHP ----
set "PHPEXE=php"
where php >nul 2>nul
if errorlevel 1 (
    if exist "C:\xampp\php\php.exe" (
        set "PHPEXE=C:\xampp\php\php.exe"
    ) else if exist "C:\laragon\bin\php\php.exe" (
        set "PHPEXE=C:\laragon\bin\php\php.exe"
    ) else (
        echo.
        echo  PHP was not found. Install PHP 8.2+ or add it to your PATH.
        echo  ^(XAMPP users: it is usually C:\xampp\php^)
        echo.
        pause
        exit /b 1
    )
)

rem ---- first run: create .env + app key ----
if not exist ".env" (
    copy ".env.example" ".env" >nul
    "%PHPEXE%" artisan key:generate --force
)

rem ---- database, mock university back-end, background worker and the web server (0.0.0.0 = whole network) ----
"%PHPEXE%" artisan portal:serve

echo.
echo  The portal has stopped.
pause
