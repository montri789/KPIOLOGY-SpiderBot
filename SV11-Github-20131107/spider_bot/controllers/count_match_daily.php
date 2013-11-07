<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Count_match_daily extends CI_Controller {

	function Index()
	{
		//$sql ="SELECT COUNT(post.id) AS 'count_post',DATE_FORMAT(post_date,'%d-%b') AS 'post_date1' 
		$sql ="SELECT COUNT(post.id) AS 'count_match',DATE(post_date) AS 'post_date1',subject.`client_id` 
				FROM post 
				RIGHT JOIN matchs ON post.id = matchs.post_id 
				RIGHT JOIN spider.`subject` ON matchs.subject_id = subject.id 
				WHERE DATE(post_date) BETWEEN DATE_SUB(CURDATE(),INTERVAL 7 DAY) AND CURDATE()
				GROUP BY subject.`client_id`,DATE(post_date)
				ORDER BY DATE(post_date) ASC,subject.`client_id`";
			
		echo 'SQL Query ...'.PHP_EOL;	
	    $query = $this->db->query($sql);	
		
	    $chart_data = array();
	    foreach($query->result() as $val){
				
			$data = array();			
		
			$data["client_id"] 		= $val->client_id;
			$data["post_date"] 		= $val->post_date1;
			$data["match_amt"] 		= $val->count_match;
			$data["update_date"] 	= date("Y-m-d H:i:s");
	
	
			$query_chk = $this->db->get_where('status_match_kpiology', array('client_id'=> $data["client_id"],'post_date'=> $data["post_date"]));
			
			echo "Found to date :".$query_chk->num_rows().PHP_EOL;
			if($query_chk->num_rows() > 0){
				echo '==Update ='.$val->post_date1." ID :".$val->client_id."".PHP_EOL;
				/*
				$sql_up ="update status_match_kpiology set match_amt=".$val->count_match." where post_date='".$val->post_date1."' and client_id='".$val->client_id."'";
				$this->db->query($sql_up);
				*/
				$this->db->update('status_match_kpiology', $data, array('client_id'=> $data["client_id"],'post_date'=> $data["post_date"]));
			}
			else{
				echo 'Insert ='.$data["post_date"]." ID :".$val->client_id."".PHP_EOL;
				$insert_query = $this->db->insert_string("status_match_kpiology",$data);
				$this->db->query($insert_query);
				//$insert_id =$this->db->insert_id();
			}
	    }
	}
}