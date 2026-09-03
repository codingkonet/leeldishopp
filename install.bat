@echo off
setlocal
cd /d "%~dp0"
php install.php
if errorlevel 1 pause
endlocal
