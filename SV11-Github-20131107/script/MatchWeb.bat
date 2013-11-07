@echo off

cd ../../../
cd spider_bot

title MatchWebsite 

@echo off
FOR %%A IN (%*) DO (
	echo running matcher_auto %%A
	php index.php matcher_auto bot update 9313 %%A
)

::pause
exit