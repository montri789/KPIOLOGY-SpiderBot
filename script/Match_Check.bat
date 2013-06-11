@echo off
:loop 
FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  BotMatch*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A
echo Bot Running : %returnvalue%
if %returnvalue% GTR 0 (
	timeout /T 120 /NOBREAK
	goto loop
)	

::pause
::exit