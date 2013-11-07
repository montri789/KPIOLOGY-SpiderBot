<?PHP
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Matcher_classify_11 extends CI_Controller{	

	public function connect_kpiology(){
			
		$config['hostname'] = "27.254.81.11";
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
		
		$db = $this->load->database($config,true);
		
		return $db;
	}
	
	public function bot($type='queue',$client_id=null,$sphinx_port=9314)
	{
		$kpiology_db =$this->connect_kpiology();
		
		$IP_Port ='27.254.81.11.'.$sphinx_port;
		$query_index = $kpiology_db->get_where('sphinx_index_all',array('name'=>$IP_Port));
		
		$match_port =$sphinx_port;
		$match_index =$query_index->row()->sphinx_index;
		$match_to_id =$query_index->row()->match_1;
		
		$bot_id = rand(0,10000);
		echo 'bot id:'.$bot_id.PHP_EOL;
		
		$bot_query = $this->db->get_where('subject',array('bot_id'=>$bot_id));
		$found_bot = $bot_query->num_rows();
		echo 'found same bot:'.$found_bot.PHP_EOL;			
		
		$sql ="select * from subject 
				where query IS NOT NULL 
				and matching_status ='".$type."' 
				and client_id =".$client_id."
				and match_id <".$match_to_id." 				
				and bot_id =0 
				and classify_id is not null";
		
		//and classify_id in(select id from subject where cate_id=10)
		
		//echo $sql; exit;
		$query = $this->db->query($sql);		
		
		$available_subjects = $query->num_rows();
		echo 'Available Subject : '.$available_subjects.PHP_EOL;
		$err = false;
		
		while(!$err && $available_subjects > 0)
		{				
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
			
			//$query = $this->db->get_where('subject',$option,1,0);
			
			$sql ="select * from subject 
					where query IS NOT NULL 
					and matching_status ='".$type."' 
					and client_id =".$client_id."
					and match_id <".$match_to_id."
					and bot_id =0 
					and classify_id in(select id from subject where cate_id=10)";
		
			//echo $sql; exit;
			$query = $this->db->query($sql);	
			
			$available_subjects = $query->num_rows();
			echo 'Available Subject : '.$available_subjects.PHP_EOL;
		}
	}
	
	public function run($subject_id,$clean=true,$query_offset=0,$match_port,$match_index,$match_to_id)
	{
		$this->load->helper('sphinxapi');
		$this->load->helper('mood');
		
		echo "\nLoad Database\n";
		$kpiology_db = $this->connect_kpiology();

		// skip if matching_status is "matching"
		$subject_data = $this->custom_model->get_multi_value('subject','client_id,query,matching_status,classify_id',$subject_id);
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

		$client_id = $subject_data->client_id;		
		   		
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
		
		echo "classify_id =".$subject_data->classify_id."\n";

		// sphinx init		
		$cl = new SphinxClient ();
		$q = $query;
		$sql = "";
		$mode = SPH_MATCH_EXTENDED;
		
		#$host = "localhost";
		$host = "27.254.81.11";		
		//$port = 9312;
		$port = (int)$match_port;
		$index = $match_index;
		
		$groupby = "";
		$groupsort = "@group desc";
		$filter = "subject_id";
		$filtervals = array($subject_data->classify_id);
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
		
		if ( count($filtervals) )	$cl->SetFilter ( $filter, $filtervals );
		// if ( $groupby )				$cl->SetGroupBy ( $groupby, SPH_GROUPBY_ATTR, $groupsort );
		if ( $sortby )				$cl->SetSortMode ( SPH_SORT_EXTENDED, $sortby );
		// if ( $sortexpr )			$cl->SetSortMode ( SPH_SORT_EXPR, $sortexpr );
		if ( $distinct )			$cl->SetGroupDistinct ( $distinct );
		if ( $select )				$cl->SetSelect ( $select );
		if ( $limit )				$cl->SetLimits ( 0, $limit, ( $limit>100000) ? $limit : 100000 );		

		$cl->SetRankingMode ( $ranker );

		echo "Starting Query Index...\n";
		$res = $cl->Query ( $q, $index );

		echo "Query Indexing\n";
		
		/////////////////////////////////////////////////
		// do Insert to DB
		/////////////////////////////////////////////////		
		
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
			
			$message_all =$res["total"];
			
			//2013-02-28 =================				
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
				echo "Updating....".PHP_EOL;
				
				$count_all =0;
				foreach ( $res["matches"] as $k=>$docinfo )
				{
						//echo '('.$k.')'.$docinfo["id"]." ";								
						set_time_limit(0);

						$post = new Post_model();
						$post->init($docinfo["id"]);											

						$mood = get_mood($post->body,$keywords);
						//-----------------------------------------------------
												
						$subject = $post->get_subject($subject_id);
						//print_r($subject);
																								
						if($post->type == "post" || $post->type == "comment"){	
						
							$postData = $post->get_post_website($post->id);
														
							if($postData != null){
								
								if($this->load_clients($client_id,$postData->sale,$postData->spam) == 'T'){
								
									//client TNS
									if($client_id==34){
									
										$block_web =array(180,278,237,64,115,72);
										if(!in_array($postData->website_id, $block_web)){
											
											//echo $client_id.' / website_id='.$postData->website_id; //exit;
										
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
											$data["sale"] = $postData->sale;
											$data["spam"] = $postData->spam;
											
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
											$kpiology_db->reconnect();
											//$res_insert[] = $kpiology_db->insert("website_c".$subject->client_id,$data);
																			
											$insert_query = $kpiology_db->insert_string("website_c".$subject->client_id,$data);
											$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
											$kpiology_db->query($insert_query);

											//$post->insert_post_comment($postData->page_id,$subject->client_id,$kpiology_db);
										}																	
									}
									else{
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
											$data["sale"] = $postData->sale;
											$data["spam"] = $postData->spam;
											
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
											$kpiology_db->reconnect();
											//$res_insert[] = $kpiology_db->insert("website_c".$subject->client_id,$data);
																			
											$insert_query = $kpiology_db->insert_string("website_c".$subject->client_id,$data);
											$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
											$kpiology_db->query($insert_query);

											//$post->insert_post_comment($postData->page_id,$subject->client_id,$kpiology_db);
									}
								
								}//if load_clients
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
								$kpiology_db->reconnect();
								//$res_insert[] = $kpiology_db->insert("twitter_c".$subject->client_id,$data);
								
								$insert_query = $kpiology_db->insert_string("twitter_c".$subject->client_id,$data);
								$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
								$kpiology_db->query($insert_query);
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
								$kpiology_db->reconnect();
								
								//$res_insert[] = $kpiology_db->insert("facebook_c".$subject->client_id,$data);
								
								$insert_query = $kpiology_db->insert_string("facebook_c".$subject->client_id,$data);
								$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
								//echo 'sql='.$insert_query; exit;
								$kpiology_db->query($insert_query);
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
						$res5 = $this->db->query($insert_str);
						//$res = $spider_db->query($insert_str);
						//---------------------------------------
											
						$count_all++;

						$data_l = array();
						$data_l["stop_datetime"] 	= date("Y-m-d H:i:s");
						$data_l["match_insert"] 	= $count_all;
						$data_l["last_post_id"] 	= $post->id;
						
						$this->db->update('status_match',$data_l,array('id'=>$match_id));
						//---------------------------------------
						
						//$res_del = $this->db->delete('matchs_all_detail', array('post_id' => $post->id,'subject_id' => $subject_data->classify_id));
						echo ' INSERT : '.$insert_matchs.'/'.$message_all.' '.$post->id.PHP_EOL;
						unset($post);						
				}
				
				echo 'total matchs :'.$insert_matchs.PHP_EOL;
				echo 'total website :'.$insert_website.PHP_EOL;
				echo 'total twitter :'.$insert_twitter.PHP_EOL;
				echo 'total facebook :'.$insert_facebook.PHP_EOL;
				echo 'total page_id :'.count($list_page_id).PHP_EOL;								
			}
		}
		
		$check = true;

		// flag subject as update..
		if($check == true){
			$data = array(
					'matching_status'=>'update',
					'latest_matching'=> mdate('%Y-%m-%d %H:%i:%s',time()),
					'bot_id'=>0,
					'match_id'=>$match_to_id
					);
					
			$this->db->update('subject',$data,array('id'=>$subject_id));
		}		
		
		$data_l = array();
		$data_l["stop_datetime"] 	= date("Y-m-d H:i:s");
		$data_l["is_matching"] 	= ($check == true) ? 'N' : 'F';	//N=Complete,F=Fail
		
		$this->db->update('status_match',$data_l,array('id'=>$match_id));
				
		return $check;	
	}
	
	public function clear($client_id)
	{		
		$sql = "UPDATE subject SET matching_status = 'update',bot_id=0 WHERE client_id='".$client_id."' and classify_id is not null and bot_id != 0 and matching_status !='disable'";
		$this->db->query($sql);		
		
		echo "Clear = ".$this->db->affected_rows()." Row".PHP_EOL;

	}
	public function clear_all()
	{		
		$sql = "UPDATE subject SET matching_status = 'update',bot_id=0 WHERE bot_id != 0 and matching_status !='disable'";
		$this->db->query($sql);		
		
		echo "Clear_all bot (-1) = ".$this->db->affected_rows()." Row".PHP_EOL;

	}
	
	public function load_clients($client_id,$sale,$spam)
    {
       $sql = "select check_sale_spam from clients where client_id = ".$client_id;
       $query = $this->db->query($sql);
       $row = $query->result();
       $check = $row[0]->check_sale_spam;
       
       switch ($check) {
           case "all":
               $check = 'T';
               break;
           case "nosalespam":
               if(($sale == 1)||($spam == 1)){
                  $check = 'F'; 
               }else{
                   $check = 'T'; 
               }
               break;
           case "sale":                
               if($spam == 1)$check = 'F';else $check = 'T';
               break;
           case "spam":
               if($sale == 1)$check = 'F';else $check = 'T';
               break;
       }
       
       return $check;
    }
	
}
?>