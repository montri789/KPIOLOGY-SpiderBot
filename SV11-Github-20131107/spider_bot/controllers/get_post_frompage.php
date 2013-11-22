<?PHP
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_post_frompage extends CI_Controller{	
	
	public function run($client_id=null,$type=null,$subject_id=null)
	{
		//$this->load->helper('sphinxapi');
		$this->load->helper('mood');
				
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
		
		echo "\nLoad Database\n";
		$thothconnect_db = $this->load->database($config,true);
		
		//=====================================================
		$insert_website = 0;
		$insert_twitter = 0;
		$insert_facebook = 0;
		$list_page_id = array();
		$insert_website_post_comment = 0;
		$insert_matchs = 0;	
		
		$subject_data = $this->custom_model->get_multi_value('subject','client_id,query,matching_status',$subject_id);
		$keywords = get_keywords($subject_data->query);
		
		
		//$sql ="select post_id from ".$type."_c".$client_id."_20131114 where subject_id='".$subject_id."'";
		//$sql ="select post_id from ".$type."_c".$client_id."_20131115 where subject_id='".$subject_id."' and post_date >='2013-11-14 15:00:00' and post_date <='2013-11-14 18:00:00'";
		$sql ="select post_id from ".$type."_c".$client_id." where subject_id='".$subject_id."' and post_date >='2013-11-17 00:00:00'";
				
		$query =$thothconnect_db->query($sql);
		foreach($query->result() as $row){
			echo "post_id : $row->post_id \n";
			
			$sql_page ="select page_id from post where id='".$row->post_id."'";
			$query_page =$this->db->query($sql_page);
			foreach($query_page->result() as $rowp){
				echo "-page_id : $rowp->page_id \n";
				
				$sql_post ="select id from post where page_id='".$rowp->page_id."'";
				$query_post =$this->db->query($sql_post);
				foreach($query_post->result() as $rowpo){
					echo "--post_id : $rowpo->id ";

						set_time_limit(0);

						$post = new Post_model();
						//$post->init($docinfo["id"]);
						$post->init($rowpo->id);
												
						$post_year_month =  date("Y-m",strtotime($post->post_date));					

						$mood = get_mood($post->body,$keywords);

						//-----------------------------------------------------												
						$subject = $post->get_subject($subject_id);
						//print_r($subject);
												
						//echo "=>1";												
						if($post->type == "post" || $post->type == "comment"){	
						
							$postData = $post->get_post_website($post->id);
							
							if($this->load_clients($client_id,$postData->sale,$postData->spam) == 'T'){
														
								if($postData != null){
									
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
											echo "w \n";
											$thothconnect_db->reconnect();
											//$res_insert[] = $thothconnect_db->insert("website_cn".$subject->client_id,$data);
																			
											$insert_query = $thothconnect_db->insert_string("website_cn".$subject->client_id,$data);
											$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
											$thothconnect_db->query($insert_query);

											//$post->insert_post_comment($postData->page_id,$subject->client_id,$thothconnect_db);
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
											echo "w \n";
											$thothconnect_db->reconnect();
											//$res_insert[] = $thothconnect_db->insert("website_cn".$subject->client_id,$data);
																			
											$insert_query = $thothconnect_db->insert_string("website_cn".$subject->client_id,$data);
											$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
											$thothconnect_db->query($insert_query);

											//$post->insert_post_comment($postData->page_id,$subject->client_id,$thothconnect_db);
									}																	
								}
							
							}//end load_clients
							
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
								echo "t \n";
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
								echo "f \n";
								$thothconnect_db->reconnect();
								
								//$res_insert[] = $thothconnect_db->insert("facebook_c".$subject->client_id,$data);
								
								$insert_query = $thothconnect_db->insert_string("facebook_c".$subject->client_id,$data);
								$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
								//echo 'sql='.$insert_query; exit;
								$thothconnect_db->query($insert_query);
							}
						}																					
						
						$insert_matchs++;																		
						unset($post);
						
				}//end post3						
			}//end page2			
		}//end post1
		
		echo 'total matchs :'.$insert_matchs.PHP_EOL;
		echo 'total website :'.$insert_website.PHP_EOL;
		echo 'total twitter :'.$insert_twitter.PHP_EOL;
		echo 'total facebook :'.$insert_facebook.PHP_EOL;
		echo 'total page_id :'.count($list_page_id).PHP_EOL;
		
		//$check = true;
		//return $check;	
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