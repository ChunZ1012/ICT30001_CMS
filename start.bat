echo off
set arg1=%1
If /i "%arg1%" == "zerotier" goto zerotier
If /i "%arg1%" == "localip" goto localip
If /i "%arg1%" == "local" goto local

echo Invalid option, only `zerotier`, `localip`, and `local` are available
goto exit

:zerotier
echo Starting spark server at zerotier
php spark serve --host 10.144.255.254 --port 8080
goto exit

:localip
rem Get local ip address
for /f "delims=[] tokens=2" %%a in ('ping -4 -n 1 %ComputerName% ^| findstr [') do set NetworkIP=%%a
echo Starting spark server at local ip
php spark serve --host %NetworkIP% --port 8080
goto exit

:local
echo Starting spark server at localhost
php spark serve
goto exit

:exit