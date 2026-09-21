@echo off
chcp 65001 >nul
title Allow Smart Campus Portal through Windows Firewall

net session >nul 2>&1
if errorlevel 1 (
    echo.
    echo  Please RIGHT-CLICK this file and choose "Run as administrator".
    echo.
    pause
    exit /b 1
)

netsh advfirewall firewall delete rule name="Smart Campus Portal (8000)" >nul 2>&1
netsh advfirewall firewall delete rule name="Smart Campus Portal mock SSO (8001)" >nul 2>&1
netsh advfirewall firewall add rule name="Smart Campus Portal (8000)" dir=in action=allow protocol=TCP localport=8000 profile=private,domain
netsh advfirewall firewall add rule name="Smart Campus Portal mock SSO (8001)" dir=in action=allow protocol=TCP localport=8001 profile=private,domain

echo.
echo  Done. Phones / other PCs on your Wi-Fi/LAN can now reach ports 8000 and 8001.
echo.
echo  If it still does not work: Windows Settings ^> Network ^& Internet ^> Wi-Fi ^> your network
echo  and set the network profile to "Private" (rules above apply to Private/Domain networks only).
echo.
pause
