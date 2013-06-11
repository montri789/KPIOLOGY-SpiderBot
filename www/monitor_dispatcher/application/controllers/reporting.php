<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporting extends CI_Controller {
	
	var $spider_db;

	public function index(){
		//insert_db()
	}
	
	public function insert_db(){
	
		date_default_timezone_set("Asia/Bangkok");
		
		$path = "C:\KPIology-Spider-Generic\Generic\Dispatcher";
		$dir = scandir($path, 1);
        $data_insert = array();
        $data_insert2 = array();
        
        //$host= gethostname();
		//$ip = gethostbyname($host);
        $ip = "203.155.21.111";
		
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
				$num_select_day = 1;
				
				$sqls = "SELECT COUNT(*) as num_row FROM stattable where date = '$dateNow'";
                
				foreach ($db->query($sqls) as $rows){
					if($rows["num_row"]%2){
						$bot_running++;
					}
				}
                
				echo $dir_bot[$i]["name"]." Start\n";
				
				$dateEnd = date("Y-m-d",strtotime("-".$num_select_day." day"));
				$sql = "SELECT * FROM detailedstattable where date BETWEEN '$dateEnd' and '$dateNow' order by date desc;";
				
				//echo "<br>".$dir_bot[$i]["name"]."<br>";
				//$dir_bot[$i]["name"];
                
				foreach ($db->query($sql) as $row){
					//print_r($row);
					$sql_table = "select * from monitor_post where domain_name = '".$row['domain']."' AND ip = '".$ip."' AND get_date = '".$row['date']."' AND bot_name = '".$dir_bot[$i]["name"]."'";
					$query = $this->db->query($sql_table);
					$array_data = $query->result_array();
					//print_r($array_data);
					
                    if($row['domain']!=""){
					if($query->num_rows()==0){
					   
                       $insert_array =  array(
                        'domain_name' => $row['domain'] ,
        				'get_date' => $row['date'] ,
                        'request' => $row['request'] ,
        				'post_count' => $row['post_count'] ,
        				'page_count' => $row['page_count'] ,
        				'bot_name' => $dir_bot[$i]["name"] ,
        				'ip' => $ip
                       );
                       $data_insert[] = $insert_array;
                       //echo $row['domain']." ".$row['date']." ".$row['request']." ".$row['post_count']." ".$row['page_count']."\n";
                       
					}else{
					   
                       $data = array();
						$data_where["domain_name"] = $row['domain'];
						$data_where["get_date"] = $row['date'];
						$data_where["bot_name"] = $dir_bot[$i]["name"];
                        $data_where["ip"] = $ip;
                        $data["request"] = $row['request'];
						$data["post_count"] = $row['post_count'];
						$data["page_count"] = $row['page_count'];
						
						$this->db->update("monitor_post",$data,$data_where);
                        //echo $row['domain']." ".$row['date']." ".$row['request']." ".$row['post_count']." ".$row['page_count']."\n";
					}
                    }
				}
                
				$dir_bot[$i]["name"]." End\n\n";
                
				$sql_s = "SELECT * FROM stattable where date BETWEEN '$dateEnd' and '$dateNow' order by date desc;";
				
				//echo "<br>".$dir_bot[$i]["name"]."<br>";
				//$dir_bot[$i]["name"];

				foreach ($db->query($sql_s) as $row_2){
                    
					//print_r($row);
					$sql_table = "select * from monitor_date_post where get_date = '".$row_2['date']."' AND ip = '".$ip."'  AND bot_name = '".$dir_bot[$i]["name"]."'";
					$query = $this->db->query($sql_table);
					$array_data = $query->result_array();
					
					if($query->num_rows()==0){
					   
                       $insert_array2 =  array(
        				'get_date' => $row_2['date'] ,
        				'post_count' => $row_2['post_count'] ,
        				'page_count' => $row_2['page_count'] ,
        				'bot_name' => $dir_bot[$i]["name"] ,
        				'ip' => $ip
                       );
                       $data_insert2[] = $insert_array2;
                       echo "i ".$row_2['date']." ".$row_2['post_count']." ".$row_2['page_count']."\n";

					}else{
					   
                       $data_2 = array();
						$data_where_2["get_date"] = $row_2['date'];
						$data_where_2["bot_name"] = $dir_bot[$i]["name"];
                        $data_where_2["ip"] = $ip;
						$data_2["post_count"] = $row_2['post_count'];
						$data_2["page_count"] = $row_2['page_count'];
						
						$this->db->update("monitor_date_post",$data_2,$data_where_2);
                        echo "u ".$row_2['date']." ".$row_2['post_count']." ".$row_2['page_count']."\n";

					}
                    
                    
				}

			}
			
		}
        
        $count_insert = count($data_insert);
        if($count_insert>0){$this->db->insert_batch('monitor_post', $data_insert);}
        
        $count_insert2 = count($data_insert2);
        if($count_insert2>0){$this->db->insert_batch('monitor_date_post', $data_insert2);}

        
		
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
		$this->load->view("template_rp",$view);*/
		
	}
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