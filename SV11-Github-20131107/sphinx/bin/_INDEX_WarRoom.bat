@echo off
cd %~dp0

title "WarRoom INDEX" 
echo  ===== Start Index ===== %date% %time% >> .\log_genIndex\src1_warroom_index.txt
indexer src1_warroom_index --config C:\sphinx\sphinx.conf --rotate >> .\log_genIndex\src1_warroom_index.txt
echo  ===== End Index ===== %date% %time% >> .\log_genIndex\src1_warroom_index.txt



