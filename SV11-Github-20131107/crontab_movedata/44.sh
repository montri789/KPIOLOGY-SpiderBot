#!/bin/bash

echo '[======= Start =======]'$'\r'
date '+DATE: %m/%d/%y TIME:%H:%M:%S'$'\r'

cd /home/tools/projects/kpiology_demo 

echo 'IBM 44'$'\r'
php index.php data client_subject 44
echo 'Gen File success'$'\r'
cat data/c44/data/data_* > data/c44/data.csv
echo 'Merge File Success'$'\r' 

date '+DATE: %m/%d/%y TIME:%H:%M:%S'$'\r'
echo '[======================]'$'\r'
