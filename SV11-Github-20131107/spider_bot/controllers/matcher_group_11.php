<?PHP
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Matcher_group_11 extends CI_Controller{	

	public function bot($type='queue',$client_id=null,$sphinx_port=9312)
	{

		$IP_Port ='27.254.81.11.'.$sphinx_port;
		$query_index = $this->db->get_where('sphinx_index',array('name'=>$IP_Port));
		
		$match_port =$sphinx_port;
		$match_index =$query_index->row()->sphinx_index;
		$match_to_id =$query_index->row()->match_1;
		
		$bot_id = rand(0,10000);
		echo 'bot id:'.$bot_id.PHP_EOL;
		
		$bot_query = $this->db->get_where('subject',array('bot_id'=>$bot_id));
		$found_bot = $bot_query->num_rows();
		echo 'found same bot:'.$found_bot.PHP_EOL;
				
		$option = array(
			'query IS NOT NULL'=> null, 
			'matching_status' => $type,
			'client_id' => $client_id,
			'bot_id' => 0,
            'match_id <  '=>$match_to_id,
			'classify_id IS NULL'=> null,
			'cate_id'=> 10
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
				$res = $this->run_group($subject_id,$clean,0,$match_port,$match_index,$match_to_id);
				
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
	public function run_group($subject_id,$clean=true,$query_offset=0,$match_port,$match_index,$match_to_id)
	{
		$this->load->helper('sphinxapi');
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
		$kpiology_db = $this->load->database($config,true);			

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

		// sphinx init		
		$cl = new SphinxClient ();
		$q = $query;
		$sql = "";
		$mode = SPH_MATCH_EXTENDED;
		
		#$host = "localhost";
		$host = "27.254.81.11";		
		//$port = 9312;
		$port = (int)$match_port;
		//$index = "src1_match_index";
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

		$cl->SetRankingMode ( $ranker );
		
		echo "Starting Query Index...\n";
		$res = $cl->Query ( $q, $index );
		
		echo "Query Indexing\n";
		
		//$res = true;
		/////////////////////////////////////////////////
		// do Insert to DB
		/////////////////////////////////////////////////		
		
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
						
			if($res['total'] == 0){
				echo "no result<br/>\n";
			}
			else if($res['total'] > $limit+$offset){
				$this->run($subject_id,$clean,$limit+$offset,$match_port,$match_index,$match_to_id);
			} 
			else
			{				
				echo "Updating....";
				
				foreach ( $res["matches"] as $k=>$docinfo )
				{
					//echo '('.$k.')'.$docinfo["id"]." ";
					set_time_limit(0);

					//-----------------------------------------------------						
					$insert_matchs++;
					echo $insert_matchs.' :'.$docinfo["id"].PHP_EOL;
										
					$data =array();
					$data["post_id"] =$docinfo["id"];
					$data["subject_id"] =$subject_id;
											
					//$this->db->insert('matchs_c'.$client_id,$data);
					
					$insert_str = $kpiology_db->insert_string('matchs_all',$data);
					$insert_str = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_str);
					$kpiology_db->query($insert_str);	
					
					$post = new Post_model();
					$post->init($docinfo["id"]);
						
					$data_post =array();
					$data_post["post_id"] 		=$post->id;
					$data_post["subject_id"] 	=$subject_id;
					$data_post["type"] 			=$post->type;
					$data_post["title"] 		=$post->title;
					$data_post["body"] 			=$post->body;
					$data_post["tweet_id"] 		=$post->tweet_id;
					$data_post["facebook_id"] 	=$post->facebook_id;
					$data_post["match_date"] 	=date("Y-m-d H:i:s");
					
					$ins_match = $kpiology_db->insert_string('matchs_all_detail',$data_post);
					$ins_match = str_replace('INSERT INTO','INSERT IGNORE INTO',$ins_match);
					$kpiology_db->query($ins_match);															
					//---------------------------------------
					
					unset($post);
				}
				
				echo 'total matchs :'.$insert_matchs.PHP_EOL;				
			}
		}
		
		$check = true;

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
		$sql = "UPDATE subject SET matching_status = 'update',bot_id=0 WHERE client_id='".$client_id."' and classify_id is null and bot_id != 0 and matching_status !='disable'";
		$this->db->query($sql);		
		
		echo "Clear = ".$this->db->affected_rows()." Row".PHP_EOL;

	}
	
}
?>