#!/bin/bash

echo '[======= Start =======]'$'\r'
date '+DATE: %m/%d/%y TIME:%H:%M:%S'$'\r'

cd /home/tools/projects/kpiology_demo 

echo 'Samsung Mobile 27'$'\r'
php index.php data client_subject 27
echo 'Gen File success'$'\r'
cat data/c27/data/data_* > data/c27/data.csv
echo 'Merge File Success'$'\r' 


date '+DATE: %m/%d/%y TIME:%H:%M:%S'$'\r'
echo '[======================]'$'\r'

