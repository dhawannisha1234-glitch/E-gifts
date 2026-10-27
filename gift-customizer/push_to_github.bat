@echo off
title Push to GitHub - dhawannisha1234-glitch/E-gifts
echo ========================================================
echo   Push Gift Customizer to GitHub: dhawannisha1234-glitch
echo   Repository: E-gifts
echo ========================================================
echo.
echo Make sure the repository exists at:
echo https://github.com/dhawannisha1234-glitch/E-gifts
echo.

set GIT_CMD="git"

set /p TOKEN="Enter your GitHub Personal Access Token (or press ENTER to use GitHub browser/desktop login): "

if "%TOKEN%"=="" (
    %GIT_CMD% push -u origin main
) else (
    %GIT_CMD% push -u https://%TOKEN%@github.com/dhawannisha1234-glitch/E-gifts.git main
)

echo.
echo ========================================================
echo Finished! Press any key to close this window.
echo ========================================================
pause
