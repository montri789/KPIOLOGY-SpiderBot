::@echo off
cd %~dp0

net stop SphinxSearch
title "Facebook INDEX" 
echo ===== Start Index ===== %date% %time% >> .\log_genIndex\src1_facebook_index.txt

indexer src1_facebook_index --config C:\sphinx\sphinx.conf >> .\log_genIndex\src1_facebook_index.txt

echo ===== End Index ===== %date% %time% >> .\log_genIndex\src1_facebook_index.txt
net start SphinxSearch

