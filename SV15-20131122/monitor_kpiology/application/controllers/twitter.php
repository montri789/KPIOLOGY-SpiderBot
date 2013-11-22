<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Twitter extends CI_Controller {
    
    function __construct()
	{
		parent::__construct();
        
        $arr = $this->session->userdata('logged_in');
           //echo $arr['user'];
           if(!isset($arr['email'])){
                    echo "<script>alert('Please Login @thothmedia.com');</script>";
                    redirect('login', 'refresh');
            }
		
	}

	var $spider_db;
	
	public function init(){
		/*
		$config['hostname'] = "203.151.21.111";
		$config['username'] = "root";
		$config['password'] = "thtoolsth!";
		*/
		//$config['hostname'] = "203.150.231.155";
		$config['hostname'] = "localhost";
		$config['username'] = "root";
		$config['password'] = "Cg3qkJsV";
		
		$config['database'] = "spider";
		$config['dbdriver'] = "mysql";
		$config['dbprefix'] = "";
		$config['pconnect'] = FALSE;
		$config['db_debug'] = TRUE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = "";
		$config['char_set'] = "utf8";
		$config['dbcollat'] = "utf8_general_ci";
		
		$this->spider_db = $this->load->database($config,true);
	}
	
	public function index2(){
			
		$last_running = "2013-03-12 17:12:08";
	
		$today = date("Y-m-d H:i:s");
			echo $today."/";
		$to_time = strtotime($today);
		$from_time = strtotime($last_running);
		$diff = $from_time - $to_time; 
		$diff = abs($diff)/60;
		
		echo $diff;
		
		if($diff > 15){
			echo "down";
		}else{
			echo "up";
		}
			
		//$dateDiff = (strtotime($today) - strtotime($last_running)) / ( 60 * 60 );
		//echo $dateDiff;	
	}
	
	
	public function index()	{
				
		$this->init();
		date_default_timezone_set("Asia/Bangkok");
		$data = array();
		
		$sql_chk ="select last_running from bot_status where bot_name = 'author_id_lookup' limit 1";
		$count_chk = $this->spider_db->query($sql_chk);
		$count_chk = $count_chk->row_array();
		if(is_array($count_chk)){
	
			$last_running =$count_chk["last_running"];
			
			$sql_chk2 = "SELECT count(id) as 'tweet_messages' FROM post_twitter_temp";
			$tweetleft = $this->spider_db->query($sql_chk2);
			$tweetleft = $tweetleft->row_array();
			//$tweetleft =$count_chk[0][3];

			$today = date("Y-m-d H:i:s");
	
			$to_time = strtotime($today);
			$from_time = strtotime($last_running);
			$diff = $from_time - $to_time;
			$diff = abs($diff)/60;
			if($diff > 15){
				$data["convertor"]["status"] = "down";
			}else{
				$data["convertor"]["status"] = "up";
			}
			
			$data["convertor"]["last_running"] = $last_running;
			$data["convertor"]["tweet_messages"] = number_format($tweetleft['tweet_messages']);
		}
		
		$skip = 0;

		$sql_chk3 ="select last_running,tweetleft  from bot_status
			    where bot_name = 'twitter_firehose' AND work_status = 'normal'
			    AND TIMESTAMPDIFF(MINUTE,last_running,NOW()) < 6 LIMIT 1";
		$count_chk3 = $this->spider_db->query($sql_chk3);
		
		if($count_chk3->num_rows() > 0){
			//normal
			$count_chk3 = $count_chk3->row_array();
			
			$data["firehose"]["tweetleft"] = number_format($count_chk3["tweetleft"]);
			$data["firehose"]["status"] = "up";
			$data["firehose"]["last_running"] = $count_chk3['last_running'];
			$skip = 1;
		}

		if($skip ==0){
			$sql_chk3 ="select last_running from bot_status
			             where bot_name = 'twitter_firehose' AND work_status = 'dead'
				     AND TIMESTAMPDIFF(MINUTE,last_running,NOW()) < 6 ORDER BY id DESC LIMIT 1";

			$count_chk3 = $this->spider_db->query($sql_chk3);
				
			if($count_chk3->num_rows() > 0){
				//dead
				$count_chk3 = $count_chk3->row_array();
				$data["firehose"]["status"] = "down";
				$data["firehose"]["last_running"] = $count_chk3['last_running'];
			}else{
	
				$sql_chk3 ="select last_running from bot_status
				            where bot_name = 'twitter_firehose' AND work_status = 'warning'
					    AND TIMESTAMPDIFF(MINUTE,last_running,NOW()) < 6 ORDER BY id DESC LIMIT 1";
		
				$count_chk3 = $this->spider_db->query($sql_chk3);
				
				if($count_chk3->num_rows() > 0){
					//warning
					$count_chk3 = $count_chk3->row_array();
					$data["firehose"]["status"] = "up";
					$data["firehose"]["warning"] = "warning";
					$data["firehose"]["last_running"] = $count_chk3['last_running'];
				}else{
					$sql = "SELECT tweetleft FROM bot_status WHERE work_status = 'normal' ";
					$query = $this->spider_db->query($sql);
					$res = $query->row_array();
					$data["tweetleft"] = number_format($res["tweetleft"]);
				}
			}
		}
		
		$view["module"] = $this->load->view("twitter",$data,true);
		$this->load->view("template_rp",$view);	
	}
}
