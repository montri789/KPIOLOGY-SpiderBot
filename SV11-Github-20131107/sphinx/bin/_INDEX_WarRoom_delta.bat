@echo off
cd %~dp0
title "WarRoom DELTA INDEX" 
echo ===== Start Index ===== %date% %time% >> .\log_genIndex\src1_warroom_delta_index.txt
indexer src1_warroom_delta_index --config C:\sphinx\sphinx.conf --rotate >> .\log_genIndex\src1_warroom_delta_index.txt

echo ===== End Index ===== %date% %time% >> .\log_genIndex\src1_warroom_delta_index.txt

