<?PHP if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 class Twitter_author_model extends CI_Model{
    
    var $hash;
    
    function __constuctor()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function init()
    {
        $this->hash = array();
    }
    
    function insert($id, $username)
    {
        $this->hash[$id] = $username;
    }
    
    function get_id($username)
    {
        return array_search($username, $this->hash);
    }
    
    function dump()
    {
        var_dump($this->hash);
    }
    
    function count()
    {
        return count($this->hash);
    }
}
?>