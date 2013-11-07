::@echo off
cd %~dp0

net stop SphinxSearch
title "Twitter INDEX" 
echo ===== Start Index ===== %date% %time% >> .\log_genIndex\src1_twitter_index.txt

indexer src1_twitter_index --config C:\sphinx\sphinx.conf >> .\log_genIndex\src1_twitter_index.txt

echo ===== End Index ===== %date% %time% >> .\log_genIndex\src1_twitter_index.txt
net start SphinxSearch

