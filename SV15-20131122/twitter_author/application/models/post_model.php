<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post_model extends CI_Model {
	
	var $id;
	var $post_date;
	var $parse_date;
	var $page_id;
	var $type;
	var $author_id;
	var $title;
	var $body;
	var $segmented;
	var $is_segmented;
	var $tweet_id;
	var $reach_calculation_state;
	var $facebook_id;
	
	function __constuctor()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	function init($id=null)
	{
		$this->id = null;
		$this->post_date = null;
		$this->parse_date = null;
		$this->page_id = null;
		$this->type = null;
		$this->author_id = null;
		$this->title = null;
		$this->body = null;
		$this->sale = 0;
		$this->spam = 0;
		$this->segmented = null;
		$this->is_segmented = 0;
		$this->tweet_id = null;
		$this->reach_calculation_state = 'ready';
		$this->facebook_id = null;
		
		if($id!=null)
		{
			$query = $this->db->get_where('post',array('id'=>$id));
			
			if($query->num_rows())
			{
				$this->id = $query->row()->id;
				$this->post_date = $query->row()->post_date;
				$this->parse_date = $query->row()->parse_date;
				$this->page_id = $query->row()->page_id;
				$this->type = $query->row()->type;
				$this->author_id = $query->row()->author_id;
				$this->title = $query->row()->title;
				$this->body = $query->row()->body;
				$this->sale = $query->row()->sale;
				$this->spam = $query->row()->spam;
				$this->segmented = $query->row()->segmented;
				$this->is_segmented = $query->row()->is_segmented;
				$this->tweet_id = $query->row()->tweet_id;
				$this->reach_calculation_state = $query->row()->reach_calculation_state;
				$this->facebook_id = $query->row()->facebook_id;
			}
			return $this;
		}
	}
	
	function insert()
	{
		$this->db->insert('post',$this);
		log_message('info',"post model : inserted.");
		return $this->db->insert_id();
	}
	
	function insert_cache($cache)
	{
		// add obj to memcache
		$key = rand(1000,9999).'-'.microtime(true);
		$cache->add($key, $this, false, 12*60*60) or die ("Failed to save OBJECT at the server");
		echo '.';
	}
	
	function update()
	{
		$res = $this->db->update('post',$this,array('id'=>$this->id));
		log_message('info',"post model [".$this->id."]: updated.");
		return $res;
	}
	
	function delete()
	{
		$res = $this->db->delete('post',array('id'=>$this->id));
		log_message('info',"post model [".$this->id."]: deleted.");
		return $res;
	}
	
	function add_ext_url($url=null)
	{
		if($url == null) return false;
		
		$ext_url_id = $this->is_ext_url_exist($url);
		if($ext_url_id === false) //if not found existing insert new ext_url and get it
		{   
			$ext_url = array('url' => $url);
			$this->db->insert('ext_url',$ext_url);
			$ext_url_id = $this->db->insert_id();
		}	
		
		// insert in post_ext_url table
		$post_ext_url = array('post_id'=> $this->id, 'ext_url_id'=>$ext_url_id);
		$this->db->insert('post_ext_url',$post_ext_url);
		$post_ext_url_id = $this->db->insert_id();
	}
	
	function is_ext_url_exist($url)
	{
		$option = array('url'=>$url);
		$query = $this->db->get_where('ext_url',$option);
		if($query->num_rows())
		{
			return $query->row()->id;
		}
		else
		{
			return false;
		}
	}
	
	function is_author_exist($str)
	{
		$author = array('username' => $str,'channel'=>'twitter');
		$query = $this->db->get_where('author',$author);
		
		if($query->num_rows())
		{
			return $query->row()->id;
		}
		else
		{
			return false;
		}
	}
	
	function is_author_twitter_id_exist($author_id)
	{
    	$query = $this->db->get_where('author_twitter',array('author_id'=>$author_id));

		if($query->num_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function get_author_id($str=null,$follower=0)
	{
		if($str == null) return 0;
		if($follower == null) $follower = 0;
		
		$author_id = $this->is_author_exist($str);
		if($author_id == false) // if not found author create one
		{
//			log_message('info',"post model : new author : ".$str);
			$author = array ('username' => $str,'channel'=>'twitter','facebook_id'=>NULL);
			//$this->db->insert('author',$author);
			//$author_id = $this->db->insert_id();
			$this->db->insert('author',$author);
			$author_id = $this->db->insert_id();
			
		}
		
		if(!$this->is_author_twitter_id_exist($author_id))
		{
		    $author_twitter = array (
			 'author_id'=>$author_id,
			 'since_id'=>'0',
			 'tweet_update_state'=>'ready',
			 'tweet_update_date'=> date("Y-m-d H:i:s"),
			 'follower'=>$follower,
			 'follower_update_state'=>'ready',
			 'follower_update_date'=> date("Y-m-d H:i:s"));
			//$this->db->insert('author_twitter',$author_twitter);
			$this->db->insert('author_twitter',$author_twitter);
		}
		
		return $author_id;
	}
	
	function get_author_name()
	{
		$author_name = $this->custom_model->get_value('author','username',$this->author);
		return $author_name;
	}
	
	function get_page()
	{
		$page = new Page_model();
		$page->init($this->page_id);
		return $page;
	}
}