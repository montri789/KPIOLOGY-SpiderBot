<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Result extends CI_Controller {
    
    function __construct()
	{
		parent::__construct();
        
        $arr = $this->session->userdata('logged_in');
           //print_r($arr);
           if(!isset($arr['email'])){
                    echo "<script>alert('Please Login @thothmedia.com');</script>";
                    redirect('login', 'refresh');
            }
		
	}
	
	var $spider_db;
	var $warroom_db;
	
	public function init(){
		
		$config['hostname'] = "27.254.81.15";
		//$config['hostname'] = "localhost";
		$config['username'] = "root";
		$config['password'] = "Cg3qkJsV";
		
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
		
		$config_wr['hostname'] = "27.254.81.6";
		$config_wr['username'] = "root";
		$config_wr['password'] = "usrobotic";
		
		$config_wr['database'] = "warroom";
		$config_wr['dbdriver'] = "mysql";
		$config_wr['dbprefix'] = "";
		$config_wr['pconnect'] = FALSE;
		$config_wr['db_debug'] = TRUE;
		$config_wr['cache_on'] = FALSE;
		$config_wr['cachedir'] = "";
		$config_wr['char_set'] = "utf8";
		$config_wr['dbcollat'] = "utf8_general_ci";
		
		$this->warroom_db = $this->load->database($config_wr,true);
	}	

	public function index(){

	    $this->init();	
		date_default_timezone_set("Asia/Bangkok");
	    $data = array();
	    
	    #Fetch Pages ====================================================================
	    $date = date("Y-m-d");
	    $sql ="SELECT * FROM monitor_fetch WHERE DATE(insert_date) = '$date' ";
	    $query = $this->spider_db->query($sql);
	    
	    $result = array();
	    if($query->num_rows() > 0 ){
		$data["result"] = $query->row_array();
	    }
	    
	    #Bot Matchs Kpiology ======================================================================
	    $sql = "SELECT COUNT(id) as 'count' FROM domain";
	    $query = $this->spider_db->query($sql);
	    $res = $query->row_array();
	    $data["website_total"] = $res["count"];
	      
	    $sql2 ="select * from status_match 
		    where is_matching='N'
		    and date(start_datetime)=date(NOW())
		    and match_all = match_insert and wpc_all = wpc_insert";
		      	    
	    $query2 = $this->spider_db->query($sql2);
	    $count_complete = $query2->num_rows();
	    
	    $date_2h = strtotime("-2 hours", strtotime(date("Y-m-d H:i:s")));
	    $date_diff_2hr =date("Y-m-d H:i:s", $date_2h);
	    //echo 'date_2h='.$date_2h;
		    
	    $sql3 ="select * from status_match 
			where is_matching='Y' 
			and start_datetime < '".$date_diff_2hr."'
			and date(start_datetime)=date(NOW())
			and (match_all != match_insert or wpc_all != wpc_insert)";
		    
	    $query3 = $this->spider_db->query($sql3);
	    $count_fail = $query3->num_rows();
	    
	    $sql = "SELECT COUNT(client_id) as 'count' FROM clients WHERE client_id in(select client_id from status_match)";
	    $query = $this->spider_db->query($sql);
	    $row = $query->row_array();
	    		
	    $data["count_client"] 	= $row["count"];
	    $data["count_complete"] 	= $count_complete;
	    $data["count_fail"] 	= $count_fail;
	    $data["count_all"] 		= $count_complete + $count_fail;
	    
	    #Bot Matchs Warroom ======================================================================
	    $sql = "SELECT COUNT(id) as 'count' FROM domain";
	    $query = $this->warroom_db->query($sql);
	    $res = $query->row_array();
	    $data["website_total"] = $res["count"];
	      
	    $sql2 ="select * from status_match 
		    where is_matching='N'
		    and date(start_datetime)=date(NOW())
		    and match_all = match_insert and wpc_all = wpc_insert";
		      	    
	    $query2 = $this->warroom_db->query($sql2);
	    $count_complete = $query2->num_rows();
	    
	    $date_2h = strtotime("-2 hours", strtotime(date("Y-m-d H:i:s")));
	    $date_diff_2hr =date("Y-m-d H:i:s", $date_2h);
	    //echo 'date_2h='.$date_2h;
		    
	    $sql3 ="select * from status_match 
			where is_matching='Y' 
			and start_datetime < '".$date_diff_2hr."'
			and date(start_datetime)=date(NOW())
			and (match_all != match_insert or wpc_all != wpc_insert)";
		    
	    $query3 = $this->warroom_db->query($sql3);
	    $count_fail = $query3->num_rows();
	    
	    $sql = "SELECT COUNT(1) as 'count' FROM clients WHERE id in(select client_id from status_match)";
	    $query = $this->warroom_db->query($sql);
	    $row = $query->row_array();
	    		
	    $data["count_wr_client"] 	= $row["count"];
	    $data["count_wr_complete"] 	= $count_complete;
	    $data["count_wr_fail"] 	= $count_fail;
	    $data["count_wr_all"] 		= $count_complete + $count_fail;
	    
	    #Bot Running Page ==================================================================
		$num_select_day = 2;
		$dateNow = date("Y-m-d");
		$dateEnd = date("Y-m-d",strtotime("-".$num_select_day." day"));
		/* -----bot count------*/
		$sql_bot = "select bot_name as name from monitor_date_post where get_date BETWEEN '".$dateEnd."' AND '".$dateNow."' group by bot_name";
		$query_bot = $this->spider_db->query($sql_bot);
		
		$arr_bot_count = $query_bot->result_array();
		
		/* -----bot running------*/
		$sql_running = "select bot_name as bot_running from monitor_date_post where get_date BETWEEN '".$dateEnd."' AND '".$dateNow."' group by bot_name";
		$query_running = $this->spider_db->query($sql_running);
		
		$arr_bot_running = $query_running->result_array();
		$bot_running = count($arr_bot_running);
		
		/* -----total post------*/
		$sql_sum = "select
				SUM(post_count) as post_count,
				SUM(page_count) as page_count
				from monitor_date_post
				WHERE get_date BETWEEN '".$dateEnd."' AND '".$dateNow."'
				order by get_date DESC limit 3";
		$query_sum = $this->spider_db->query($sql_sum);
		
		$arr_post_sum = $query_sum->result_array();
		$total_post = $arr_post_sum[0]["post_count"];
		$total_page = $arr_post_sum[0]["page_count"];
		
		$data["bot_running"] = $bot_running;
		$data["total_post"] = $total_post;
		$data["total_page"] = $total_page;
		$data["dir_bot"] = $arr_bot_count;
		
	    #Running Domain ======================================================================
		$now = date("Y-m-d");
		$yes = date("Y-m-d",strtotime("-1 day"));
		$avg = date("Y-m-d",strtotime("-6 day"));
		 
		 $sql_rundomain = "select domain_name,sum(request) as sumrequest
					from monitor_post
					where get_date BETWEEN '$avg' and '$now'
					group by domain_name
					order by sumrequest desc";
					
		$query_rundomain = $this->spider_db->query($sql_rundomain);
		$arr_domain = $query_rundomain->result_array();

		$data['arr_report'] = $arr_domain;
		
	    #Running Twitter ======================================================================
		$sql_chk ="select last_running from bot_status where bot_name = 'author_id_lookup' limit 1";
		$count_chk = $this->spider_db->query($sql_chk);
		$count_chk = $count_chk->row_array();
		if(is_array($count_chk)){
	
			$last_running =$count_chk["last_running"];
			
			$sql_chk2 = "SELECT count(id) as 'tweet_messages' FROM post_twitter_temp";
			$tweetleft = $this->spider_db->query($sql_chk2);
			$tweetleft = $tweetleft->row_array();
			//$tweetleft =$count_chk[0][3];

			$today = date("Y-m-d H:i:s");
	
			$to_time = strtotime($today);
			$from_time = strtotime($last_running);
			$diff = $from_time - $to_time;
			$diff = abs($diff)/60;
			if($diff > 15){
				$data["convertor"]["status"] = "down";
			}else{
				$data["convertor"]["status"] = "up";
			}
			
			$data["convertor"]["last_running"] = $last_running;
			$data["convertor"]["tweet_messages"] = number_format($tweetleft['tweet_messages']);
		}
		
		$skip = 0;

		$sql_chk3 ="select last_running,tweetleft  from bot_status
			    where bot_name = 'twitter_firehose' AND work_status = 'normal'
			    AND TIMESTAMPDIFF(MINUTE,last_running,NOW()) < 6 LIMIT 1";
		$count_chk3 = $this->spider_db->query($sql_chk3);
		
		if($count_chk3->num_rows() > 0){
			//normal
			$count_chk3 = $count_chk3->row_array();
			
			$data["firehose"]["tweetleft"] = number_format($count_chk3["tweetleft"]);
			$data["firehose"]["status"] = "up";
			$data["firehose"]["last_running"] = $count_chk3['last_running'];
			$skip = 1;
		}

		if($skip ==0){
			$sql_chk3 ="select last_running from bot_status
			             where bot_name = 'twitter_firehose' AND work_status = 'dead'
				     AND TIMESTAMPDIFF(MINUTE,last_running,NOW()) < 6 ORDER BY id DESC LIMIT 1";

			$count_chk3 = $this->spider_db->query($sql_chk3);
				
			if($count_chk3->num_rows() > 0){
				//dead
				$count_chk3 = $count_chk3->row_array();
				$data["firehose"]["status"] = "down";
				$data["firehose"]["last_running"] = $count_chk3['last_running'];
			}else{
	
				$sql_chk3 ="select last_running from bot_status
				            where bot_name = 'twitter_firehose' AND work_status = 'warning'
					    AND TIMESTAMPDIFF(MINUTE,last_running,NOW()) < 6 ORDER BY id DESC LIMIT 1";
		
				$count_chk3 = $this->spider_db->query($sql_chk3);
				
				if($count_chk3->num_rows() > 0){
					//warning
					$count_chk3 = $count_chk3->row_array();
					$data["firehose"]["status"] = "up";
					$data["firehose"]["warning"] = "warning";
					$data["firehose"]["last_running"] = $count_chk3['last_running'];
				}else{
					$sql = "SELECT tweetleft FROM bot_status WHERE work_status = 'normal' ";
					$query = $this->spider_db->query($sql);
					$res = $query->row_array();
					$data["tweetleft"] = number_format($res["tweetleft"]);
				}
			}
		}
	    #======================================================================
	    $data["chart_post"] =$this->get_post_insert();
	    $data["chart_pantip"] =$this->get_pantip_insert();
	    
        $data["graph_get_post"] =$this->graph_get_post();
	    $data["graph_get_page"] =$this->graph_get_page();
	    
	    $view["module"] = $this->load->view('result',$data,true);
	    $this->load->view("template_rp",$view);
	}
	
	public function select_date_one($date,$domain)
	{
	    
	    $sql = "select sum(request) as sum_request_n, sum(page_count) as sum_page_n, sum(post_count) as sum_post_n
	    from monitor_post
	    where domain_name = '$domain' and get_date = '$date'";
	    $query = $this->spider_db->query($sql);
	    $arr = $query->result_array();
	    return $arr[0];
	}
	
	public function select_date_yes($date,$domain)
	{
	    
	    $sql = "select sum(request) as sum_request_y, sum(page_count) as sum_page_y, sum(post_count) as sum_post_y
	    from monitor_post
	    where domain_name = '$domain' and get_date = '$date'";
	    $query = $this->spider_db->query($sql);
	    $arr = $query->result_array();
	    return $arr[0];
	}
	
	public function select_date_seven($date_start,$date_end,$domain)
	{
	    
	    $sql = "select sum(request) as sum_request_7, sum(page_count) as sum_page_7, sum(post_count) as sum_post_7
	    from monitor_post
	    where domain_name = '$domain' and get_date BETWEEN '$date_start' and '$date_end'";
	    $query = $this->spider_db->query($sql);
	    $arr = $query->result_array();
	    return $arr[0];
	    
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
	function get_post_insert(){
				
	    $sql ="SELECT DATE_FORMAT(post_date,'%d-%b') AS 'post_date1',post_amt AS 'count_post'
			FROM status_post 
			WHERE id > (select max(id) from status_post)-31
			ORDER BY post_date ASC";		
			
	    $query = $this->spider_db->query($sql);	
		
	    $chart_data = array();
	    foreach($query->result() as $val){
		$chart_data[] = "['".$val->post_date1."',".$val->count_post."]";
	    }
	    //print_r($chart_data); exit;
	    
	    return implode(",",$chart_data);
	}
	function get_pantip_insert(){
				
	    $sql ="SELECT DATE_FORMAT(insert_date,'%d-%b') AS 'insert_date1',page_amt AS 'count_page',post_amt AS 'count_post'
			FROM status_pantip
			WHERE id > (select max(id) from status_pantip)-31
			ORDER BY insert_date ASC";		
			
	    $query = $this->spider_db->query($sql);	
		
	    $chart_data = array();
	    foreach($query->result() as $val){
		$chart_data[] = "['".$val->insert_date1."',".$val->count_page.",".$val->count_post."]";
	    }
	    //print_r($chart_data); exit;
	    
	    return implode(",",$chart_data);
	}
    
    function graph_get_post(){
        
        $date_get_page = date("Y-m-d",strtotime("-15 day"));
        
        $sql = "select DATE_FORMAT(m1.date,'%d-%b') as dateFormat,date,
                sum( case when (m1.type = 'facebook' and m1.date = m1.date) then m1.countPage else '0' end ) as 'facebook',
                sum( case when (m1.type = 'post' and m1.date = m1.date) then m1.countPage else '0' end ) as 'post',
                sum( case when (m1.type = 'post_today_all' and m1.date = m1.date) then m1.countPage else '0' end ) as 'post_today_all'
                from monitor_get_page as m1
                where m1.date >= '$date_get_page'
                group by m1.date
                order by m1.date asc";
        $query = $this->db->query($sql);
        
        $graph_data = array();
        $graph_data[] = "['Date', 'Facebook', 'Post', 'Post/comment Today']";
	    foreach($query->result() as $val){
        $graph_data[] = "['".$val->dateFormat."', ".$val->facebook.", ".$val->post.", ".$val->post_today_all."]";
	    }
        
        return implode(",",$graph_data);
	}
    
    function graph_get_page(){
        
        $date_get_page = date("Y-m-d",strtotime("-15 day"));
        
        $sql = "select DATE_FORMAT(m1.date,'%d-%b') as dateFormat,date,
                sum( case when (m1.type = 'pageAll' and m1.date = m1.date) then m1.countPage else '0' end ) as 'pageAll',
                sum( case when (m1.type = 'pageNo0' and m1.date = m1.date) then m1.countPage else '0' end ) as 'pageNo0',
                sum( case when (m1.type = 'post_today' and m1.date = m1.date) then m1.countPage else '0' end ) as 'post_today'
                from monitor_get_page as m1
                where m1.date >= '$date_get_page'
                group by m1.date
                order by m1.date asc";
        $query = $this->db->query($sql);
        
        $graph_data = array();
        $graph_data[] = "['Date', 'Page All no pantip', 'Page post>0 no pantip', 'Post Today']";
	    foreach($query->result() as $val){
        $graph_data[] = "['".$val->dateFormat."', ".$val->pageAll.", ".$val->pageNo0.", ".$val->post_today."]";
	    }
        
        return implode(",",$graph_data);
	}
}

?>