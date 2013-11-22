<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporting extends CI_Controller {
    
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
	
	public function index(){

		date_default_timezone_set("Asia/Bangkok");

		$num_select_day = 2;
		$dateNow = date("Y-m-d");
		$dateEnd = date("Y-m-d",strtotime("-".$num_select_day." day"));
		/* -----bot count------*/
		$sql_bot = "select bot_name as name from monitor_date_post where get_date BETWEEN '".$dateEnd."' AND '".$dateNow."' group by bot_name";
		$query_bot = $this->db->query($sql_bot);
		
		$arr_bot_count = $query_bot->result_array();
		
		/* -----bot running------*/
		$sql_running = "select bot_name as bot_running from monitor_date_post where get_date BETWEEN '".$dateEnd."' AND '".$dateNow."' group by bot_name";
		$query_running = $this->db->query($sql_running);
		
		$arr_bot_running = $query_running->result_array();
		$bot_running = count($arr_bot_running);
		
		
		/* -----total post------*/
		$sql_sum = "select
				SUM(post_count) as post_count,
				SUM(page_count) as page_count
				from monitor_date_post
				WHERE get_date BETWEEN '".$dateEnd."' AND '".$dateNow."'
				order by get_date DESC limit 3";
		$query_sum = $this->db->query($sql_sum);
		
		$arr_post_sum = $query_sum->result_array();
		$total_post = $arr_post_sum[0]["post_count"];
		$total_page = $arr_post_sum[0]["page_count"];
		/*------detail post---------*/
				
		$sql_detail = " SELECT
				bot_name as bot,
				get_date as date,
				post_count,
				page_count,ip
				FROM monitor_date_post
				WHERE get_date BETWEEN '".$dateEnd."' AND '".$dateNow."'
				order by get_date DESC
				";
		$query_detail = $this->db->query($sql_detail);
		
		$arr_post_detail = $query_detail->result_array();
		
		
		
		
		$array_data["bot_running"] = $bot_running;
		$array_data["total_post"] = $total_post;
		$array_data["total_page"] = $total_page;
		$array_data["dir_bot"] = $arr_bot_count;
		$array_data["var_row"] = $arr_post_detail;
		
		$view["module"] = $this->load->view("reporting",$array_data,true);
		$this->load->view("template_rp",$view);
		
	}
	
	public function insert_db(){
		/*
		$path = "C:\KPIology-Spider-Generic\Generic\Dispatcher";
		$dir = scandir($path, 1);
		$dir = array_diff($dir,array("..","."));
		$num_path = count($dir);
		$j=0;
		//print_r($dir);
		
		for($i=0;$i<$num_path;$i++){
			if(preg_match("/Bot(.*)/" ,$dir[$i])){
				$dir_bot[$j]["path"] = $path.trim("\ ").$dir[$i];
				$dir_bot[$j]["name"] = $dir[$i];
				$j++;
			}
		}
		
		$cout_path = count($dir_bot);
		//print_r($dir_bot);
		//$var_row = array();
		//$j=0;
		//$total_post = 0;
		//$total_page = 0;
		$bot_running = 0;
		
		for($i=0;$i<$cout_path;$i++){
			
			if(file_exists($dir_bot[$i]["path"]."\SpiderStat.db")){
				$dbname = $dir_bot[$i]["path"]."\SpiderStat.db";
				$db = new PDO("sqlite:$dbname");
				$dateNow = date("Y-m-d");
				$sqls = "SELECT COUNT(*) as num_row FROM stattable where date = '$dateNow'";
				
				foreach ($db->query($sqls) as $rows){
					if($rows["num_row"]%2){
						$bot_running++;
					}
				}
				
				
				
				$sql = "SELECT * FROM detailedstattable order by date desc;";
				
				//echo "<br>".$dir_bot[$i]["name"]."<br>";
				//$dir_bot[$i]["name"];
				
				foreach ($db->query($sql) as $row){
					//print_r($row);
					$sql_table = "select * from monitor_post where domain_name = '".$row['domain']."' AND get_date = '".$row['date']."' AND bot_name = '".$dir_bot[$i]["name"]."'";
					$query = $this->db->query($sql_table);
					$array_data = $query->result_array();
					
					if(empty(count($array_data))){
						$data["domain_name"] = $row['domain'];
						$data["get_date"] = $row['date'];
						$data["post_count"] = $row['post_count'];
						$data["page_count"] = $row['page_count'];
						$data["bot_name"] = $dir_bot[$i]["name"];
						
						$this->db->insert("monitor_post",$data);
						
						/*$var_row[$j]['date'] = $row['date'];
						$var_row[$j]['post_count'] = $row['post_count'];
						$var_row[$j]['page_count'] = $row['page_count'];
						$var_row[$j]['bot'] = $dir_bot[$i]["name"];
						//array_push($var_row , $row);
						$total_post = $total_post + $row['post_count'];
						$total_page = $total_page + $row['page_count'];
						$j++;
					}else{
						
						$data_where["domain_name"] = $row['domain'];
						$data_where["get_date"] = $row['date'];
						$data_where["bot_name"] = $dir_bot[$i]["name"];
						$data["post_count"] = $row['post_count'];
						$data["page_count"] = $row['page_count'];
						
						$this->db->update("monitor_post",$data,$data_where);
					}
				}
				
				
				$sql_s = "SELECT * FROM stattable order by date desc;";
				
				//echo "<br>".$dir_bot[$i]["name"]."<br>";
				//$dir_bot[$i]["name"];
				
				foreach ($db->query($sql_s) as $row_2){
					//print_r($row);
					$sql_table = "select * from monitor_date_post where get_date = '".$row_2['date']."' AND bot_name = '".$dir_bot[$i]["name"]."'";
					$query = $this->db->query($sql_table);
					$array_data = $query->result_array();
					
					if(empty(count($array_data))){
						$data["get_date"] = $row_2['date'];
						$data["post_count"] = $row_2['post_count'];
						$data["page_count"] = $row_2['page_count'];
						$data["bot_name"] = $dir_bot[$i]["name"];
						
						$this->db->insert("monitor_date_post",$data);
						
						/*$var_row[$j]['date'] = $row['date'];
						$var_row[$j]['post_count'] = $row['post_count'];
						$var_row[$j]['page_count'] = $row['page_count'];
						$var_row[$j]['bot'] = $dir_bot[$i]["name"];
						//array_push($var_row , $row);
						$total_post = $total_post + $row['post_count'];
						$total_page = $total_page + $row['page_count'];
						$j++;
					}else{
						
						$data_where["get_date"] = $row_2['date'];
						$data_where["bot_name"] = $dir_bot[$i]["name"];
						$data["post_count"] = $row_2['post_count'];
						$data["page_count"] = $row_2['page_count'];
						
						$this->db->update("monitor_date_post",$data,$data_where);
					}
				}
			}
			*/
		}
		
		//print_r($var_row);
		//$dir_bot = sort($dir_bot);
		
		/*sort($dir_bot);
		//print_r($dir_bot);
		$data["bot_running"] = $bot_running;
		$data["dir_bot"] = $dir_bot;
		$data["var_row"] = $var_row;
		$data["total_post"] = $total_post;
		$data["total_page"] = $total_page;
			
		$view["module"] = $this->load->view("reporting",$data,true);
		$this->load->view("template_rp",$view);
		
	}
	*/
	/*
	public function testsql(){
		$dbname="assets/sql/SpiderStat.db";
		$db = new PDO("sqlite:$dbname");
		$sql="SELECT * FROM stattable order by date desc limit 1,3;";
		foreach ($db->query($sql) as $row){
		    print_r($row);
		}
		
		
		//$dbhandle = sqlite_open('assets/sql/SpiderStat');
		//$result = sqlite_array_query($dbhandle, 'SELECT * FROM stattable LIMIT 3', SQLITE_ASSOC);
		/*foreach ($result as $entry) {
		    echo 'Name: ' . $entry['name'] . '  E-mail: ' . $entry['email'];
		}
		//print_r($result);
	}
	*/
	/*
	public function path(){
	
		$path = "C:\KPIology-Spider-Generic\Generic\Dispatcher";
		$dir = scandir($path, 1);
		$dir = array_diff($dir,array("..","."));
		$num_path = count($dir);
		$j=0;
		//print_r($dir);
		
		for($i=0;$i<$num_path;$i++){
			if(preg_match("/Bot(.*)/" ,$dir[$i])){
				$dir_bot[$j]["path"] = $path.trim("\ ").$dir[$i];
				$dir_bot[$j]["name"] = $dir[$i];
				$j++;
			}
		}
		
		$cout_path = count($dir_bot);
		//print_r($dir_bot);

		for($i=0;$i<$cout_path;$i++){
			
			if(file_exists($dir_bot[$i]["path"]."\SpiderStat.db")){
				$dbname = $dir_bot[$i]["path"]."\SpiderStat.db";
				$db = new PDO("sqlite:$dbname");
				$sql = "SELECT * FROM stattable order by date desc limit 1,3;";
				
				//echo "<br>".$dir_bot[$i]["name"]."<br>";
				
				foreach ($db->query($sql) as $row){
					if(count($row) > 0){
						//print_r($row);
					}    
				}
			}
			
		}
		
		$data["dir_bot"] = $dir_bot;
			
		$view["module"] = $this->load->view("reporting",$data);
		$this->load->view("template_rp",$view);
		
	}
	*/
}

?>