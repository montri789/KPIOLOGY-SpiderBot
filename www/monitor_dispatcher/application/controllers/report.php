<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {
    
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

	public function matchs($client_id){

		$this->init();
		$query2 = $this->spider_db->get_where('clients', array('client_id' => $client_id));
		if ($query2->num_rows() > 0){
		   foreach ($query2->result() as $row){
		      $data["client_name"] = $row->client_name;
		   }
		}
		
		$data["client_id"] = $client_id;
		
		$view["module"] = $this->load->view('report',$data,true);
		$this->load->view("template_rp",$view);
	}
	public function getData(){
	   
		$this->init();
		$client_id = $this->input->get("client_id");
		
		$sql ="select c.client_name,s.subject,sm.*
			from clients c
			inner join subject s on s.client_id=c.client_id
			left join status_match sm on sm.subject_id=s.id
			where c.client_id='".$client_id."' and matching_status !='disable'
			order by sm.id desc,subject asc";
		
	
		$query = $this->spider_db->query($sql);

		$data = array();		
		$data["rp"] =$query->result_array();
		
		echo json_encode($data);
	}
	public function logout(){

		$this->session->unset_userdata('wr_user_id');
		$this->session->unset_userdata('wr_client_id');
		$this->session->unset_userdata('wr_username');
		$this->session->unset_userdata('wr_groups');
				
		$this->index();
	}
}

?>