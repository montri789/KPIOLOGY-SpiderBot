@echo off
title Twitter 

:loopStart

SET cnt=0 
for %%B IN (C:\twitterdata\*) DO (
      SET /a cnt=cnt + 1 
)
echo =========== File count = %cnt% =============


if %cnt% == 0 (
	goto loopEnd
)

::FOR /R C:\twitterdata %%f IN (test*) DO (
FOR /R C:\twitterdata %%f IN (2013*) DO (


	echo %%f
	SET delfile=%%f
	start /min run_twitter.bat %%f
	
	goto loop
)

	:loop 
	FOR /F "tokens=*" %%A IN ('tasklist /FI "WINDOWTITLE eq Administrator:  RunTwit*" ^| find "cmd.exe" /c') DO SET returnvalue=%%A
		
	echo Bot Twitter Running : %returnvalue%
	if %returnvalue% GTR 0 (
		::timeout /T 120 /NOBREAK
		timeout /T 10 /NOBREAK
		goto loop
	)
	
	::==================
	IF EXIST %delfile% (
		echo Delete File : %delfile%
		del %delfile%
	)
	goto loopStart
			
	

:loopEnd	
echo End Script Get Twitter
timeout /T 10

pause
exit