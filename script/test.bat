@echo OFF

::FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  Bot*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A
FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  BotMat*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A

if %returnvalue% == 6 echo "BotMatch is 6"
if %returnvalue% == 5 echo "BotMatch is 5"
if %returnvalue% == 4 echo "BotMatch is 4" 
if %returnvalue% == 3 echo "BotMatch is 3"
if %returnvalue% == 2 echo "BotMatch is 2"
if %returnvalue% == 1 echo "BotMatch is 1"
if %returnvalue% == 0 echo "BotMatch is 0" 

pause