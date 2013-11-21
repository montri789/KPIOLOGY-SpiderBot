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
	date(post_date) as post_date,
    dayname(post_date) as day_of_week,
    day(post_date) as 'date',
    month(post_date) as 'month',
    year(post_date) as 'year',
    website_name,
    (if(mood <= - 20,
        '-1',
        if(mood > 20, '1', '0'))) as sentiment
from
    website_c44
where
    post_date >= date('2013-07-01')
	and post_date <= date('2013-08-31')
group by subject_id , day_of_week , channel, type, date , month , year , website_name , sentiment
union
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
    date(post_date) as post_date,
	dayname(post_date) as day_of_week,
    day(post_date) as 'date',
    month(post_date) as 'month',
    year(post_date) as 'year',
    'facebook' as website_name,
    (if(mood <= - 20,
        '-1',
        if(mood > 20, '1', '0'))) as sentiment
from
    facebook_c44
where
    post_date >= date('2013-07-01')
	and post_date <= date('2013-08-31')
group by subject_id , day_of_week , channel, type, date , month , year , website_name , sentiment
union
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
    date(post_date) as post_date,
	dayname(post_date) as day_of_week,
    day(post_date) as 'date',
    month(post_date) as 'month',
    year(post_date) as 'year',
    'twitter' as website_name,
    (if(mood <= - 20,
        '-1',
        if(mood > 20, '1', '0'))) as sentiment
from
    twitter_c44
where
    post_date >= date('2013-07-01')
	and post_date <= date('2013-08-31')
group by subject_id , day_of_week , channel, type, date , month , year , website_name , sentiment
order by subject_name, year asc , month asc, date asc, channel, website_name asc, sentiment desc
