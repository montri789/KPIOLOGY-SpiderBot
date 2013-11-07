@echo off

cd ../../../
cd spider_bot

title BotClear 

@echo off
FOR %%B IN (%*) DO (
	echo running clear bot %%B	
	
	::php index.php matcher_auto clear %%B
	php index.php matcher_auto_11 clear %%B
)

::pause
exit