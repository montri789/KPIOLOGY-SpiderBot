::@echo off
cd ../../../
cd script

echo Clear Subject bot_id !=0
start /min BotClear.bat 33
timeout /t 5

cd ../../../
cd spider_bot

echo Start Match Pantip....

echo ====== Start Run Kpiology Matcher Pantip ====== %date% %time% >> C:\script\log_match\Match_Pantip.txt

start /min php index.php matcher_auto bot update 9313 33

echo End Run Kpiology Matcher Pantip %date% %time% >> C:\script\log_match\Match_Pantip.txt

::pause
exit