::@echo off

cd ../../../
cd script

::Update bot_id =0 
start /min BotClearClassify.bat 19 27 34 37 44 31 40 41 11
timeout /t 5

cd ../../../
cd script

echo Start Match ....
echo ====== Start Run Match Classify ====== %date% %time% >> C:\script\log_match\Match_Classify.txt

start /min StartClassify.bat 19 37
timeout /t 5
start /min StartClassify.bat 27
timeout /t 5
start /min StartClassify.bat 34
timeout /t 5
start /min StartClassify.bat 44 31 40
timeout /t 5
start /min StartClassify.bat 41 11
timeout /t 5


:loop 
FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  StartClassify*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A
echo Bot Running : %returnvalue%
if %returnvalue% GTR 0 (
	timeout /T 30 /NOBREAK
	goto loop
)	

echo End Run Match Classify %date% %time% >> C:\script\log_match\Match_Classify.txt
timeout /t 5

::pause
exit

::cd ../../../
::cd script
::Index_and_Match_Classify.bat