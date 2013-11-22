<?php
error_reporting(0);

//define('_DB_SERVER', '54.251.101.254');
//define('_DB_SERVER', '203.151.21.111');
//define('_DB_SERVER', '203.150.231.155');
define('_DB_SERVER', '27.254.81.15');
define('_DB_NAME', 'spider');
define('_DB_USER', 'root');
define('_DB_PASSWD', 'Cg3qkJsV');

define('BOT_USER', 'thothmedia');
define('BOT_PASS', 'i5Uu70AnS8DD');

ini_set('memory_limit', '96M');
require_once('Thirehose.php');
require_once("c_query.php");

class MyStream extends Thirehose
{
	public function enqueueStatus($status) {
		
		$db = new c_db(_DB_SERVER,_DB_NAME,_DB_USER,_DB_PASSWD);
		$db->db_connect();
		
		//print_r(json_decode($status, true));
		$twitter_result =json_decode($status, true);
		
		
		if(is_array($twitter_result)){

				$post_d1 		= $twitter_result["created_at"];
	
				//$post_date = date("Y-m-d H:i:s",strtotime("+7 hours",strtotime($post_d1)));
				$post_date = date("Y-m-d H:i:s",strtotime($post_d1));
				
				$parse_date_t 		= date("Y-m-d H:i:s");
				//$parse_date = date("Y-m-d H:i:s",strtotime("+7 hours",strtotime($parse_date_t)));
				$parse_date = $parse_date_t;
				
				//$type			= (!empty($twitter_result["retweeted_status"])) ? "retweet" : "tweet";				
				
				if(isset($twitter_result["retweeted_status"])){
				
					$type ="retweet";
					$retweeted = $twitter_result["retweeted_status"];
					
					$col =array('tweet_id','type','retweet_id','retweet_count','favorite_count');
					$val =array($twitter_result["id_str"],$type,$retweeted["id_str"],$retweeted["retweet_count"],$retweeted["favorite_count"]);
											
					$db->set_insert('retweeted',$col,$val);
					
				}
				else if($twitter_result["in_reply_to_status_id_str"] !=""){
				
					$type ="reply";
					$col =array('tweet_id','type','reply_id');
					$val =array($twitter_result["id_str"],$type,$twitter_result["in_reply_to_status_id_str"]);
											
					$db->set_insert('retweeted',$col,$val);
					
					$type ="tweet";			//can not add type 'reply' to Table Post
				}
				else{
					$type ="tweet";
				}
				
				$body			= $twitter_result["text"];
				$tweet_id		= $twitter_result["id_str"];				
				$user_name		= $twitter_result["user"]["screen_name"];
				$follower 		= $twitter_result["user"]["followers_count"];

				//Debug - Print tweetid and username
				//echo " (tweet_id)".$tweet_id." ".$user_name."\n";		
			
				//insert post
				$db->set_insert('post_twitter_temp',array('post_date','parse_date','page_id','type','author_id','username','follower','body','tweet_id'),
						array($post_date,$parse_date,0,$type,0,$user_name,$follower,$body,$tweet_id));
											
		
		}
	}
}

// METHOD_FIREHOSE requires authentication
//$stream = new MyStream(BOT_USER, BOT_PASS, Thirehose::METHOD_SAMPLE);
$stream = new MyStream(BOT_USER, BOT_PASS, Thirehose::METHOD_FIREHOSE);
$stream->consume();
