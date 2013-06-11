cd ../../../
cd script

echo ====== Start Run Kpiology Matcher ====== %date% %time% >> C:\script\log_match\Match_all_2.txt

start /min BotMatch.bat 11 12 19 14 30 31 32
timeout /t 5
start /min BotMatch.bat 27
timeout /t 5
start /min BotMatch.bat 21
timeout /t 5
start /min BotMatch.bat 21
timeout /t 5
start /min BotMatch.bat 21
timeout /t 5
start /min BotMatch.bat 21

timeout /t 5

:loop 
FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  BotMatch*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A
echo Bot Running : %returnvalue%
if %returnvalue% GTR 0 (
	timeout /T 120 /NOBREAK
	goto loop
)	

echo End Run Kpiology Matcher %date% %time% >> C:\script\log_match\Match_all_2.txt

::10 Seconds
timeout /t 10

cd ../../../
cd script

Index_and_Match_All.bat