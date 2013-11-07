cd ../../../
cd spider_bot

@ECHO OFF
@echo Auto Run TNS Matching for Samsung %%A


@echo ====== Start Run Kpiology Matcher ====== %date% %time% >> C:\script\log_match\Kpiology_match_21_6.txt

::php index.php matcher_no_comment_111 bot update 21 2013-04-24 
php index.php matcher_auto_111 bot update 21

@echo End %date% %time% >> C:\script\log_match\Kpiology_match_21_6.txt