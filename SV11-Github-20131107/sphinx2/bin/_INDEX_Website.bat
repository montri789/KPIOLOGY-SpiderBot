::@echo off
::cd %~dp0

net stop SphinxSearch2
title "Match INDEX Website" 
echo  ===== Start Index ===== %date% %time% >> .\log_genIndex\src1_pantip_index.txt

::indexer src1_web_index --config C:\sphinx2\sphinx2.conf >> .\log_genIndex\src1_web_index.txt
indexer src1_pantip_index --config C:\sphinx2\sphinx2.conf >> .\log_genIndex\src1_pantip_index.txt

echo  ===== End Index ===== %date% %time% >> .\log_genIndex\src1_pantip_index.txt

net start SphinxSearch2


::timeout /t 120
timeout /t 50
cd ../../../

echo Run Matcher

cd script
Match_Website.bat