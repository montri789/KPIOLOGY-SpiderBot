::@echo off

cd ../../../
cd script

::Update bot_id =0 
start /min BotClear.bat 11 12 19 21 31 34 35 37 39 40 41 42 44 45 46 48 50
timeout /t 5

cd ../../../
cd script

echo Start Match ...
echo ====== Start Run Kpiology Matcher ====== %date% %time% >> C:\script\log_match\Match_all_2.txt

start /min BotMatchGroup.bat 19 27
timeout /t 5
start /min BotMatchGroup.bat 34 37
timeout /t 5
start /min BotMatchGroup.bat 44 31 40 41 11
timeout /t 5

start /min BotMatch.bat 11 12 19 31 44 45 46 48 50
timeout /t 5
start /min BotMatch.bat 35 37 39 40 41 42
timeout /t 5
start /min BotMatch.bat 34
timeout /t 5
start /min BotMatch.bat 34
timeout /t 5


:loop 
FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  BotMatch*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A
echo Bot Running : %returnvalue%
if %returnvalue% GTR 0 (
	timeout /T 60 /NOBREAK
	goto loop
)	

echo End Run Kpiology Matcher %date% %time% >> C:\script\log_match\Match_all_2.txt

timeout /t 5

cd ../../../
cd script

Index_and_Match_All.bat