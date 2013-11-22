<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Match_warroom extends CI_Controller {
    
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
		#$config['hostname'] = "203.150.231.155";
		#$config['hostname'] = "localhost";
		$config['hostname'] = "27.254.81.6";
		$config['username'] = "root";
		$config['password'] = "usrobotic";
		
		$config['database'] = "warroom";
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
		date_default_timezone_set("Asia/Bangkok");
		$this->load->helper("url");
		
		$date_2h = strtotime("-2 hours", strtotime(date("Y-m-d H:i:s")));
		$date_diff_2hr =date("Y-m-d H:i:s", $date_2h);
		//echo 'date_2h='.$date_2h;		
		
		$sql ="SELECT c.id,c.name,MAX(s.post_date)AS match_to_last
				,(SELECT COUNT(1)FROM SUBJECT WHERE client_id=c.id AND subject.status ='enable') AS count_subject
				,(SELECT MIN(DATE(latest_matching))AS min_date FROM warroom.subject WHERE client_id=c.id AND subject.status ='enable') AS min_date	
				,(SELECT MAX(DATE(latest_matching))AS max_date FROM warroom.subject WHERE client_id=c.id AND subject.status ='enable') AS max_date
			FROM clients c
			INNER JOIN status_match_warroom s ON s.client_id=c.id
			GROUP BY c.id,c.name
			ORDER BY c.id ASC";
		
		
		//echo $sql; //exit;
		$query = $this->spider_db->query($sql);
		$count_client = $query->num_rows();
				
		$sql2 ="select * 
			from status_match 
			where is_matching='N'
			and date(start_datetime)=date(NOW())
			and match_all = match_insert and wpc_all = wpc_insert";
			
		$query2 = $this->spider_db->query($sql2);
		$count_complete = $query2->num_rows();	
			
		$sql3 ="select *
			from status_match 
			where is_matching='Y' 
			and start_datetime < '".$date_diff_2hr."'
			and date(start_datetime)=date(NOW())
			and (match_all != match_insert or wpc_all != wpc_insert)";
			
		$query3 = $this->spider_db->query($sql3);
		$count_fail = $query3->num_rows();
		
		$sql4 ="select max(update_date)as last_update from status_match_warroom"; 
			
		$query4 = $this->spider_db->query($sql4);
		$last_update = $query4->row()->last_update;
		
		$data = array();		
		$data["rp"] =$query->result_array();
		$data["count_client"] 	=$count_client; 
		$data["count_complete"] =$count_complete;
		$data["count_fail"] 	=$count_fail;
		$data["count_all"] 	=$count_complete + $count_fail;
		$data["last_update"] 	=$last_update;
		
		//echo "Date Diff = ".$this->DateDiff(,date("Y-m-d"))."<br>";
		
		$view["module"] = $this->load->view('clients_warroom',$data,true);
		$this->load->view("template_rp",$view);
	}
	
	public function DateDiff($strDate1,$strDate2)
	{
		return (strtotime($strDate2) - strtotime($strDate1))/( 60 * 60 * 24 );  // 1 day = 60*60*24
	}
	
	public function getData(){
	   
		//echo "==>"; exit;
		$this->init();
		$client_id = $this->input->get("client_id");
			
		/*		
		$sql ="SELECT COUNT(1) AS 'count_match',DATE_FORMAT(post_date,'%d-%b') AS 'post_date1'
			FROM message_client_".$client_id."
			WHERE DATE(post_date) BETWEEN DATE_SUB(CURDATE(),INTERVAL 16 DAY) AND DATE_SUB(CURDATE(),INTERVAL 3 DAY)
			GROUP BY DATE(post_date)
			ORDER BY DATE(post_date) ASC";
		*/	
		$sql ="SELECT match_amt AS 'count_match',DATE_FORMAT(post_date,'%d-%b') AS 'post_date1' 
			FROM status_match_warroom
			WHERE client_id='".$client_id."'
			AND DATE(post_date) BETWEEN DATE_SUB(CURDATE(),INTERVAL 13 DAY) AND CURDATE()
			ORDER BY DATE(post_date) ASC";
		
		//echo $sql; exit;
		$query = $this->spider_db->query($sql);
		
		//$data = array();		
		//$data = $query->result_array();		
		//echo json_encode($data);		
		
		$data ='';
		if ($query->num_rows() > 0)
		{
		   $i=0;
		   foreach ($query->result() as $row)
		   {
		      $i++;		      
		      $data .='[\''.$row->post_date1.'\', '.$row->count_match.']';
		      if($i < $query->num_rows()) $data .='#';
		   }
		}
		
		echo $data; //exit;
	}
	public function updateData(){
		
		
		$this->init();
		//$id = $this->input->get("id");
	   
		$sqlc ="SELECT * FROM clients WHERE status='enabled' ORDER BY id ASC";
			
		echo 'SQL Query ...'.$sqlc.PHP_EOL;	
		$queryc = $this->spider_db->query($sqlc); 
		
		foreach($queryc->result() as $valc){ 
			 
			$sql ="SELECT COUNT(1) AS 'count_match',DATE(post_date) AS 'post_date1'
					FROM message_client_".$valc->id." 
					WHERE DATE(post_date) BETWEEN DATE_SUB(CURDATE(),INTERVAL 7 DAY) AND CURDATE()
					GROUP BY DATE(post_date)
					ORDER BY DATE(post_date) ASC";
				
			//echo 'SQL Query ...'.PHP_EOL;	
			$query = $this->spider_db->query($sql);	
			
			$chart_data = array();
			foreach($query->result() as $val){
					
				$data = array();			
			
				$data["client_id"] 		= $valc->id;
				$data["post_date"] 		= $val->post_date1;
				$data["match_amt"] 		= $val->count_match;
				$data["update_date"] 	= date("Y-m-d H:i:s");
		
		
				$query_chk = $this->spider_db->get_where('status_match_warroom', array('client_id'=> $data["client_id"],'post_date'=> $data["post_date"]));
				
				echo "Found to date :".$query_chk->num_rows().PHP_EOL;
				if($query_chk->num_rows() > 0){
					//echo '==Update ='.$val->post_date1." ID :".$valc->id."".PHP_EOL;					
					$this->spider_db->update('status_match_warroom', $data, array('client_id'=> $data["client_id"],'post_date'=> $data["post_date"]));
				}
				else{
					//echo 'Insert ='.$data["post_date"]." ID :".$valc->id."".PHP_EOL;
					$insert_query = $this->spider_db->insert_string("status_match_warroom",$data);
					$this->spider_db->query($insert_query);
					//$insert_id =$this->db->insert_id();
				}
			}
		}
		echo "success";
	}
	
}

?>