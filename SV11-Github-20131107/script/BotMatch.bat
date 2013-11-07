@echo off

cd ../../../
cd spider_bot

title BotMatch 

@echo off
FOR %%A IN (%*) DO (
	echo running matcher_auto %%A
	
	php index.php matcher_auto_11 bot update %%A 9312	
)

::php index.php matcher_auto bot update 9312 %%A
::php index.php matcher_auto_11 bot update %%A 9312

::pause
exit