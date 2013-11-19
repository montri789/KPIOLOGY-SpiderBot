::@echo off
::cd %~dp0

net stop SphinxClassify
title "ReIndex Classify" 
echo  ===== Start Index ===== %date% %time% >> .\log_genIndex\src1_classify_index.txt

indexer src1_classify_index --config C:\sphinx_classify\sphinx_classify.conf >> .\log_genIndex\src1_classify_index.txt

echo  ===== End Index ===== %date% %time% >> .\log_genIndex\src1_classify_index.txt

net start SphinxClassify

::pause
timeout /t 60
cd ../../../

echo Run Matcher Classify

cd script
Match_Classify.bat