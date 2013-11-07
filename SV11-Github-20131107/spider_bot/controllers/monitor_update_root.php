<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class Monitor_update_root extends CI_Controller {

	function index()
	{
		echo "Monitor update root running...\n";

		$sql = "SELECT 	domain.id,domain.name,MAX(page.insert_date) as 'insert_date',MAX(page.latest_fetch) as 'latest_fetch' 
		,MAX(page.parse_date) as 'parse_date' 
						FROM 	domain,page 
						WHERE 	domain.id = page.domain_id 
							AND  root_page = 0 AND domain.status = 'idle' GROUP BY domain.id   ";  


		$query = $this->db->query($sql);
		foreach($query->result_array() as $row){
			$data = array();
			$data["last_insert_date"] = $row["insert_date"];
			$data["latest_fetch"] = $row["latest_fetch"];
			$data["parse_date"] = $row["parse_date"];

            $sql2 = "SELECT id FROM monitor_update_root WHERE domain_id = ".$row["id"];
			$check_query = $this->db->query($sql2);
			if($check_query->num_rows() > 0){
				$this->db->update("monitor_update_root",$data,array("domain_id"=>$row["id"]));
				///echo "update domain $row[id]/ $row[name] \n";
			}else{
				$data["domain_id"] = $row["id"];
				$data["name"] = $row["name"];

				$this->db->insert("monitor_update_root",$data);
				///echo "insert domain $row[id]/ $row[name] \n";
			}

		}

		echo "update success";
	}
	
	

}
// End File Monitor.php
// File Source /system/application/controllers/Monitor.php