cd ../../../
cd spider_bot

@ECHO OFF
@echo Parser Pantip running Domain 26 28
php index.php fetch3 update_root 26
php index.php fetch3 update_root 28
php index.php fetch3 all 26
php index.php fetch3 all 28 