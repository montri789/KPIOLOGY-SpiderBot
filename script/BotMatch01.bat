@echo off

cd ../../../
cd spider_bot

title "BotMatch" 

echo ====== Start Run Kpiology Matcher ====== %date% %time% >> C:\script\log_match\Match_all_2.txt

@echo off
FOR %%A IN (%*) DO (
	::echo running with parameter %%A
	php index.php matcher_auto_id bot update %%A
)
pause