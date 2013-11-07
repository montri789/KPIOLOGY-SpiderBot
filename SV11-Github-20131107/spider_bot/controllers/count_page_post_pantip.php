<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Count_page_post_pantip extends CI_Controller {

	function index()
	{

		$sql ="SELECT count(1) AS 'count_page',date(insert_date) AS 'insert_date1'
				FROM page 
				WHERE domain_id=212 
				AND DATE(insert_date) BETWEEN DATE_SUB(CURDATE(),INTERVAL 5 DAY) AND CURDATE()
				GROUP BY insert_date1
				ORDER BY insert_date1";
			
		echo 'SQL Query ...'.PHP_EOL;	
	    $query = $this->db->query($sql);	
		
	    $chart_data = array();
	    foreach($query->result() as $val){
					
				$query_post = "SELECT COUNT(1) AS 'count_post',DATE(post_date) AS 'post_date1'
								FROM post 
								RIGHT JOIN page ON post.page_id = page.id 
								WHERE page.domain_id=212 
								AND DATE(post.post_date)='".$val->insert_date1."'";
								
				$post =$this->db->query($query_post);								
				
				$data = array();						
				$data["insert_date"] 	= $val->insert_date1;
				$data["page_amt"] 		= $val->count_page;
				$data["post_amt"] 		= $post->row()->count_post;
				
				$query_chk = $this->db->get_where('status_pantip',array('insert_date'=> $data["insert_date"]));		
				echo "Found to date :".$query_chk->num_rows().PHP_EOL;
				
				if($query_chk->num_rows() > 0){
					echo 'Update ='.$val->insert_date1.' /page_amt='.$val->count_page.' /post_amt='.$post->row()->count_post.PHP_EOL;
					$sql_up ="update status_pantip set page_amt=".$val->count_page." ,post_amt=".$post->row()->count_post." where insert_date='".$val->insert_date1."'";
					$this->db->query($sql_up);
				}
				else{
					echo 'Insert ='.$val->insert_date1.' /page_amt='.$val->count_page.' /post_amt='.$post->row()->count_post.PHP_EOL;
					$insert_query = $this->db->insert_string("status_pantip",$data);
					$this->db->query($insert_query);
					
				}
	    }
	}

}