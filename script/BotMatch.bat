@echo off

cd ../../../
cd spider_bot

title BotMatch 

@echo off
FOR %%A IN (%*) DO (
	echo running with parameter php index.php matcher_auto_id bot update %%A
	php index.php matcher_auto_id bot update %%A
)

::pause
exit