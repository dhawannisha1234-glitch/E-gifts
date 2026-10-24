@echo off
title CraftGift Studio PHP Server
echo ========================================================
echo   CraftGift Studio - Personalized Gift E-Commerce Store
echo ========================================================
echo.
echo Starting PHP development server at http://localhost:8000...
echo.

start "" "http://localhost:8000"
php -S localhost:8000
pause
