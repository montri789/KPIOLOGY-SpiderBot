<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {
	
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
		
		$sql = "SELECT id,name FROM domain WHERE `status` = 'idle' ORDER BY id";
		$query = $this->spider_db->query($sql);
		
		$data["website"] = $query->result_array();
		$query->free_result();
		
		$date = date("Y-m-d");
		$sql ="SELECT * FROM monitor_fetch WHERE DATE(insert_date) = '$date' ";
		$query = $this->db->query($sql);
		
		$result = array();
		if($query->num_rows() > 0 ){
			$data["result"] = $query->row_array();
		}
		
		$view["module"] = $this->load->view("page",$data,true);
		$this->load->view("template_rp",$view);
	}
	public function website(){
		$this->init();
		$this->load->helper("json");
		
		$id = $this->input->get("id");
		if(!empty($id)){
			$data = array();
			$id = explode(",",$id);
			foreach($id as $row){
				if(!empty($row)){
					$sql = "SELECT 	domain.id,MAX(page.insert_date) as 'insert_date',MAX(page.latest_fetch) as 'latest_fetch' 
						FROM 	domain,page 
						WHERE 	domain.id = page.domain_id 
							AND domain.id = $row AND parent_page_id != 0 ";  
						//AND parent_page_id != 0 
					$query = $this->spider_db->query($sql);
					
					if($query->num_rows() > 0){
						$res = $query->row_array();
						array_push($data,array(	"id"=>$row,"insert_date"=>$res["insert_date"],"latest_fetch"=>$res["latest_fetch"]));
					}else{
						array_push($data,array(	"id"=>$row,"insert_date"=>0,"latest_fetch"=>0));
					}
				}
			}
			
			echo json_encode($data);
		}
	}
	public function update(){
		$g1 = $this->input->post("g1");
		$g2 = $this->input->post("g2");
		$g3 = $this->input->post("g3");
		$g4 = $this->input->post("g4");
		$g5 = $this->input->post("g5");
		$g6 = $this->input->post("g6");
		
		$date = date("Y-m-d H:i:s");
		
		$data = array();
		$data["insert_date"] = $date;
		$data["result1"] = $g1;
		$data["result2"] = $g2;
		$data["result3"] = $g3;
		$data["result4"] = $g4;
		$data["result5"] = $g5;
		$data["result6"] = $g6;
		

		$sql = "SELECT id FROM monitor_fetch WHERE DATE(insert_date) = DATE('$date')  ";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$res = $query->row_array();
			$this->db->update("monitor_fetch",$data,array("id"=>$res["id"]));
		}else{
			$this->db->insert("monitor_fetch",$data);
		}	
	}
}
?>