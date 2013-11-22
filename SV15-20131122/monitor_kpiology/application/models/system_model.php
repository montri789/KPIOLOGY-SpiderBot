<?PHP if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 class System_model extends CI_Model{
    
    function checkLogin()
	{
		if($this->session->userdata("wr_user_id") == NULL)
		{
	    	redirect("/");
		}
    }
    function checkRole($module)
	{
        
    }
    function dateFormat($date,$type="long")
	{
		$date = explode(" ",$date);
		
		if(count($date) == 1 || $type=="short")
		{
			$d = explode("-",$date[0]);
			return $d[2]."-".$d[1]."-".$d[0];
		}
		else
		{
			$d = explode("-",$date[0]);
			return $d[2]."-".$d[1]."-".$d[0]." ".$date[1];
		}
    }
 }

?>