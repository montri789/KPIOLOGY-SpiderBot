::@echo off

cd ../../../
cd script

::Clear subject matching_status='matching' and bot_id !=0
start /min BotClear.bat 33
timeout /t 5

cd ../../../
cd script

echo Start Match ....

echo ====== Start Run Kpiology Matcher ====== %date% %time% >> C:\script\log_match\Match_Website.txt

start /min MatchWeb.bat 33
timeout /t 5


:loop 
FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  MatchWebsite*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A
echo Bot Running : %returnvalue%
if %returnvalue% GTR 0 (
	::timeout /T 120 /NOBREAK
	timeout /T 10 /NOBREAK
	goto loop
)	

echo End Run Kpiology Matcher %date% %time% >> C:\script\log_match\Match_Website.txt

timeout /t 5

cd ../../../
cd script

Index_and_Match_Website.bat