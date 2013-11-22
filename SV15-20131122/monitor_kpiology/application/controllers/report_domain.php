<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_domain extends CI_Controller {
	
	var $spider_db;
	
	public function init(){
		$config['hostname'] = "27.254.81.11";
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
		
		$this->spider_db = $this->load->database($config,true);
	}
	
	public function index(){
		
		$this->init();
		$arr_bot_show = array();
		for($i=0;$i<=2;$i++){
			switch ($i) {
				case 0:
					$i = $i;
					$num_select_day = $i;
					$dateNow = date("Y-m-d",strtotime("-".$num_select_day." day"));
					$dateEnd = date("Y-m-d",strtotime("-".$num_select_day." day"));
					
					$arr_bot = $this->getAvg($dateEnd,$dateNow);
					array_push($arr_bot_show,$arr_bot);
				case 1:
					$i = 1;
					$num_select_day = $i;
					$dateNow = date("Y-m-d",strtotime("-".$num_select_day." day"));
					$dateEnd = date("Y-m-d",strtotime("-".$num_select_day." day"));
					$arr_bot = $this->getAvg($dateEnd,$dateNow);
					array_push($arr_bot_show,$arr_bot);
				case 2:
					$i = 7;
					$num_select_day = $i;
					$dateNow = date("Y-m-d",strtotime("-1 day"));
					$dateEnd = date("Y-m-d",strtotime("-".$num_select_day." day"));
					$arr_bot = $this->getAvg($dateEnd,$dateNow);
					array_push($arr_bot_show,$arr_bot);
			}
			
		}
		
		$sql_domain = "select domain_name from monitor_post group by domain_name";
		$query_domain = $this->spider_db->query($sql_domain);
		
		$arr_domain = $query_domain->result_array();
		$num_domain = count($arr_domain);
		//print_r($arr_bot_show);
		/* -----bot count------
		$sql_bot = "select * from monitor_post where get_date = '".$date."' and bot_name = '".$bot_name."'";
		$query_bot = $this->spider_db->query($sql_bot);
		
		$arr_bot_count = $query_bot->result_array();
		*/
		/* -----bot running------
		$sql_running = "select COUNT(bot_name) as bot_running from monitor_date_post where get_date = '$dateNow' group by bot_name";
		$query_running = $this->spider_db->query($sql_running);
		
		$arr_bot_running = $query_running->result_array();
		$bot_running = count($arr_bot_running);*/
		
		/* -----total post------
		$sql_sum = "select SUM(post_count) as post_count,SUM(page_count) as page_count from monitor_date_post order by get_date DESC limit 3";
		$query_sum = $this->spider_db->query($sql_sum);
		
		$arr_post_sum = $query_sum->result_array();
		$total_post = $arr_post_sum[0]["post_count"];
		$total_page = $arr_post_sum[0]["page_count"];
		/*------detail post---------
		
		$dateEnd = date("Y-m-d",strtotime("-".$num_select_day." day"));
		
		
		$sql_detail = " SELECT
				bot_name as bot,
				get_date as date,
				post_count,
				page_count
				FROM monitor_date_post
				WHERE get_date BETWEEN '".$dateEnd."' AND '".$dateNow."'
				order by bot_name ASC
				";
		$query_detail = $this->spider_db->query($sql_detail);
		
		$arr_post_detail = $query_detail->result_array();
		
		*/
		
		$array_data["domain"] = $num_domain;
		$array_data["arr_color_show"] = $this->getSumItem();
		$array_data["arr_bot_show"] = $arr_bot_show;
		$array_data["arr_domain"] = $arr_domain;

		$view["module"] = $this->load->view("report_domain",$array_data,true);
		$this->load->view("template_rp",$view);
		
	}
	
	public function getAvg($dateEnd,$dateNow){
		//echo $dateEnd."|".$dateNow.",";
		$this->init();
		
		$sql_domain = "select domain_name from monitor_post group by domain_name";
		$query_domain = $this->spider_db->query($sql_domain);
		$arr_domain = $query_domain->result_array();
		$arr_tmp_bot = array();
		$i=0;
		$sum_post = 0;
		$sum_page = 0;
		
		foreach ($arr_domain as $rows){
			//echo $rows["domain_name"]."|main|<br>";
			$sql_bot = "select
				    sum(post_count) as post_count,
				    sum(page_count) as page_count,
				    domain_name,
				    get_date
				    from monitor_post
				    where get_date BETWEEN '".$dateEnd."' AND '".$dateNow."' and domain_name = '".$rows["domain_name"]."'
				    group by domain_name,get_date";
			$query_bot = $this->spider_db->query($sql_bot);
			$num_avg_domain = $query_bot->num_rows();

			$arr_bot = $query_bot->result_array();
			
			foreach ($arr_bot as $rows_bot){
				$sum_post = number_format($sum_post + $rows_bot["post_count"]);
				$sum_page = number_format($sum_page + $rows_bot["page_count"]);
				
			}
			//echo $rows["domain_name"]."|".$sum_post."|".$sum_page."<br>";
			$arr_tmp_bot[$i]["domain_name"] = $rows["domain_name"];
			@$arr_tmp_bot[$i]["post_count"] = number_format($sum_post / $num_avg_domain);
			@$arr_tmp_bot[$i]["page_count"] = number_format($sum_page / $num_avg_domain);
			$i++;
			unset($arr_bot);
			$sum_post = 0;
			$sum_page = 0;
		}
		return $arr_tmp_bot;
		
	}
	
	public function getSumItem(){
		$this->init();
		
		$dateNow = date("Y-m-d");
		$dateEnd = date("Y-m-d");
		$sql_domain = "select domain_name from monitor_post group by domain_name";
		$query_domain = $this->spider_db->query($sql_domain);
		$arr_domain = $query_domain->result_array();
		$arr_tmp_bot = array();
		$arr_chk_color = array();
		$arr_chk_color["red"] = 0;
		$arr_chk_color["yello"] = 0;
		$arr_chk_color["green"] = 0;
		$i=0;
		$sum_post = 0;
		$sum_page = 0;
		
		foreach ($arr_domain as $rows){
			//echo $rows["domain_name"]."|main|<br>";
			$sql_bot = "select
				    sum(post_count) as post_count,
				    sum(page_count) as page_count,
				    domain_name,
				    get_date
				    from monitor_post
				    where get_date BETWEEN '".$dateEnd."' AND '".$dateNow."' and domain_name = '".$rows["domain_name"]."'
				    group by domain_name,get_date";
			$query_bot = $this->spider_db->query($sql_bot);
			$num_avg_domain = $query_bot->num_rows();

			$arr_bot = $query_bot->result_array();
			
			foreach ($arr_bot as $rows_bot){
				$sum_post = number_format($sum_post + $rows_bot["post_count"]);
				$sum_page = number_format($sum_page + $rows_bot["page_count"]);
			}
			
			@$sum_post = number_format($sum_post / $num_avg_domain);
			@$sum_page = number_format($sum_page / $num_avg_domain);
			//echo $sum_post." | ".$sum_page."<br>";
			if(empty($sum_post) and empty($sum_page)){
				$arr_chk_color["red"] = $arr_chk_color["red"] + 1;
			}else if(empty($sum_post) or empty($sum_page)){
				$arr_chk_color["yello"] = $arr_chk_color["yello"] + 1;
			}else{
				$arr_chk_color["green"] = $arr_chk_color["green"] + 1;
			}
			$sum_post = 0;
			$sum_page = 0;
		}
		//print_r($arr_chk_color);
		return $arr_chk_color;
		
	}
	
}

?>