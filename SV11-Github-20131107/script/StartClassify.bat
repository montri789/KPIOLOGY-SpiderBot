@echo off

cd ../../../
cd spider_bot

title StartClassify 

@echo off
FOR %%A IN (%*) DO (
	echo running matcher classify %%A
	php index.php matcher_classify_11 bot update %%A 9314
)

::pause
exit