<?php
error_reporting(0);

define('_DB_SERVER', '54.251.101.254');
define('_DB_NAME', 'spider');
define('_DB_USER', 'root');
define('_DB_PASSWD', 'thtoolsth!');

define('BOT_USER', 'thothmedia');
define('BOT_PASS', 'i5Uu70AnS8DD');

ini_set('memory_limit', '96M');
#ini_set('memory_limit', '1024M');
require_once('Thirehose.php');
require_once("c_query.php");

class MyStream extends Thirehose
{
	public function enqueueStatus($status) {
		
		$db = new c_db(_DB_SERVER,_DB_NAME,_DB_USER,_DB_PASSWD);
		$db->db_connect();
		
		//print_r(json_decode($status, true));
		$twitter_result =json_decode($status, true);
		
		//if(isset($twitter_result) && is_array($twitter_result)){
		if(is_array($twitter_result)){

				$post_d1 		= $twitter_result["created_at"];
	
				$post_date = date("Y-m-d H:i:s",strtotime("+7 hours",strtotime($post_d1)));

				$parse_date_t 		= date("Y-m-d H:i:s");
				$parse_date = date("Y-m-d H:i:s",strtotime("+7 hours",strtotime($parse_date_t)));
				
				$type			= (!empty($twitter_result["retweeted"]))? "retweet" : "tweet";
				$body			= $twitter_result["text"];
				$tweet_id		= $twitter_result["id_str"];
				$user_name		= $twitter_result["user"]["screen_name"];
				$follower 		= $twitter_result["user"]["followers_count"];

				echo " (tweet_id)".$tweet_id." ".$user_name."\n";		
			
				//insert post
				$db->set_insert('post_twitter_temp',
									array('post_date','parse_date','page_id','type','author_id','username','follower','body','tweet_id'),
									array($post_date,$parse_date,0,$type,0,$user_name,$follower,$body,$tweet_id));
		}
	}
}

// METHOD_FIREHOSE requires authentication
//$stream = new MyStream(BOT_USER, BOT_PASS, Thirehose::METHOD_SAMPLE);
$stream = new MyStream(BOT_USER, BOT_PASS, Thirehose::METHOD_FIREHOSE);
$stream->consume();
