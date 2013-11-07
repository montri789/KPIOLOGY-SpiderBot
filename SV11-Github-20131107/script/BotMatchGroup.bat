@echo off

cd ../../../
cd spider_bot

title BotMatchGroup 

@echo off
FOR %%A IN (%*) DO (
	echo running matcher_auto Group %%A	
	php index.php matcher_group_11 bot update %%A 9312
)

::pause
exit