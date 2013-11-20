#!/bin/bash

#echo "Bash version ${BASH_VERSION}..."
cd /home/tools/projects/kpiology_demo

array=(11 19 31 33 34 35 37 39 40 41 42 45 46 47 48 50 51)
for i in "${array[@]}"
do
    
    echo '[======= Start Gen Excel Client :' $i '=======]'$'\r'
    date '+DATE: %m/%d/%y TIME:%H:%M:%S'$'\r'
    
    php index.php data client $i

    echo '[Merge File Client :' $i']'$'\r'
    cat data/c$i/data/data_* > data/c$i/data.csv    
    
    date '+DATE: %m/%d/%y TIME:%H:%M:%S'$'\r'
    echo '[===========================================]'$'\r'
    
    sleep 2
done