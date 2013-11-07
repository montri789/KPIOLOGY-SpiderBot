@echo off

title RunTwitter 

::cd ../../../
cd %~dp0
cd C:\spider_bot

FOR %%A IN (%*) DO (
	
	::echo %%A
	php index.php twitter_after getdata %%A
)

::pause
exit