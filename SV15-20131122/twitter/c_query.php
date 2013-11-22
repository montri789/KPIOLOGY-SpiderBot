<?php
class c_db
{
    protected $db_host,$db_name,$db_user,$db_pass,$db_base,$use_transactions;
	
	public function __construct($s_host,$s_name,$s_user,$s_pass = null)
	{
		$this->db_host = $s_host;
		$this->db_name = $s_name;
		$this->db_user = $s_user;
		$this->db_pass = $s_pass;
	}
	
	public function db_start() 
	{  
    	$this->db_query('START TRANSACTION;');
    }
	
	public function db_close()
	{
		mysql_close();
	}
	
	public function db_commit()
	{
        return $this->db_query('COMMIT;');
	}
	
	public function db_rollback()
	{
		$this->db_query('ROLLBACK;');	
	}
	
	public function db_connect()
	{
		$login = mysql_connect($this->db_host,$this->db_user,$this->db_pass );
		if (!$login){ echo ('cannot connect database.'); exit; }
		
		mysql_select_db($this->db_name, $login);
		
		$login = $this->db_base;
		
		$this->use_transactions = true;
	}
	
	public function db_disconnect()
	{
		mysql_close($this->db_base);
	}
	
	public function insert_id()
	{
		return mysql_insert_id();	
	}
	
	public function db_query($qty)
	{
		$result = mysql_query('SET NAMES UTF8');
		$result = mysql_query($qty); 
		return $result;
	}
	
	private function set_datatype($data)
	{
		if (is_numeric($data))
		{
			$data_type = '"'.htmlspecialchars($data).'"'; 
		}
		else
		{
		
			switch ($data)
			{	
				case "date": $data_type = 'CURDATE()';
				break;
					
				case "datetime": $data_type = 'NOW()';
				break;
						
				case "time": $data_type = 'CURTIME()';
				break;
						
				default: $data_type = '"'.htmlspecialchars($data).'"'; 
				break;
			}		
		
		}
		
		return ($data_type);	
		
		
	}
	
	public function set_insert($tb_name,$db_field,$db_data)
	{	
	
		//$buffer = 'INSERT INTO '.$tb_name.'(' ;
		$buffer = 'INSERT IGNORE INTO '.$tb_name.'(' ;
			
		for ($i= 0;$i<count($db_field);$i++)
		{
			$buffer = $buffer.$db_field[$i];
			
			if ($i != (count($db_field)-1))
			{
				$buffer = $buffer.',';	
			}
		}
			
		$buffer = $buffer.') VALUES(' ;
		
		for ($i = 0;$i<count($db_data);$i++)
		{
			$buffer = $buffer.$this->set_datatype($db_data[$i]);
			
			if ($i != (count($db_data)-1))
			{
				$buffer = $buffer.',';	
			}
		}
			
		$qty = $buffer.')';
		
		//echo $qty; exit;
		
		$this->db_query($qty);	
	}
	
	public function set_update($tb_name,$db_field,$db_data,$ref_recname,$ref_data)
	{
		$buffer = 'UPDATE '.$tb_name.' SET ';
		
		for ($i=0;$i<count($db_field);$i++)
		{
			$buffer .= $db_field[$i].'='.$this->set_datatype(($db_data[$i]));
			
			if ($i != (count($db_data)-1))
			{
				$buffer .= ',';	
			}
		}

		$buffer .= ' WHERE '.$ref_recname.'= '.$this->set_datatype($ref_data);
		
		
		$qty = $buffer;
		
		
		//echo $qty.'<br/>';
		$this->db_query($qty);	
	}
	
	public function set_update_custom($tb_name,$db_field,$db_data,$action)
	{
		$buffer = 'UPDATE '.$tb_name.' SET ';
		
		for ($i=0;$i<count($db_field);$i++)
		{
			$buffer .= $db_field[$i].'='.$this->set_datatype(($db_data[$i]));
			
			if ($i != (count($db_data)-1))
			{
				$buffer .= ',';	
			}
		}

		$buffer .= ' WHERE '.$action;
	
		$qty = $buffer;
		
		$this->db_query($qty);	
	}
	
	public function set_update_array($tb_name,$db_field,$db_data,$tb_field,$arr_id)
	{
		if (count($arr_id) > 0)	
		{
			$sql = 'UPDATE '.$tb_name.' SET ';
		
			for ($i=0;$i<count($db_field);$i++)
			{
				$sql .= $db_field[$i].'='.$this->set_datatype(($db_data[$i]));
				
				if ($i != (count($db_data)-1))
				{
					$sql .= ',';	
				}
			}
	
			$sql .= ' WHERE ';
			
			//$count_max = count($arr_id)-1;
			
			for ($i = 0; $i < count($arr_id) ;$i++)
			{
				if ($i != (count($arr_id) -1) )
				{
					$sql .= '('.$tb_field.' = "'.$arr_id[$i].'") OR ';
				}
				else
				{
					$sql .= '('.$tb_field.' = "'.$arr_id[$i].'") ';
				}
			}
		
		
			/*foreach ($arr_id as $idx)
			{
				if ($arr_id[$count_max] == $idx)
				{
					$sql .= '('.$tb_field.' = "'.$idx.'") ';
				} 
				else
				{
					$sql .= '('.$tb_field.' = "'.$idx.'") OR ';
				}
			}	*/				
			
			
			$this->db_query($sql);
		}	
	}
	
	public function set_delete($tb_name,$ref_recname,$rec_data)
	{
		$buffer = 'DELETE FROM '.$tb_name.' WHERE '.$ref_recname.'="'.mysql_real_escape_string($rec_data).'" ';			
		$qty = $buffer;
	
	
		$this->db_query($qty);
	}
	
	public function set_delete_custom($tb_name,$action)
	{
		$buffer = 'DELETE FROM '.$tb_name.' WHERE '.action;			
		$qty = $buffer;
		$this->db_query($qty);
	}
	
	public function set_delete_array($tb_name,$tb_field,$arr_id)
	{
		if (count($arr_id) > 0)	
		{
			$sql = 'DELETE FROM '.$tb_name.' WHERE ';
		
			foreach ($arr_id as $idx)
			{
				if ($arr_id[count($arr_id)-1] == $idx)
				{
					$sql .= '('.$tb_field.' = "'.$idx.'") ';
				} 
				else
				{
					$sql .= '('.$tb_field.' = "'.$idx.'") OR ';
				}
			}		
			
			$this->db_query($sql);
		}	
	}
	
	public function set_public_array($tb_name,$tb_field,$arr_id)
	{
		if (count($arr_id) > 0)	
		{
			$sql = 'UPDATE '.$tb_name.' SET public = 1 WHERE ';
		
			foreach ($arr_id as $idx)
			{
				if ($arr_id[count($arr_id)-1] == $idx)
				{
					$sql .= '('.$tb_field.' = "'.$idx.'") ';
				} 
				else
				{
					$sql .= '('.$tb_field.' = "'.$idx.'") OR ';
				}
			}		
			
			$this->db_query($sql);
		}
	}
	
	public function set_unpublic_array($tb_name,$tb_field,$arr_id)
	{
		if (count($arr_id) > 0)	
		{
			$sql = 'UPDATE '.$tb_name.' SET public = 0 WHERE ';
		
			foreach ($arr_id as $idx)
			{
				if ($arr_id[count($arr_id)-1] == $idx)
				{
					$sql .= '('.$tb_field.' = "'.$idx.'") ';
				} 
				else
				{
					$sql .= '('.$tb_field.' = "'.$idx.'") OR ';
				}
			}		
			
			$this->db_query($sql);
		}
	}
	
	public function db_count($tb_name)
	{
		$buffer = 'SELECT COUNT(*) max_page FROM '.$tb_name;
		$qty = $buffer;
		$result = $this->db_query($qty);
		
		while ($record = mysql_fetch_array($result))
		{
			$count_page = $record[max_page];
		}

		return $count_page;
	}
	
	public function db_max($rec,$table)
	{
		$qty = 'SELECT MAX('.$rec.')+1 idd FROM '.$table;
		
		$result = $this->db_query($qty);
		
		while ($record = mysql_fetch_array($result))
		{
			$idx = $record[idd];
		}
		
		if ($idx == 0) $idx = 1;
		
		if ($idx == NULL) $idx = 1;
		
		if ($idx == '') $idx = 1;
		
		return $idx;
	}	
		
	public function db_newid($field)
	{
		$sql = 'SELECT process_id+1 idd FROM '._DB_PREFIX_TABLE.'refid WHERE ref_name ="'.$field.'" ';
		
		$result = $this->db_query($sql);
		while ($record = mysql_fetch_array($result))
		{
			return $idd = $record[idd];	
		}		
	}
	
	public function db_updateid($num,$name)
	{
		$this->set_update(_DB_PREFIX_TABLE.'refid',array('process_id'),array($num),'ref_name',$name);	
	}
	
	
	/* 2012-01-05 */
	public function page_count($name)
	{
		$sql = 'SELECT COUNT(*) dd FROM '._DB_PREFIX_TABLE.$name	;
		
		$result = $this->db_query($sql);
		
		while ($record = mysql_fetch_array($result))
		{
			$dd = $record[dd];	
		}
		
		return $dd;
	}
	
	public function view_select($raw)
	{
		$sql = 'SELECT ';
	
		foreach ($raw[rec] as $rec)
		{
			$sql .= $rec.',';		
		}
		
		$sql = substr($sql,0,strlen($sql)-1).' ';
		
		$sql .= 'FROM '.$raw[field].' ';
		
		if (!empty($raw[compare]))
		{
				
			$sql .= 'WHERE UPPER('.$raw[compare].') = UPPER("'.$raw[id].'") ';
		}
	
		$sql .= 'ORDER BY ';
		
		foreach ($raw[rec] as $rec)
		{
			$sql .= $rec.',';		
		}
		$sql = substr($sql,0,strlen($sql)-1);


		$result = $this->db_query($sql);
		
		return $result;
	}
	
	public function fetch_select($raw)
	{
		$sql = 'SELECT ';
		
		foreach ($raw[rec] as $rec)
		{
			$sql .= $rec.',';		
		}
		
		$sql = substr($sql,0,strlen($sql)-1).' ';
		
		$sql .= 'FROM '.$raw[field].' ';
		$sql .= 'ORDER BY ';
		
		foreach ($raw[rec] as $rec)
		{
			$sql .= $rec.',';		
		}
		$sql = substr($sql,0,strlen($sql)-1);
		$result = $this->db_query($sql);
		
		return $result;
		
	}
	
	public function fetch_query_one($sql)
	{
		$result = $this->db_query($sql); 

		if(is_array($result)){
			while ($record = mysql_fetch_array($result))
			{
				$rec = $record;
			}
		}
		else{
			$rec = 0;
		}

		return $rec;
	}
	
	public function fetch_query($sql)
	{
		$result = $this->db_query($sql); $i = 0;
		
		while ($record = mysql_fetch_array($result))
		{
			$rec[$i] = $record ;
			$i++;
		}
		
		return $rec;
	}
	/* 2012-01-05 */
}
?>