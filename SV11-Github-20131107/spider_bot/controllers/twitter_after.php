<?php

date_default_timezone_set('Asia/Bangkok');
defined('BASEPATH') OR exit('No direct script access allowed');

class Twitter_after extends CI_Controller {
			
	public function getdata($filename)	
	{
		//$this->load->helper("url");
		
		/*
		$config['hostname'] = "localhost";
		$config['username'] = "root";
		$config['password'] = "thtoolsth!";
		
		$config['database'] = "spider";
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
		*/
		
		//======================================
		$file_arr = explode('\\',$filename);
				
		$query = $this->db->get_where('log_twitter_after',array('filename =' => $file_arr[2],'status_insert =' => 1));
				
		if($query->num_rows() == 0)
		{
			if(file_exists($filename)){
			
				echo "File : ".$filename."\n"; //exit;
				
				$data_log = array();
				$data_log["filename"] 		= $file_arr[2];
				$data_log["status_insert"] 	= 0;
				$data_log["start_insert"] 	= date("Y-m-d H:i:s");
				
				$insert_log = $this->db->insert_string("log_twitter_after",$data_log);				
				$this->db->query($insert_log);
				
				/**/	
				//$filelog = fopen(base_url()."data_twitter/20130701-1", "r");
				//$filelog = fopen(base_url()."data_twitter/".$row->filename, "r");
				$filelog = fopen($filename, "r");
						
				$ln= 0;				
				while ($line= fgets($filelog,20000000)) {
				++$ln;                       
								
					if($line===FALSE){
						//print ("FALSE\n");
					}else{					
						echo $filename." / line = ".$ln."\n";
						
						$tweet_arr =explode(" ",$line,6);
						if(isset($tweet_arr)){
							
							$dateTweet =$tweet_arr[0]." ".$tweet_arr[1];											
						
							str_replace("RT @", " ", $tweet_arr[5], $count_rt);
							//echo " count=".$count_rt;																												
						}
						
						$data = array();			
						$data["post_date"] 	= $dateTweet;
						$data["parse_date"] 	= date("Y-m-d H:i:s");
						$data["page_id"] 	= 0;
						$data["type"] 		= ($count_rt > 0) ? "retweet" : "tweet";
						$data["author_id"] 	= 0;
						$data["username"] 	= str_replace('"','',$tweet_arr[3]);
						$data["follower"] 	= 0;
						//$data["body"]		= str_replace('"','',$tweet_arr[5]);
						$databody 		= explode('"',$tweet_arr[5]);
						$data["body"] 		= $databody[1];
						$data["tweet_id"] 	= $tweet_arr[4];
															
						$insert_query = $this->db->insert_string("post_twitter_after",$data);					
						$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
						$this->db->query($insert_query);
						
					}				    			
				}
			
				fclose ($filelog);						
				$this->db->update('log_twitter_after',array('status_insert'=>1,'end_insert'=>date("Y-m-d H:i:s")),array('filename'=>$file_arr[2])); 
			}
		}
		else{
			echo "Check Insert = ".$query->num_rows().PHP_EOL;
			echo "File : ".$filename." Insert Complete".PHP_EOL;
			sleep(5);
		}
				
		
	}
	
}
?>
