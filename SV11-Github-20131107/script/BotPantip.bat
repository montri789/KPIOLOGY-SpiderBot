@echo off

cd ../../../
cd spider_bot

title BotPantip 

@echo off
FOR %%A IN (%*) DO (
	echo running update 20000 page %%A
	php index.php fetch3_pantip_update all 212 20000 %%A
)

::pause
exit