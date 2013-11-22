<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_domain_new extends CI_Controller {
    
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
	


    
    public function index()
    {
          
        $now = date("Y-m-d");
        $yes = date("Y-m-d",strtotime("-1 day"));
        $avg = date("Y-m-d",strtotime("-6 day"));
         
        /*$sql = "select domain_name
         from monitor_post
         group by domain_name
         order by domain_name asc";*/
         $sql = "select domain_name,sum(request) as sumrequest
         from monitor_post
         where get_date BETWEEN '$avg' and '$now'
         group by domain_name
         order by sumrequest desc";  
        $query = $this->db->query($sql);
        $arr_domain = $query->result_array();
        /*
        foreach ($arr_domain as $key => $value) {
            $arr_data[] = $this->select_date_one($now,$value['domain_name']);
            $arr_Y = $this->select_date_yes($yes,$value['domain_name']);
            //$arr_7 = $this->select_date_one($avg,$now,$value['domain_name']);
            array_push($arr_data,$arr_Y);
        }
            print_r($arr_data);
        */
        $data['arr_report'] = $arr_domain;
        $view["module"] = $this->load->view("report_domain_new2",$data,true);
		$this->load->view("template_rp",$view);
    }
    
    public function select_date_one($date,$domain)
    {
        
        $sql = "select sum(request) as sum_request_n, sum(page_count) as sum_page_n, sum(post_count) as sum_post_n
        from monitor_post
        where domain_name = '$domain' and get_date = '$date'";
        $query = $this->db->query($sql);
        $arr = $query->result_array();
        return $arr[0];
    }
    
    public function select_date_yes($date,$domain)
    {
        
        $sql = "select sum(request) as sum_request_y, sum(page_count) as sum_page_y, sum(post_count) as sum_post_y
        from monitor_post
        where domain_name = '$domain' and get_date = '$date'";
        $query = $this->db->query($sql);
        $arr = $query->result_array();
        return $arr[0];
    }
    
    public function select_date_seven($date_start,$date_end,$domain)
    {
        
        $sql = "select sum(request) as sum_request_7, sum(page_count) as sum_page_7, sum(post_count) as sum_post_7
        from monitor_post
        where domain_name = '$domain' and get_date BETWEEN '$date_start' and '$date_end'"; //echo $sql;
        $query = $this->db->query($sql);
        $arr = $query->result_array();
        return $arr[0];
        
    }
    public function select_fetch_page($domain){
					
	$sql = "SELECT 	mu.domain_id,mu.last_insert_date,mu.latest_fetch,mu.parse_date,d.config_filename   
		FROM 	monitor_update_root mu,domain d 
		WHERE 	d.url LIKE '%$domain%'  AND  mu.domain_id = d.id AND d.status!='error'";
			
	$query = $this->db->query($sql);
	
	if($query->num_rows() > 0){
		$res = $query->row_array();
		$data = array("id"=>$res["domain_id"],"last_insert_date"=>$res["last_insert_date"],"latest_fetch"=>$res["latest_fetch"],"parse_date"=>$res["parse_date"]);
	}else{
		$data = array("id"=>0,"last_insert_date"=>0,"latest_fetch"=>0,"parse_date"=>0);
	}
			
	return 	$data;
	
	}
	public function get_color($date){
		
		$color = "";
		if(!empty($date)){
		
			$date = explode(" ",trim($date));
			$date = $date[0];
			
			if(!empty($date)){
			
				$dateNow = date("Y-m-d");
				$dateDiff = floor((strtotime($dateNow) - strtotime($date))/3600);
	
				if($dateDiff == 0){
					$color = "green";
				}else if($dateDiff == 24){
					$color = "green2";
				}else if($dateDiff == 48){
					$color= "green3";
				}else{
					$color = "red1";
				}
			}
		}

		return $color;
	}
	public function test_get_color($date){
		
		$color = "";
		
		$date = explode(" ",$date);
		$date = $date[0];
		
	$date = '2013-04-30';
		
		if(!empty($date)){
		
			$dateNow = date("Y-m-d");
			$dateDiff = floor((strtotime($dateNow) - strtotime($date))/3600);
			
	
			
			echo $dateDiff;
			if($dateDiff == 0){
				$color = "green1";
			}else if($dateDiff == 24){
				$color = "green2";
			}else if($dateDiff == 48){
				$color= "green3";
			}else{
				$color = "red1";
			}
		}

		return $color;
	}
}

?>