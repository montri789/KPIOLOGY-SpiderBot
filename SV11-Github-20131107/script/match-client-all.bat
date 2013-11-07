cd ../../../
cd spider_bot


@ECHO OFF
echo ====== Start Run Kpiology Matcher ====== %date% %time% >> C:\script\log_match\Kpiology_match.txt

php index.php matcher_client_all_111 bot update 10
php index.php matcher_client_all_111 bot update 19
php index.php matcher_client_all_111 bot update 12
php index.php matcher_client_all_111 bot update 14
php index.php matcher_client_all_111 bot update 30
php index.php matcher_client_all_111 bot update 31


echo End Run Kpiology Matcher %date% %time% >> C:\script\log_match\Kpiology_match.txt
