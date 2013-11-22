SELECT concat(round(sum(data_length)/(1024*1024*1024),2),'G')as data_length
,concat(round(sum(index_length)/(1024*1024*1024),2),'G')as index_length
,concat(round(sum(data_free)/(1024*1024*1024),2),'G')as data_free
FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA ='spider' and table_name='post'