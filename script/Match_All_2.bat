cd ../../../
cd script

echo ====== Start Run Kpiology Matcher ====== %date% %time% >> C:\script\log_match\Match_all_2.txt

::Clear subject matching_status='matching'
::timeout /t 15

start /min BotMatch.bat 10 11 12 19 14 30 31 32
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
	::timeout /T 120 /NOBREAK
	timeout /T 100 /NOBREAK
	goto loop
)	

echo End Run Kpiology Matcher %date% %time% >> C:\script\log_match\Match_all_2.txt

timeout /t 5

cd ../../../
cd script

Index_and_Match_All.bat