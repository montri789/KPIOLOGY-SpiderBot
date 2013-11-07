cd ../../../
cd spider_bot

@ECHO OFF
@echo Parser Pantip Not Insert Last 5-10 hours

echo ====== Start Run Pantip_not_insert ====== %date% %time% >> C:\script\log_fetch\Pantip_not_insert.txt

php index.php fetch3_pantip_not_insert all 212

echo End %date% %time% >> C:\script\log_fetch\Pantip_not_insert.txt
