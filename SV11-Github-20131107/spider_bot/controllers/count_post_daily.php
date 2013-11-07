<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Count_post_daily extends CI_Controller {

	function Index()
	{
		//$sql ="SELECT COUNT(post.id) AS 'count_post',DATE_FORMAT(post_date,'%d-%b') AS 'post_date1' 
		$sql ="SELECT COUNT(post.id) AS 'count_post',DATE(post_date) AS 'post_date1' 
			FROM post 
			WHERE DATE(post_date) BETWEEN DATE_SUB(CURDATE(),INTERVAL 7 DAY) AND CURDATE()
			GROUP BY DATE(post_date)
			ORDER BY post_date ASC";
			
			//WHERE DATE(post_date)='2013-06-30'
			
		echo 'SQL Query ...'.PHP_EOL;	
	    $query = $this->db->query($sql);	
		
	    $chart_data = array();
	    foreach($query->result() as $val){
				
				$data = array();			
			
				$data["post_date"] 		= $val->post_date1;
				$data["post_amt"] 		= $val->count_post;
				
				$query_chk = $this->db->get_where('status_post',array('post_date'=> $data["post_date"]));
		
				echo "Found to date :".$query_chk->num_rows().PHP_EOL;
				if($query_chk->num_rows() > 0){
					echo 'Update ='.$val->post_date1.PHP_EOL;
					$sql_up ="update status_post set post_amt=".$val->count_post." where post_date='".$val->post_date1."'";
					$this->db->query($sql_up);
				}
				else{
					echo 'Insert ='.$data["post_date"].PHP_EOL;
					$insert_query = $this->db->insert_string("status_post",$data);
					$this->db->query($insert_query);
					//$match_id =$this->db->insert_id();
				}
	    }
	}
}