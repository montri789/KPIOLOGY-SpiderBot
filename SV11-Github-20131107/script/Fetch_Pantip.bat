cd ../../../
cd script

:loopStart

title Fetch-Pantip 

echo ====== Start Run fetchPantip 5000-105000 ====== %date% %time% >> C:\script\log_fetch\fetchPantipUpdate.txt

timeout /t 5
start /min BotPantip.bat 5000
timeout /t 20
start /min BotPantip.bat 25000 	
timeout /t 20
start /min BotPantip.bat 45000
timeout /t 20
::start /min BotPantip.bat 65000
::timeout /t 20
::start /min BotPantip.bat 85000
::timeout /t 20

:loop 
FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  BotPantip*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A
echo Bot Running : %returnvalue%
if %returnvalue% GTR 0 (
	timeout /T 120 /NOBREAK
	goto loop
)	

echo End Run fetchPantipWeek %date% %time% >> C:\script\log_fetch\fetchPantipUpdate.txt
timeout /t 5

goto loopStart
::exit;