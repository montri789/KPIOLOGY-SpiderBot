<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Matchs_to_kpiology extends CI_Controller {
	

	public function run($client_id){
				
		//$config['default']['hostname'] = 'localhost';
		//$config['default']['username'] = 'root';
		//$config['default']['password'] = 'thtoolsth!';

		$config['hostname'] = '203.151.21.111';
		$config['username'] = 'root';
		$config['password'] = 'thtoolsth!';

		//$config['default']['hostname'] = '203.150.231.155';
		//$config['default']['username'] = 'root';
		//$config['default']['password'] = 'Cg3qkJsV';
		
		$config['database'] = "kpiology";
		$config['dbdriver'] = "mysql";
		$config['dbprefix'] = "";
		$config['pconnect'] = FALSE;
		$config['db_debug'] = TRUE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = "";
		$config['char_set'] = "utf8";
		$config['dbcollat'] = "utf8_general_ci";
		
		$thothconnect_db = $this->load->database($config,true);
		

		$sql ="SELECT id,subject,s.cate_id,cate_name FROM subject s,categories c
		       WHERE client_id = $client_id AND `to` = '2013-03-31' AND s.cate_id = c.cate_id AND matching_status != 'disable' ";
		
		
		$query = $this->db->query($sql);
		
		
		foreach($query->result() as $row){
			$match_sql = "SELECT m.post_id,m.sentiment,system_correct_date 
					FROM subject s,matchs m,post p   
					WHERE client_id = ".$client_id."  
					AND `to` = '2013-03-31' 
					AND m.subject_id = s.id 
					AND m.post_id = p.id 
					AND (DATE(p.post_date) >= '2013-03-19' AND DATE(p.post_date) <= '3013-03-31')
					AND m.subject_id = ".$row->id." ";
					
			set_time_limit(60);

			$match_query = $this->db->query($match_sql);
			foreach($match_query->result() as $match_row){
							
				set_time_limit(60);

				echo "SUBJECT ID = ".$row->id." / POST ID = ".$match_row->post_id." \n";


				$post = new Post_model();
				$post->init($match_row->post_id);

				$mood = $match_row->sentiment;
				$system_correct_date = $match_row->system_correct_date;
																		
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
						$data["group_id"] = $row->cate_id;
						$data["group"] = $row->cate_name;
						$data["url"] = substr($postData->root_url,0,-1)."".$postData->url;
						$data["page_id"] = $postData->page_id;
						$data["subject_id"] = $row->id;
						$data["subject_name"] = $row->subject;
						$data["mood"] = $mood;
						$data["mood_by"] = 'system';
						$data["system_correct"] = $mood;
						$data["system_correct_date"] = $system_correct_date;
													
						echo 'w';
						$thothconnect_db->reconnect();
									
						$insert_query = $thothconnect_db->insert_string("website_c".$client_id,$data);
						$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
						$thothconnect_db->query($insert_query);

						
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
						$data["group_id"] = $row->cate_id;
						$data["group"] = $row->cate_name;
						$data["tweet_id"] = $postData->tweet_id;
						$data["subject_id"] = $row->id;
						$data["subject_name"] = $row->subject;
						$data["mood"] = $mood;
						$data["mood_by"] = 'system';
						$data["system_correct"] = $mood;
						$data["system_correct_date"] = $system_correct_date;
						
						echo 't';
						$thothconnect_db->reconnect();
						
						$insert_query = $thothconnect_db->insert_string("twitter_c".$client_id,$data);
						$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
						$thothconnect_db->query($insert_query);
					}
						
				}else if($post->type == "fb_post" || $post->type == "fb_comment"){
					$postData = $post->get_post_facebook($post->id);
					if($postData != null){
						$data = array();
						$data["post_id"] = $postData->post_id;
						$data["post_date"] = $postData->post_date;
						$data["body"] = $postData->body;
						$data["type"] = $postData->type;
						$data["author_id"] = $postData->author_id;
						$data["author"] = $postData->author;
						$data["group_id"] = $row->cate_id;
						$data["group"] = $row->cate_name;
						$data["facebook_page_id"] = $postData->facebook_page_id;
						$data["facebook_page_name"] = $postData->facebook_page_name;
						$data["subject_id"] = $row->id;
						$data["subject_name"] = $row->subject;
						$data["facebook_id"] = $postData->facebook_id;
						$data["parent_post_id"] = $postData->parent_post_id;
						$data["likes"] = $postData->likes;
						$data["shares"] = $postData->shares;
						$data["mood"] = $mood;
						$data["mood_by"] = 'system';
						$data["system_correct"] = $mood;
						$data["system_correct_date"] = $system_correct_date;
				

						echo 'f';
						$thothconnect_db->reconnect();
													
						$insert_query = $thothconnect_db->insert_string("facebook_c".$client_id,$data);
						$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
						$thothconnect_db->query($insert_query);
					}
				}																	
			}
		}
		
		echo "\nsuccess";
	}
}
?>