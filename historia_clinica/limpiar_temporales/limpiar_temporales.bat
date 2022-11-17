cd /d %~dp0
cd /d %~dp0\..\tmp
for /F "delims=" %%i in ('dir /b') do (rmdir "%%i" /s/q || del "%%i" /s/q)
