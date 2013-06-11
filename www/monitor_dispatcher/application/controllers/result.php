<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Result extends CI_Controller {
	
	var $spider_db;
	
	public function init(){
		$config['hostname'] = "203.151.21.111";
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
	    $data = array();
	    
	    $date = date("Y-m-d");
	    $sql ="SELECT * FROM monitor_fetch WHERE DATE(insert_date) = '$date' ";
	    $query = $this->db->query($sql);
	    
	    $result = array();
	    if($query->num_rows() > 0 ){
		$data["result"] = $query->row_array();
	    }
	    
	    $sql = "SELECT COUNT(id) as 'count' FROM domain";
	    $query = $this->db->query($sql);
	    $res = $query->row_array();
	    $data["website_total"] = $res["count"];
	      
	    $sql2 ="select * 
		    from status_match 
		    where is_matching='N'
		    and date(start_datetime)=date(NOW())
		    and match_all = match_insert and wpc_all = wpc_insert";
		      	    
	    $query2 = $this->spider_db->query($sql2);
	    $count_complete = $query2->num_rows();
	    
	    $date_2h = strtotime("-2 hours", strtotime(date("Y-m-d H:i:s")));
	    $date_diff_2hr =date("Y-m-d H:i:s", $date_2h);
	    //echo 'date_2h='.$date_2h;
		    
	    $sql3 ="select *
		    from status_match 
			where is_matching='Y' 
			and start_datetime < '".$date_diff_2hr."'
			and date(start_datetime)=date(NOW())
			and (match_all != match_insert or wpc_all != wpc_insert)";
		    
	    $query3 = $this->spider_db->query($sql3);
	    $count_fail = $query3->num_rows();
	    
	    $sql = "SELECT COUNT(client_id) as 'count' FROM clients ";
	    $query = $this->spider_db->query($sql);
	    $row = $query->row_array();
	    		
	    $data["count_client"] 	= $row["count"];
	    $data["count_complete"] 	= $count_complete;
	    $data["count_fail"] 	= $count_fail;
	    $data["count_all"] 		= $count_complete + $count_fail;
			    
	    $view["module"] = $this->load->view('result',$data,true);
	    $this->load->view("template_rp",$view);
	}
}

?>