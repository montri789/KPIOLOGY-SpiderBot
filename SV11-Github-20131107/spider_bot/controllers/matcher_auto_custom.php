<?PHP
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Matcher_auto_custom extends CI_Controller{	

	public function bot($type='queue',$sphinx_port=9313,$client_id=null)
	{
		/*
		$IP_Port ='203.151.21.111.'.$sphinx_port;
		$query_index = $this->db->get_where('sphinx_index',array('name'=>$IP_Port));				
		
		$match_port  =$sphinx_port;
		$match_index =$query_index->row()->sphinx_index;
		$match_to_id =$query_index->row()->match_1;
		*/
		$match_port  =9313;
		$match_index ='src1_facebook_index';
		$match_to_id =438600000;					//Before Match update Match_id=0 & set $match_to_id
		
		$bot_id = rand(0,10000);
		echo 'bot id:'.$bot_id.PHP_EOL;
		
		$bot_query = $this->db->get_where('subject',array('bot_id'=>$bot_id));
		$found_bot = $bot_query->num_rows();
		echo 'found same bot:'.$found_bot.PHP_EOL;
				
		$option = array(
			'query IS NOT NULL'=> null, 
			'matching_status' => $type,
			'client_id' => $client_id,
			'match_id <  '=>$match_to_id,			
			'bot_id' => 0             
		);
			
		
		$query = $this->db->get_where('subject',$option,1,0);
		$available_subjects = $query->num_rows();
		echo 'Available Subject : '.$available_subjects.PHP_EOL;
		$err = false;
		
		while(!$err && $available_subjects > 0)
		{			
			/*	if( $query->row()->to >= $to_date){
					echo "Input Date < Last Update (Skip)";

					$query = $this->db->get_where('subject',$option,1,0);
					$available_subjects = $query->num_rows();

					continue;
				}
			*/
			
			$subject_id = $query->row()->id;    
			echo "Found Subjects ID:".$subject_id.PHP_EOL;

			$matching_failed = true;
			$subject_taken = false;

			// select subject, change bot_id
			$subject = new Subject_model();
			$subject->init($subject_id);
			if($subject->bot_id != 0)
			{
				echo 'FAILED, Subject Taken by other bot :'.$subject->bot_id.PHP_EOL;
				$subject_taken = true;
			}
			else
			{
				$subject->bot_id = $bot_id;
				$subject->update();
			}
			
			if(!$subject_taken)
			{
				$subject->init($subject_id);
				if($subject->bot_id != $bot_id) { echo 'FAILED, Subject Taken by other bot :'.$subject->bot_id.PHP_EOL; continue; }
				
				echo 'subject name: '.$subject->subject.PHP_EOL;
				
				// update status = matching, latest_matching, from, to
				$from_date = $subject->to;


				if($type == 'queue') $clean = true;
				if($type == 'update') $clean = false;
				
				//call Function run
				$res = $this->run($subject_id,$clean,0,$match_port,$match_index,$match_to_id);
				
				if(!$res)
				{
					echo '(-err)';
					$subject->init($subject_id);
					$subject->bot_id = -1;
					$subject->update();
				}
			}
			
			$query = $this->db->get_where('subject',$option,1,0);
			$available_subjects = $query->num_rows();
			echo 'Available Subject : '.$available_subjects.PHP_EOL;
		}
	}
		
	public function run_all($type='queue',$client_id=null,$from=null,$to=null)
	{
		$from = '2012-06-01';
		$to = '2012-07-01';
		
		$option = array(
			'query IS NOT NULL'=> null, 
			'matching_status' => $type,
			'client_id' => $client_id,
			'from !=' => $from,
			'to !=' => $to
		);
		
		$query = $this->db->get_where('subject',$option);
		
		echo "Found Subjects:".$query->num_rows().PHP_EOL;
		
		if($type == 'queue') $clean = true;
		else $clean = false;
		
		foreach($query->result() as $row)
		{
			// Reset PHP Timeout to 5min
			set_time_limit(5*60);
			
			$this->run($row->id,$clean,0,$from,$to);
		}
	}
	
	public function fix_subject(){
		$array = array(122,123,124,125,126,127,128,129,131,132,135,136,137,138,140,143,146,150,151,152,153,154,155,156,157,158,159,160,161,162,163,
			178,179,180,181,182,183,184);
		foreach($array as $val){
		    $this->fix($val);
			set_time_limit(60);
		}
	}
	public function fix($subject_id,$from = NULL,$to = NULL)
	{       //Test Subject 594 Client 10
		$clean = false;

		if($from == NULL)
		$from = '2012-10-31';
		
		if($to == NULL)
		$to = '2012-12-02';

		
		$this->run($subject_id,$clean,0,$from,$to);
	}
	public function run($subject_id,$clean=true,$query_offset=0,$match_port,$match_index,$match_to_id)
	{
		$this->load->helper('sphinxapi');
		$this->load->helper('mood');

		// skip if matching_status is "matching"
		$subject_data = $this->custom_model->get_multi_value('subject','client_id,query,matching_status',$subject_id);
        $matching_status = $subject_data->matching_status;
		if($matching_status == 'matching')
		{
			echo "subject is matching";
			return false;
		}
		
		// flag subject as matching.. do other bot runs this queue.
		$this->db->update('subject',array('matching_status'=>'matching'),array('id'=>$subject_id));
		
		echo 'match_port:'.$match_port .PHP_EOL;
		echo 'match_index:'.$match_index .PHP_EOL;
		echo 'match_to_id:'.$match_to_id .PHP_EOL; //exit;
		
		
		$config['hostname'] = "localhost";
		$config['username'] = "root";
		$config['password'] = "thtoolsth!";
		
		$config['database'] = "kpiology";
		$config['dbdriver'] = "mysql";
		$config['dbprefix'] = "";
		$config['pconnect'] = FALSE;
		$config['db_debug'] = TRUE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = "";
		$config['char_set'] = "utf8";
		$config['dbcollat'] = "utf8_general_ci";
		
		echo "\nLoad Database\n";
		$thothconnect_db = $this->load->database($config,true);

		//$query = $this->db->query("SELECT client_id FROM subject WHERE id = ".$subject_id);
		//$row = $query->row();
		$client_id = $subject_data->client_id;
		
		
		   $date_repeat = "";
		   
		   $sql = "SELECT max(date) as 'date' FROM match_repeat WHERE client_id = $client_id ";
		   $query = $this->db->query($sql);
		   if($query->num_rows() > 0){
			 $res = $query->row();
			 $date_repeat = date("Y-m",strtotime($res->date));
		   }else{
			 $date_repeat = date("Y-m",strtotime("-1 month",strtotime(date("Y-m"))));
		   }
		   
		/*   		   
		echo "Clean Data\n";
		if($clean){
			echo 'Cleaning data : ';
			$this->db->delete('matchs',array('subject_id'=>$subject_id));
			echo ' spider.mtach';
			$thothconnect_db->delete('website_c'.$client_id,array('subject_id'=>$subject_id));
			echo ',kpiology.website_c'.$client_id;
			$thothconnect_db->delete('twitter_c'.$client_id,array('subject_id'=>$subject_id));
			echo ',kpiology.twitter_c'.$client_id;
			$thothconnect_db->delete('facebook_c'.$client_id,array('subject_id'=>$subject_id));
			echo ',kpiology.facebook_c'.$client_id.PHP_EOL;
		}*/
		
		// get search string from subject_id
		$query = $subject_data->query;
		//$this->custom_model->get_value('subject','query',$subject_id);
		
		//get exclude keyword from exclude_keywords table Test for client 7,21
		$ex_query = $this->db->query("SELECT ex_word FROM exclude_keywords WHERE client_id = ".$client_id);
		echo "Build query\n";
		$build_ex_query = " ";
		foreach($ex_query->result() as $row){

				$build_ex_query .= "-\"";
				$build_ex_query .= $row->ex_word;
				$build_ex_query .= "\" ";
		}

		$query = $query.$build_ex_query;

		// sphinx init		
		$cl = new SphinxClient ();
		$q = $query;
		$sql = "";
		$mode = SPH_MATCH_EXTENDED;
		
		#$host = "localhost";
		$host = "203.151.21.111";
		
		//$port = 9312;
		//$port = 9313;
		$port = (int)$match_port;
		
		//$index = "*";
		//$index = "src1_custom_index";
		//$index = "src1_match_index";
		//$index = "src1_facebook_index";
		//$index = "src1_web_index";
		$index = $match_index;
		
		$groupby = "";
		$groupsort = "@group desc";
		$filter = "group_id";
		$filtervals = array();
		$distinct = "";
		$sortby = "@id ASC";
		$sortexpr = "";
		$offset = $query_offset;
		$limit = 100000;
		$ranker = SPH_RANK_PROXIMITY_BM25;
		$select = "";
		
		echo 'limit='.$limit.' offset='.$offset.PHP_EOL;
		
		//Extract subject keyword from search string
		$keywords = get_keywords($q);
		echo "get_keywords\n";
		
		////////////////////////////////////////////
		// do query
		////////////////////////////////////////////
		
		$cl->SetServer ( $host, $port );
		$cl->SetConnectTimeout ( 15 );
		$cl->SetArrayResult ( true );
		$cl->SetWeights ( array ( 100, 1 ) );
		$cl->SetMatchMode ( $mode );
		// if ( count($filtervals) )	$cl->SetFilter ( $filter, $filtervals );
		// if ( $groupby )				$cl->SetGroupBy ( $groupby, SPH_GROUPBY_ATTR, $groupsort );
		if ( $sortby )				$cl->SetSortMode ( SPH_SORT_EXTENDED, $sortby );
		// if ( $sortexpr )			$cl->SetSortMode ( SPH_SORT_EXPR, $sortexpr );
		if ( $distinct )			$cl->SetGroupDistinct ( $distinct );
		if ( $select )				$cl->SetSelect ( $select );
		if ( $limit )				$cl->SetLimits ( 0, $limit, ( $limit>100000) ? $limit : 100000 );
		
		/*	
        $from = @date('Y-m-d ',strtotime("+1 day",strtotime($from)));
        echo "FROM: ".$from." TO: ".$to."\n";
        $cl->SetFilterRange('post_date',strtotime($from.' 00:00:00'),strtotime($to.' 23:59:59'));
		*/

		$cl->SetRankingMode ( $ranker );

		//debug
		$time_start = microtime(true);

		echo "Starting Query Index...\n";
		$res = $cl->Query ( $q, $index );

		$time_end = microtime(true);
        $usetime = $time_end - $time_start;

		echo "Did query in ".$usetime."seconds\n";

		echo "Query Indexing\n";
		//$res = true;
		/////////////////////////////////////////////////
		// do Insert to DB
		/////////////////////////////////////////////////
		
		// Current matching
		//$current_matching = array();
		/*$query_matchs = $this->db->get_where('matchs',array('subject_id'=>$subject_id));
		if($query_matchs->num_rows() > 0)
		{
			echo PHP_EOL.'currents matching :'.$query_matchs->num_rows();
			foreach($query_matchs->result() as $match)
			{
				$current_matching[] = $match->post_id;
			}
		}*/		
		
		$insert_website = 0;
		$insert_twitter = 0;
		$insert_facebook = 0;
		$list_page_id = array();
		$insert_website_post_comment = 0;
		$insert_matchs = 0;
		
		$res_insert = array();
		
		// Search and Update
		if ( $res===false )  
		{
			echo "Query failed: " . $cl->GetLastError() . ".\n";
			return $res;
		}
		else
		{
			if ( $cl->GetLastWarning() ) echo "WARNING: " . $cl->GetLastWarning() . "\n\n";
			echo "Query '$q' \nretrieved $res[total] of $res[total_found] matches in $res[time] sec.\n";
			
			//2013-02-28 =================
			//$thothconnect_db->reconnect();				
			$data = array();
			
			$data["client_id"] 			= $client_id;
			$data["subject_id"] 		= $subject_id;
			$data["is_matching"] 		= 'Y';
			$data["start_datetime"] 	= date("Y-m-d H:i:s");
			$data["match_from"] 		= date("Y-m-d");
			$data["match_to"] 			= date("Y-m-d");
			$data["match_all"] 			= $res["total"];
			$data["match_insert"] 		= 0;
			$data["last_post_id"] 		= 0;
			$data["wpc_all"] 			= 0;
			$data["wpc_insert"] 		= 0;
			$data["wpc_last_post_id"] 	= 0;
			
			$insert_query = $this->db->insert_string("status_match",$data);
			$this->db->query($insert_query);
			$match_id =$this->db->insert_id();
			
			//echo "match_id=".$match_id."\n";
			//2013-02-28 ================= end
			
			if($res['total'] == 0) echo "no result<br/>\n";
			else if($res['total'] > $limit+$offset) $this->run($subject_id,$clean,$limit+$offset,$match_port,$match_index,$match_to_id);
			else
			{
				
				echo "Updating....";
				
				$count_all =0;
				foreach ( $res["matches"] as $k=>$docinfo )
				{
//					echo '('.$k.')'.$docinfo["id"]." ";
					// Reset PHP Timeout to 1min
					
					// if found in $current_matching then skip
					//if(in_array($docinfo["id"],$current_matching))
					//{
					//	continue;
					//}
					//else
					//{
						
						// else insert new match
						set_time_limit(0);

						$post = new Post_model();
						$post->init($docinfo["id"]);
												
					$post_year_month =  date("Y-m",strtotime($post->post_date));					
					//if($post_year_month > $date_repeat){
							
						//if post_date is our of range then skip
						
						//$post_date = strtotime($post->post_date);	
						//if($post_date < $from || $post_date > $to) continue;

						$mood = get_mood($post->body,$keywords);

						//-----------------------------------------------------
												
						$subject = $post->get_subject($subject_id);
						//print_r($subject);
												
						//echo "=>1";												
						if($post->type == "post" || $post->type == "comment"){	
						
							$postData = $post->get_post_website($post->id);
														
							if($postData != null){
								
								$data = array();
								$data["post_id"] = $postData->post_id;
								$data["post_date"] = $postData->post_date;
								$data["title"] = $postData->title;
								$data["body"] = $postData->body;
								$data["type"] = $postData->type;
								$data["author_id"] = $postData->author_id;
								$data["author"] = $postData->author;
								$data["website_id"] = $postData->website_id;
								$data["website_name"] = $postData->website_name;
								$data["website_cate_id"] = $postData->website_cate_id;
								$data["website_cate"] = $postData->website_cate;
								$data["website_type_id"] = $postData->website_type_id;
								$data["website_type"] = $postData->website_type;
								$data["group_id"] = $subject->group_id;
								$data["group"] = $subject->group;
								$data["url"] = substr($postData->root_url,0,-1)."".$postData->url;
								$data["page_id"] = $postData->page_id;
								$data["subject_id"] = $subject->subject_id;
								$data["subject_name"] = $subject->subject_name;
								$data["mood"] = $mood;
								$data["mood_by"] = 'system';
								$data["system_correct"] = $mood;
								$data["system_correct_date"] = mdate('%Y-%m-%d %H:%i',time());
									
								$insert_website++;
								$list_page_id[] = $postData->page_id;
								echo 'w';
								$thothconnect_db->reconnect();
								//$res_insert[] = $thothconnect_db->insert("website_c".$subject->client_id,$data);
																
								$insert_query = $thothconnect_db->insert_string("website_c".$subject->client_id,$data);
								$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
								$thothconnect_db->query($insert_query);

								//$post->insert_post_comment($postData->page_id,$subject->client_id,$thothconnect_db);
							}
							
						}else if($post->type == "tweet" || $post->type == "retweet"){
							$postData = $post->get_post_twitter($post->id);
							if($postData != null){
								$data = array();
								$data["post_id"] = $postData->post_id;
								$data["post_date"] = $postData->post_date;
								$data["body"] = $postData->body;
								$data["type"] = $postData->type;
								$data["author_id"] = $postData->author_id;
								$data["author"] = $postData->author;
								$data["group_id"] = $subject->group_id;
								$data["group"] = $subject->group;
								$data["tweet_id"] = $postData->tweet_id;
								$data["subject_id"] = $subject->subject_id;
								$data["subject_name"] = $subject->subject_name;
								$data["mood"] = $mood;
								$data["mood_by"] = 'system';
								$data["system_correct"] = $mood;
								$data["system_correct_date"] = mdate('%Y-%m-%d %H:%i',time());
								
								$insert_twitter++;
								echo 't';
								$thothconnect_db->reconnect();
								//$res_insert[] = $thothconnect_db->insert("twitter_c".$subject->client_id,$data);
								
								$insert_query = $thothconnect_db->insert_string("twitter_c".$subject->client_id,$data);
								$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
								$thothconnect_db->query($insert_query);
							}
								
						}else if($post->type == "fb_post" || $post->type == "fb_comment"){
						//echo "=>2";	
							$postData = $post->get_post_facebook($post->id);
							if($postData != null){
							
								$page_arr =explode('_',$postData->facebook_id,-1);
								$pageData = $post->get_page_facebook($page_arr[0]);
								//print_r($pageData); exit;
							
								$data = array();
								$data["post_id"] = $postData->post_id;
								$data["post_date"] = $postData->post_date;
								$data["body"] = $postData->body;
								$data["type"] = $postData->type;
								$data["author_id"] = $postData->author_id;
								$data["author"] = $postData->author;
								$data["group_id"] = $subject->group_id;
								$data["group"] = $subject->group;
								$data["facebook_page_id"] = $pageData->facebook_page_id;
								$data["facebook_page_name"] = $pageData->facebook_page_name;
								$data["subject_id"] = $subject->subject_id;
								$data["subject_name"] = $subject->subject_name;
								$data["facebook_id"] = $postData->facebook_id;
								//$data["parent_post_id"] = $postData->parent_post_id;								
								//$data["likes"] = $postData->likes;
								//$data["shares"] = $postData->shares;
								$data["parent_post_id"] = 0;
								$data["likes"] = 0;
								$data["shares"] = 0;
								$data["mood"] = $mood;
								$data["mood_by"] = 'system';
								$data["system_correct"] = $mood;
								$data["system_correct_date"] = mdate('%Y-%m-%d %H:%i',time());
						
								$insert_facebook++;
								echo 'f';
								$thothconnect_db->reconnect();
								
								//$res_insert[] = $thothconnect_db->insert("facebook_c".$subject->client_id,$data);
								
								$insert_query = $thothconnect_db->insert_string("facebook_c".$subject->client_id,$data);
								$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
								//echo 'sql='.$insert_query; exit;
								$thothconnect_db->query($insert_query);
							}
						}															
						
						$data_matchs = array(
							'post_id'=> $post->id, 
							'subject_id' => $subject_id , 
							'matching_date' => null,
							'sentiment' => $mood,
							'by' => 'system',
							'system_correct' => $mood,
							'system_correct_date' => mdate('%Y-%m-%d %H:%i',time())
						);
						
						$insert_matchs++;
												
						//$this->db->insert('matchs',$data);
						$insert_str = $this->db->insert_string('matchs',$data_matchs);
						$insert_str = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_str);
						$res = $this->db->query($insert_str);
						//$res = $spider_db->query($insert_str);
						//---------------------------------------
						
						//2013-02-28 =================
						$count_all++;

						$data_l = array();
						$data_l["stop_datetime"] 	= date("Y-m-d H:i:s");
						$data_l["match_insert"] 	= $count_all;
						$data_l["last_post_id"] 	= $post->id;
						
						//$thothconnect_db->reconnect();
						$this->db->update('status_match',$data_l,array('id'=>$match_id));
						//2013-02-28 ================= end
						
						unset($post);
					
					//} //if($post_year_month > $date_repeat){					
				}
				
				echo 'total matchs :'.$insert_matchs.PHP_EOL;
				echo 'total website :'.$insert_website.PHP_EOL;
				echo 'total twitter :'.$insert_twitter.PHP_EOL;
				echo 'total facebook :'.$insert_facebook.PHP_EOL;
				echo 'total page_id :'.count($list_page_id).PHP_EOL;
				
				}
			}
		
		$check = true;
		
		/*	
		foreach($res_insert as $val){
			if($val == false){
				$check = false;
				continue;
			}
			
		}*/

		// flag subject as update..
		if($check == true){
			$data = array(
					'matching_status'=>'update',
					'latest_matching'=> mdate('%Y-%m-%d %H:%i:%s',time()),					
					'match_id'=> $match_to_id,
					'bot_id'=>0
					);					
					
			$this->db->update('subject',$data,array('id'=>$subject_id));
		}
		
		$data_l = array();
		$data_l["stop_datetime"] 	= date("Y-m-d H:i:s");
		$data_l["is_matching"] 	= ($check == true) ? 'N' : 'F';	//N=Complete,F=Fail
		
		//$thothconnect_db->reconnect();
		$this->db->update('status_match',$data_l,array('id'=>$match_id));
		
		return $check;	
	}
	
	public function update($subject_id=null)
	{
		$this->db->where('matching_status','update');
		if($subject_id != null) $this->db->where('id',$subject_id);
		$query = $this->db->get('subject');
		
		$debug = true;
		
		if($query->num_rows())
		{
			foreach($query->result() as $row)
			{
				log_message('info','Matcher : updating subject id :'.$row->id);
				
				// flag subject as matching.. do other bot runs this queue.
				$this->db->update('subject',array('matching_status'=>'matching'),array('id'=>$row->id));
				
				// prepare subject keywords
				$subject_inc = explode(",",$row->inclusive);
				if($row->exclusive != null) $subject_exc = explode(",",$row->exclusive);
				else $subject_exc = null;
				
				// query post with parse date is later than latest matching
				$this->db->order_by('id','desc');
				$posts = $this->db->get_where('post',array('parse_date  >'=>$row->latest_matching));
				if($debug) echo 'Matcher : total posts : '.$posts->num_rows();
				log_message('info','Matcher : total posts : '.$posts->num_rows());
				foreach($posts->result() as $p)
				{
					// Reset PHP Timeout to 1min
					set_time_limit(60);
					
					// is match inclusive keywords
					if($this->is_match($p->body,$subject_inc,$debug))
					{
						// if matched check exclusive, if found skip
						if($subject_exc != null) { if($this->is_match($p->body,$subject_exc,$debug)) continue; }
						
						// otherwise check sentiment
						$sentiment = $this->sentiment($p->body,$subject_inc);
						
						// store a record
						$match = new Match_model();
						$match->init();
						$match->post_id = $p->id;
						$match->subject_id = $row->id;
						$match->sentiment = $sentiment;
						$match->insert();
						log_message('info','Matcher : matched subject : '.$row->id.' with post :'.$p->id);
					}
				}
				
				// flag subject as update..
				$this->db->update('subject',array('matching_status'=>'update','latest_matching'=> mdate('%Y-%m-%d %H:%i:%s',time())),array('id'=>$row->id));
				
			}
		} else 
		{
			log_message('info','Matcher : No Subject in Queue');
		}

		unset($query);

		// Activate Gabage Collection
		gc_enable();
		$gc_cycles = gc_collect_cycles();
		log_message('info','MATCHER : GC : '.$gc_cycles);

		return true;
	}
	
	public function run_queue()
	{
		$options = array(
			'matching_status' => 'queue'
		);
		
		$query = $this->db->get_where('subject',$options);
		
		if($query->num_rows())
		{
			foreach($query->result() as $row)
			{
				log_message('info','Matcher : running Queue subject id :'.$row->id);
				
				// flag subject as matching.. do other bot runs this queue.
				$this->db->update('subject',array('matching_status'=>'matching'),array('id'=>$row->id));
				
				$subject_inc = explode(",",$row->inclusive);
				if($row->exclusive != null) $subject_exc = explode(",",$row->exclusive);
				else $subject_exc = null;
				
				// clear all match record for this subject
				$this->db->delete('matchs',array('subject_id'=>$row->id));
				
				// begin re-matching this subject
				$this->db->order_by('id','desc');
				$posts = $this->db->get('post');
				log_message('info','Matcher : total posts : '.$posts->num_rows());
				foreach($posts->result() as $p)
				{
					// Reset PHP Timeout to 1min
					set_time_limit(60);
					
					// is match inclusive keywords
					if($this->is_match($p->body,$subject_inc))
					{
						// if matched check exclusive, if found skip
						if($subject_exc != null) { if($this->is_match($p->body,$subject_exc)) continue; }
						
						// otherwise check sentiment
						$sentiment = $this->sentiment($p->body,$subject_inc);
						
						// store a record
						$match = new Match_model();
						$match->init();
						$match->post_id = $p->id;
						$match->subject_id = $row->id;
						$match->sentiment = $sentiment;
						$match->insert();
						log_message('info','Matcher : matched subject : '.$row->id.' with post :'.$p->id);
						
						unset($match);
						
					}
				}
				unset($posts);
				
				// flag subject as update..
				$this->db->update('subject',array('matching_status'=>'update','latest_matching'=> mdate('%Y-%m-%d %H:%i:%s',time())),array('id'=>$row->id));
				
			}
		} else 
		{
			log_message('info','Matcher : No Subject in Queue');
		}

		unset($query);
		
		// Activate Gabage Collection
		gc_enable();
		$gc_cycles = gc_collect_cycles();
		log_message('info','MATCHER : GC : '.$gc_cycles);

		return true;
	}

	function is_match($str=null,$keywords,$debug = false)
	{
		if($debug)
		{
			echo "<hr>matching : <br />";
			echo "Body:".$str;
			echo "<br />with :".var_dump($keywords);
		}
		
		if($str==null) return false;
		
		mb_internal_encoding('utf-8');
		foreach($keywords as $k)
		{
			$k_split = explode("+",$k);
			
			if(count($k_split) > 0)
			{
				$found = 0;
				
				foreach($k_split as $k)
				{
					$keyword_pos = mb_stripos($str,trim($k));
					if(is_int($keyword_pos)) $found += 1;
				}
				
				if($debug) echo "found :".$k;
				if($found == count($k_split)) return true;
			}
			else
			{
				$keyword_pos = mb_stripos($str,trim($k));
				if(is_int($keyword_pos))
				{
					if($debug) echo "found :".$k;
					return (int)$keyword_pos;
				}
			}
		}
		
		return false;
	}
	
	function sentiment($str=null,$keywords)
	{
		$debug = false;
		mb_internal_encoding('utf-8');
		$thres_len = 20;
		
		$total_sentiment_phrase = 0;
		$total_net_score = 0;
		
		$total_len = mb_strlen($str,'utf-8'); // Str length
		$str = preg_split('#[\s\n\r]+#', $str); // split by space
		//print_r($str);
		
		foreach($str as $s)
		{
			// if this phrase contains subject words, increase amp_score start weight to 2 otherwise is 0.5
			$amp_score = 0.5;
			
			foreach($keywords as $k)
			{
				$subj_pos = mb_stripos(trim($s),trim($k));
				if(is_int($subj_pos))
				{
					$amp_score++;
					if($debug)
					{
						echo "found at ".$subj_pos." subject:".$subj;
					}
					break;
				}
			}
			
			
			foreach($this->words->emo as $w)
			{
				$s_len = mb_strlen($s);
				
				$score = 0;
				$amp_len = 0;
				$neg_len = 0;
				
				// search for emo words
				$emo_pos = mb_stripos($s,$w->word);
				if(is_int($emo_pos))
				{
					$total_sentiment_phrase += $s_len;
					$score = $w->value;
					$emo_len = mb_strlen($w->word);
					
					if($debug)
					{
						echo "<hr/>context(".$s_len."):".$s."<br/>";
						echo "found[".$emo_pos."]:".$w->word."(".$score.")";
						echo "<br />";
					}
				}
				else continue; // if not found emo word skip whole chunk

				// look backward for neg
				foreach($this->words->neg as $n)
				{
					$neg_pos = mb_strrpos($s,$n->word);
					if(is_int($neg_pos))
					{
						$gap_pos = $emo_pos-$neg_pos;
						if($gap_pos > 0 && $gap_pos < $thres_len)
						{
							$score *= $n->value;
							$neg_len = mb_strlen($n->word);
							
							if($debug)
							{
								echo "found[".$gap_pos."]:".$n->word."(".$score.")";
								echo "<br />";
							}
						}
					}
				}

				// look backward for amp

				// look forward for amp
				foreach($this->words->amp as $a)
				{
					$amp_pos = mb_stripos($s,$a->word,$emo_pos);
					if(is_int($amp_pos))
					{
						$amp_score += $a->value;
						$amp_len += mb_strlen($a->word);
						
						if($debug)
						{
							echo "found[".$amp_pos."]:".$a->word."(".$amp_score.")";
							echo "<br />";
						}
					}
				}
				if($amp_score != 0) $score *= $amp_score;


				// look forward for ign
				foreach($this->words->ign as $i)
				{
					$ign_pos = mb_stripos($s,$i->word,$emo_pos);
					if(is_int($ign_pos))
					{
						$score = 0;

						if($debug)
						{
							echo "found[".$ign_pos."]:".$i->word;
							echo "<br />";
						}
						
						continue;
						
					}
				}
				
				if($debug)
				{
					// total emo value
					echo "Score:".$score;
					echo "<br />";
				}
				
				// check weight
				$sentiment_len = $emo_len + $neg_len + $amp_len;
				$weight = $sentiment_len/$s_len;
				if($debug)
				{
					echo "Weight:".$weight;
					echo "<br />";
				}
				
				// cal net emo value
				$net_score = $score*$weight*100;
				$total_net_score += $net_score;
				if($debug)
				{
					echo "Net:".$net_score;
					echo "<br />";
				}
				
				$score = 0;
			}
		}
		
		// sum all 
		$net_weight = $total_sentiment_phrase/$total_len;
		$sentiment = $total_net_score*$net_weight;

		if($debug)
		{
			echo "<hr />";
			echo "Total Sentiment Phrase:".$total_sentiment_phrase."<br />";
			echo "Total Len:".$total_len."<br />";
			echo "Weight:".$net_weight."<br />";
		}
		
		unset($str);
		return $sentiment;
	}
	
	public function index()
	{
		//$client_id = 1;
		$sql = "SELECT 	* 
				FROM 	subject  ";
				//WHERE 	client_id = $client_id ";
				
		$query = $this->db->query($sql);
		
		foreach($query->result() as $row){
			
			//----------------------------------------------------------
			$sql_match = "SELECT post_id FROM matchs WHERE subject_id = ".$row->id." ";
			
			$query_match = $this->db->query($sql_match);
			$post_id = "";
			if($query_match->num_rows() > 0){
				$post =  array();
				foreach($query_match->result() as $post_id_row){
					$post[] = $post_id_row->post_id;
				}
				$post_id = implode(",",$post);
			}
			//----------------------------------------------------------

			$in = $row->inclusive;
			$ex = $row->exclusive;	
			
			$in_c = explode(",",$in);
			$ex_c = explode(",",$ex);
			
			$in_sql = "";
			$ex_sql = "";
							
			foreach($in_c as $val){
				if(!empty($val)){
					if(strpos($val,"+")){
						$in_replace = str_replace("+","%' AND body LIKE '%",$val);
						$in_sql .= (!empty($in_sql) ? 'OR' : '' )." (body LIKE '%".$in_replace."%') ";
					}else{
						$in_sql .= (!empty($in_sql) ? 'OR' : '' )." body LIKE '%".$val."%' ";
					}
				}
			}	
			
			$ex_replace = str_replace(",","%' AND body NOT LIKE '%",$ex);
			$ex_sql =  " body NOT LIKE '%".$ex_replace."%' ";
			
			$sql =  "SELECT id,body 
					 FROM post 
					 WHERE (".$in_sql . ") ";
					 
			$sql .= (!empty($ex)) ? " AND (".$ex_sql.") " : "";
			$sql .= (!empty($post_id)) ? " AND id NOT IN(".$post_id.") " : "";		 
			
			
			$query2 = $this->db->query($sql);
			
			foreach($query2->result() as $row2){
				$data = array("post_id"=>$row2->id,
							  "subject_id"=>$row->id);
				$this->db->insert("matchs",$data);
			}	
		}
	}
	
	function eval_sentiment($str,$subject_id,$debug=0)
	{
		mb_internal_encoding('utf-8');
		$thres_len = 20;
		
		// get subject keywords
		$query = $this->db->get_where('subject',array('id'=>$subject_id));
		$subject_inc = $query->row()->inclusive;
		$subject_exc = $query->row()->exclusive;
		$subject_inc = explode(",",$subject_inc);
		$subject_exc = explode(",",$subject_exc);
		
		
		$total_sentiment_phrase = 0;
		$total_net_score = 0;
		
		$total_len = mb_strlen($str,'utf-8'); // Str length
		$str = preg_split('#[\s\n\r]+#', $str); // split by space
		//print_r($str);
		
		foreach($str as $s)
		{
			// if this phrase contains subject words, increase amp_score start weight to 2 otherwise is 0.5
			$amp_score = 0.5;
			
			foreach($subject_inc as $subj)
			{
				$subj_pos = mb_stripos(trim($s),trim($subj));
				if(is_int($subj_pos))
				{
					$amp_score++;
					if($debug)
					{
						echo "found at ".$subj_pos." subject:".$subj;
					}
					break;
				}
			}
			
			foreach($this->words->emo as $w)
			{
				$s_len = mb_strlen($s);
				
				$score = 0;
				$amp_len = 0;
				$neg_len = 0;
				
				// search for emo words
				$emo_pos = mb_stripos($s,$w->word);
				if(is_int($emo_pos))
				{
					$total_sentiment_phrase += $s_len;
					$score = $w->value;
					$emo_len = mb_strlen($w->word);
					
					if($debug)
					{
						echo "<hr/>context(".$s_len."):".$s."<br/>";
						echo "found[".$emo_pos."]:".$w->word."(".$score.")";
						echo "<br />";
					}
				}
				else continue; // if not found emo word skip whole chunk

				// look backward for neg
				foreach($this->words->neg as $n)
				{
					$neg_pos = mb_strrpos($s,$n->word);
					if(is_int($neg_pos))
					{
						$gap_pos = $emo_pos-$neg_pos;
						if($gap_pos > 0 && $gap_pos < $thres_len)
						{
							$score *= $n->value;
							$neg_len = mb_strlen($n->word);
							
							if($debug)
							{
								echo "found[".$gap_pos."]:".$n->word."(".$score.")";
								echo "<br />";
							}
						}
					}
				}

				// look backward for amp

				// look forward for amp
				foreach($this->words->amp as $a)
				{
					$amp_pos = mb_stripos($s,$a->word,$emo_pos);
					if(is_int($amp_pos))
					{
						$amp_score += $a->value;
						$amp_len += mb_strlen($a->word);
						
						if($debug)
						{
							echo "found[".$amp_pos."]:".$a->word."(".$amp_score.")";
							echo "<br />";
						}
					}
				}
				if($amp_score != 0) $score *= $amp_score;


				// look forward for ign
				foreach($this->words->ign as $i)
				{
					$ign_pos = mb_stripos($s,$i->word,$emo_pos);
					if(is_int($ign_pos))
					{
						$score = 0;

						if($debug)
						{
							echo "found[".$ign_pos."]:".$i->word;
							echo "<br />";
						}
						
						continue;
						
					}
				}

				if($debug)
				{
					// total emo value
					echo "Score:".$score;
					echo "<br />";
				}
				
				// check weight
				$sentiment_len = $emo_len + $neg_len + $amp_len;
				$weight = $sentiment_len/$s_len;
				if($debug)
				{
					echo "Weight:".$weight;
					echo "<br />";
				}
				
				// cal net emo value
				$net_score = $score*$weight*100;
				$total_net_score += $net_score;
				if($debug)
				{
					echo "Net:".$net_score;
					echo "<br />";
				}
				
				$score = 0;
			}
		}
		
		// sum all 
		$net_weight = $total_sentiment_phrase/$total_len;
		$sentiment = $total_net_score*$net_weight;

		if($debug)
		{
			echo "<hr />";
			echo "Total Sentiment Phrase:".$total_sentiment_phrase."<br />";
			echo "Total Len:".$total_len."<br />";
			echo "Weight:".$net_weight."<br />";
		}
		
		return $sentiment;
	}
	
	public function clear($client_id)
	{		
		$sql = "UPDATE subject SET matching_status = 'update',bot_id=0 WHERE client_id='".$client_id."' and bot_id != 0 and matching_status !='disable'";
		//$sql = "UPDATE subject SET matching_status = 'update',bot_id=0 WHERE client_id='".$client_id."' and bot_id = -1 and matching_status !='disable'";
		$this->db->query($sql);		
		
		echo "Clear = ".$this->db->affected_rows()." Row".PHP_EOL;

	}
	public function clear_all()
	{		
		$sql = "UPDATE subject SET matching_status = 'update',bot_id=0 WHERE bot_id != 0 and matching_status !='disable'";
		$this->db->query($sql);		
		
		echo "Clear_all bot (-1) = ".$this->db->affected_rows()." Row".PHP_EOL;

	}
	
}
?>