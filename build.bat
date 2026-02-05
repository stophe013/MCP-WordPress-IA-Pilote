@echo off
REM ============================================================
REM Build WordPress Plugin - IA Pilote MCP Ability
REM ============================================================
REM Usage: build.bat [version]
REM Exemple: build.bat 1.6.0
REM ============================================================

set VERSION=%1
if "%VERSION%"=="" set VERSION=1.6.0

echo.
echo Building ia-pilote-mcp-ability v%VERSION%...
echo.

powershell -ExecutionPolicy Bypass -File "%~dp0build.ps1" -Version "%VERSION%"

echo.
pause
