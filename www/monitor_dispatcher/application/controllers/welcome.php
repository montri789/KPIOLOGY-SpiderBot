<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
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
		$this->load->helper("url");
		exit();
		$date_2h = strtotime("-2 hours", strtotime(date("Y-m-d H:i:s")));
		$date_diff_2hr =date("Y-m-d H:i:s", $date_2h);
		//echo 'date_2h='.$date_2h;
		
		$sql = "select *
			,(select case
					when 	(select count(id) from status_match 
							where client_id=clients.client_id
							and is_matching='N'
							group by match_to order by id desc limit 1) >=  
						(select count(id) from subject 
							where client_id=clients.client_id and matching_status !='disable') 
						then match_to
					else  match_from
					END AS last_match_date
				from status_match 
				where client_id=clients.client_id 
				order by id desc limit 1)as match_from
			,(select count(id)
				from subject where client_id=clients.client_id 
				and matching_status !='disable') as count_subject
			,(select count(id) 
				from status_match 
				where client_id=clients.client_id
				and is_matching='N'
				and date(start_datetime)=date(NOW())
				and match_all = match_insert and wpc_all = wpc_insert)as match_complete
			,(select count(id)
				from status_match 
				where client_id=clients.client_id
				and is_matching='Y' 
				and start_datetime < '".$date_diff_2hr."'
				and date(start_datetime)=date(NOW())
				and (match_all != match_insert or wpc_all != wpc_insert) )as match_fail				
			,(select count(id) 
				from status_match 
				where client_id=clients.client_id
				and is_matching='Y'
				and start_datetime >= '".$date_diff_2hr."'
				and date(start_datetime)=date(NOW())
				and (match_all != match_insert or wpc_all != wpc_insert) )as matching
			from clients
			order by client_id asc";		
			
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
		
		$data = array();		
		$data["rp"] =$query->result_array();
		$data["count_client"] 	=$count_client; 
		$data["count_complete"] =$count_complete;
		$data["count_fail"] 	=$count_fail;
		$data["count_all"] 	=$count_complete + $count_fail;
		
		$view["module"] = $this->load->view('clients',$data,true);
		$this->load->view("template_rp",$view);
	}
}

?>