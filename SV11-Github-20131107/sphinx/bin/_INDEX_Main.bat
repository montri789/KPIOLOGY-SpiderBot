@echo off
cd %~dp0
net stop SphinxSearch
title "MAIN INDEX" 
echo %date% %time% >> .\log_genIndex\src1_main_index.txt
indexer src1_main_index --config C:\sphinx\sphinx.conf >> .\log_genIndex\src1_main_index.txt
net start SphinxSearch



