@echo off
cd %~dp0

net stop SphinxSearch
title "Custom INDEX" 
echo ===== Start Index ===== %date% %time% >> .\log_genIndex\src1_custom_index.txt

::indexer src1_custom_index --config C:\sphinx\sphinx.conf --rotate >> .\log_genIndex\src1_custom_index.txt
indexer src1_custom_index --config C:\sphinx\sphinx.conf >> .\log_genIndex\src1_custom_index.txt

echo ===== End Index ===== %date% %time% >> .\log_genIndex\src1_custom_index.txt
net start SphinxSearch

