<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_warroom extends CI_Controller {
    
	var $warroom_db;
	
	public function init(){
		
		/*
		$config['hostname'] = "203.151.21.111";
		$config['username'] = "root";
		$config['password'] = "thtoolsth!";
			
		$config['hostname'] = "localhost";
		$config['username'] = "root";
		$config['password'] = "Cg3qkJsV";
		*/
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
		
		$this->warroom_db = $this->load->database($config,true);
	}     

	public function matchs($client_id){

		$this->init();
		$query2 = $this->warroom_db->get_where('clients', array('id' => $client_id));
		if ($query2->num_rows() > 0){
		   foreach ($query2->result() as $row){
		      $data["name"] = $row->name;
		   }
		}
		
		$data["client_id"] = $client_id;
		
		$view["module"] = $this->load->view('report_warroom',$data,true);
		$this->load->view("template_rp",$view);
	}
	public function getData(){
	   
		$this->init();
		$client_id = $this->input->get("client_id");
		
		$sql ="select c.name,s.subject,s.id as subject_id,sm.*
				,(select max(match_to) from status_match
					where client_id=c.id and subject_id=s.id) as match_to
			from clients c
			inner join subject s on s.client_id=c.id
			left join status_match sm on sm.subject_id=s.id and sm.client_id=c.id 
			and sm.match_to=(select max(match_to) from status_match where client_id=c.id)
			where c.id='".$client_id."' and matching_status !='disable'
			order by sm.id desc,subject asc";
		
		//echo $sql; exit;
		$query = $this->warroom_db->query($sql);

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