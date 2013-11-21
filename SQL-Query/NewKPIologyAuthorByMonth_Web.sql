select 
    count(1) as 'post_count',
    subject_name,
	( if (type = 'post' or type = 'comment',
			'website',
			if(type = 'fb_post' or type = 'fb_comment',
				'facebook', 
				'twitter')
		)) as channel,
	type,
    month(post_date) as 'month',
    year(post_date) as 'year',
    website_name,
	author,
    (if(mood <= - 20,
        '-1',
        if(mood > 20, '1', '0'))) as sentiment
from
    website_c44
where
    post_date >= date('2013-07-01')
	and post_date <= date('2013-08-31')
group by subject_name , channel, type, month , year , website_name , author, sentiment
order by year asc , month asc, channel, website_name asc, sentiment desc
