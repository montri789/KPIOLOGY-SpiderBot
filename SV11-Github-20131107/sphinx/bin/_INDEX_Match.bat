::@echo off
::cd %~dp0

net stop SphinxSearch
title "Match INDEX" 
echo  ===== Start Index ===== %date% %time% >> .\log_genIndex\src1_match_index.txt

indexer src1_match_index --config C:\sphinx\sphinx.conf >> .\log_genIndex\src1_match_index.txt

echo  ===== End Index ===== %date% %time% >> .\log_genIndex\src1_match_index.txt

net start SphinxSearch


timeout /t 90
cd ../../../

echo Run Matcher

cd script
Match_All_2.bat