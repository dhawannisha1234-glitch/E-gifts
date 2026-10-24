@echo off
title Push to GitHub - dhawannisha1234-glitch/customize-gift
echo ========================================================
echo   Push Gift Customizer to GitHub: dhawannisha1234-glitch
echo   Repository: customize-gift
echo ========================================================
echo.
echo Make sure the repository exists at:
echo https://github.com/new?name=customize-gift
echo.

set GIT_CMD="C:\Users\Sanjeev Dhawan\AppData\Local\GitHubDesktop\app-3.6.7\resources\app\git\cmd\git.exe"

set /p TOKEN="Enter your GitHub Personal Access Token (or press ENTER to use GitHub browser/desktop login): "

if "%TOKEN%"=="" (
    %GIT_CMD% push -u origin main
) else (
    %GIT_CMD% push -u https://%TOKEN%@github.com/dhawannisha1234-glitch/customize-gift.git main
)

echo.
echo ========================================================
echo Finished! Press any key to close this window.
echo ========================================================
pause
