cd ../../../
cd spider_bot

::@ECHO OFF
::@echo Clear Kpiology Matcher (-1) %%A
::php index.php matcher_auto_id clear 10 %%A

echo ====== Start Run Kpiology Matcher ====== %date% %time% >> C:\script\log_match\Match_all.txt

::php index.php matcher_auto_id bot update 10
php index.php matcher_auto_id bot update 19
php index.php matcher_auto_id bot update 12
php index.php matcher_auto_id bot update 14
php index.php matcher_auto_id bot update 21
php index.php matcher_auto_id bot update 27
php index.php matcher_auto_id bot update 30
php index.php matcher_auto_id bot update 31

echo End Run Kpiology Matcher %date% %time% >> C:\script\log_match\Match_all.txt

timeout /t 10
::pause


cd ../../../
cd script
Index_and_Match_All.bat