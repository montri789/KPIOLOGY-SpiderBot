<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
date_default_timezone_set('Asia/Bangkok');
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '128M');

class ConvertAuthor extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
		

	public function tweet2post($tweet)
	{
		$this->load->model("post_model");
		$post = new Post_model();
		$post->init();
		
		$post->post_date = $tweet->post_date;
		$post->parse_date = $tweet->parse_date;
		$post->page_id = $tweet->page_id;
		$post->type = $tweet->type;
		$post->author_id = $tweet->author_id;
		$post->title = $tweet->title;
		$post->body = $tweet->body;
		$post->sale = 0;
		$post->spam = 0;
		$post->segmented = $tweet->segmented;
		$post->is_segmented = $tweet->is_segmented;
		$post->tweet_id = $tweet->tweet_id;
		$post->reach_calculation_state = $tweet->reach_calculation_state;
		$post->facebook_id = $tweet->facebook_id;
    	
    	return $post;
	}
	
	public function move_all_tweets()
	{
		$posts = array();
		
		$start_time = microtime(true);
		$table_row_count = $this->db->count_all('post_twitter_temp');
				//count if < 2000 sleep 30 sec
		while ($table_row_count < 2000) {
			//True
		$table_row_count = $this->db->count_all('post_twitter_temp');
			echo 'Not enough tweet records: Please wait 30 Sec...'."\n";
			sleep(30);
		}
		$end_time = microtime(true);
		$time = $end_time - $start_time;
		//echo "count tweet time (s) : $time\n";
		
		echo 'Tweet messages wait to lookup author id = '.$table_row_count." records\n";
		
		//echo 'Start timer'."\n";
		$start_time = microtime(true);
		
		$offset = 0;
		$load = 100;
		$size = 0;
		$max = 2000;


		// load every 100 tweets
		//echo "s=$size,l=$load,o=$offset\n";
		while(($offset==0) || ($size==$load) && ($offset < $max))
		{
    		$start_time1 = microtime(true);
		//	echo "\n query ";
    		//$query = $this->db->get_where('post_twitter_temp',array('author_id'=>0),$load,$offset);
			$query = $this->db->get('post_twitter_temp',$load,$offset);
    		$tweets = $query->result();
    		$size = count($tweets);
    		$offset+= $size;
    		//echo "s=$size,o=$offset\n";
    		$end_time1 = microtime(true);
			$time1 = $end_time1 - $start_time1;
			//echo "Get tweet message(s) : $time1\n";
			
			
    		foreach($tweets as $t)
    		{
				$pass = 0;
				//Check tweet lenght > 2 (ex. TOT, True) & false charactor
				if(strlen($t->body) >2){
				//Check Thai or Eng /digit charactor
				if(preg_match('/[\x{0E00}-\x{0E7F}]/u', $t->body) || preg_match('/[A-Za-z0-9_~\-!@#\$%\^&\*\(\)]/', $t->body)){				

					//Check follower number > 0
						if($t->follower >= 0){
						$p = $this->tweet2post($t);
						// check username to author_id
						if($p->author_id == 0) $p->author_id = $p->get_author_id($t->username,$t->follower);
						$posts[] = $p;
						$tweet_id[] = $t->id;
						//echo '.'.'('.$t->username.') ';
						//echo '.'.'('.$t->id.') ';
						//echo '.';
						$pass = 1;
						}
					}
				}
				 if($pass < 1){
			
				 	echo "D";
                                       $this->db->where('id', $t->id);
                                       $this->db->delete('post_twitter_temp');
                                }
    		}
		}
		echo "\nInsert to Post Table:";
		
		$res = $this->db->insert_batch_ignore('post',$posts);
		
		if($res) {
		echo 'OK'."\n";
		//Delete post_twitter_temp
		echo "Delete Twitter Temp Table:";

		$res3 = $this->db->where_in( id, $tweet_id );
		$res2 = $this->db->delete('post_twitter_temp');

		//$res2 = $this->db->delete('post_twitter_temp', array('id' => $id)); 
		//	$res2 = $this->db->update('post_twitter_temp', $data2, array('id' => $tweet_id));
			if($res2) echo 'OK'."\n";
			else	echo 'FAILED'."\n";
			
			//$dateTime = date("Y-m-d H:i:s", strtotime("+7 hours"));
			echo $dateTime = date("Y-m-d H:i:s");
			
			$table_row_count = $this->db->count_all('post_twitter_temp');
			//$table_row_count = 888888;
			//$this->db->where('bot_name', "author_id_lookup");
			$this->db->update('bot_status',$data = array('last_running' => $dateTime, 'tweetleft' =>$table_row_count),"id = 1"); 
			
			//$res5 = $this->db->update('bot_status', 'now()', array('bot_name' => 'author_id_lookup'));
			
			
		}else{
		echo 'FAILED'."\n";
		//echo "---> ".$posts;
		}

		
		$end_time = microtime(true);
		$time = $end_time - $start_time;
		echo "exe time (s) : $time\n";
	

	}
	
}
?>
