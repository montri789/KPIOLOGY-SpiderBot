<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monitor_facebook extends CI_Controller {
    
    function __construct()
	{
		parent::__construct();
        
        $arr = $this->session->userdata('logged_in');
            if ($this->session->sess_expiration == 0){
                echo "<script>alert('Please Login @thothmedia.com');</script>";
                    redirect('login', 'refresh');
            }
           if(!isset($arr['email'])){
                    echo "<script>alert('Please Login @thothmedia.com');</script>";
                    redirect('login', 'refresh');
            }
		
	}
/*
    var $spider_db;
    
    public function init(){
		$config['hostname'] = "203.151.21.106";
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
		
		$this->spider_db = $this->load->database($config,true);
	}
*/
	public function index()
	{
       
       $sql = "select * from post_log_fb order by id desc limit 100";
       $query = $this->db->query($sql);
       $arr_facebook = $query->result_array();

        $data['arr_report'] = $arr_facebook;
        $view["module"] = $this->load->view("report_facebook",$data,true);
		$this->load->view("template_rp",$view);
    }
    
    public function get_report_date($date)
	{
        $start_date = $this->timemin($date.'- 7 hour');
        $end_date = $this->timemin($start_date.'+ 1 day - 1 sec');
        $sql = "select sum(sum_post) as sum_post from post_log_fb where start_date BETWEEN '$start_date' and '$end_date'";
        $query = $this->db->query($sql);
        $arr_sum_date = $query->result_array();
        
        return $arr_sum_date[0]['sum_post'];
    }
    
    public function get_page_facebook()
	{
	   $url = 'https://thothmedia:123456!@tothmedia.gnip.com/data_collectors/2/rules.json';
        $content = file_get_contents($url);
        $json = json_decode($content, true);
        
        return count($json['rules']);
    }
    
    function dateThaiEngTimeShort($date,$l){	
        if($l==1){
        	$_month_name = array("01"=>"ม.ค","02"=>"ก.พ","03"=>"มี.ค","04"=>"เม.ย","05"=>"พ.ค","06"=>"มิ.ย","07"=>"ก.ค","08"=>"ส.ค","09"=>"ก.ย","10"=>"ต.ค","11"=>"พ.ย","12"=>"ธ.ค");
        	$yy=substr($date,0,4);$mm=substr($date,5,2);$dd=substr($date,8,2);$time=substr($date,11,8);
        	$yy+=543;
        }else if($l==2){	
        	$_month_name = array("01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr","05"=>"May","06"=>"Jun","07"=>"Jul","08"=>"Aug","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec");
        	$yy=substr($date,0,4);$mm=substr($date,5,2);$dd=substr($date,8,2);$time=substr($date,11,8);
        }
        	$dateTE=intval($dd)." ".$_month_name[$mm]." ".$yy." , ".$time;
        	//$dateTE=intval($dd)." ".$_month_name[$mm]." ".$yy."";
        	return $dateTE;
        }
    public function timemin($timemin)
	{
        $time = strtotime($timemin);
        $adate = getdate($time);
        if($adate['mon']<10){$adate['mon']='0'.$adate['mon'];}
        if($adate['mday']<10){$adate['mday']='0'.$adate['mday'];}
        if($adate['hours']<10){$adate['hours']='0'.$adate['hours'];}
        if($adate['minutes']<10){$adate['minutes']='0'.$adate['minutes'];}
        if($adate['seconds']<10){$adate['seconds']='0'.$adate['seconds'];}
		$datetime = "$adate[year]-$adate[mon]-$adate[mday] $adate[hours]:$adate[minutes]:$adate[seconds]";
        return($datetime);
    }
 }