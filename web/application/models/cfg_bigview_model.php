<?php 
class cfg_bigview_model extends CI_Model{

    function get_total_record_sql($sql){
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
				{
						$result['datalist']=$query->result_array();
            $result['datacount']=$query->num_rows();
            return $result;
				}
    }
    
    
	function get_total_db(){
		$sql="select id, 'oracle' as db_type, host, port, dsn, tags from db_cfg_oracle where is_delete=0 order by id asc";
    $query = $this->db->query($sql);
    
    if($query->num_rows() > 0)
    {
    	return $query->result_array();
    }
	}
	
	function get_total_os(){
		$sql="select * from db_cfg_os where is_delete=0 order by id asc";
    $query = $this->db->query($sql);
    
    if($query->num_rows() > 0)
    {
    	return $query->result_array();
    }
	}

	function get_unselect_db(){
		$sql="select o.id, 'oracle' as db_type, o.host, o.port, o.tags 
						from db_cfg_oracle o
						where o.is_delete=0 and o.id not in(select server_id from db_cfg_bigview where type='oracle' and metrix_name like 'center%')
						order by id;";
    $query = $this->db->query($sql);
    
    if($query->num_rows() > 0)
    {
    	return $query->result_array();
    }
	}
	    
	function get_select_db(){
		$sql="select * from db_cfg_bigview where metrix_name like 'center_db%' and server_id != -1 order by metrix_name;";
    $query = $this->db->query($sql);
    
    if($query->num_rows() > 0)
    {
    	return $query->result_array();
    }
	}
	
	function get_core_db(){
		$sql="select * from db_cfg_bigview where metrix_name = 'core_db';";
    $query = $this->db->query($sql);
    
    if($query->num_rows() > 0)
    {
    	return $query->result_array();
    }
	}
  
  
  function get_core_os(){
		$sql="select * from db_cfg_bigview where metrix_name = 'core_os';";
    $query = $this->db->query($sql);
    
    if($query->num_rows() > 0)
    {
    	return $query->result_array();
    }
	}
  
  /*
	 * 更新信息
	*/
	public function update_center_db($data, $metrix_name){
		$id = substr($data,0,strrpos($data, ":"));
		$db_type = substr($data,strrpos($data, ":") + 1);
		
		if($db_type == 'oracle'){
			$sql="update db_cfg_bigview b, db_cfg_oracle o
						set b.server_id = o.id, b.host = o.host, b.port = o.port, b.type = 'oracle', b.tags = o.tags
						where o.id = $id and b.metrix_name = '$metrix_name';";
	    $this->db->query($sql);
		}
		elseif($db_type == 'mysql'){
			$sql="update db_cfg_bigview b, db_cfg_mysql o
						set b.server_id = o.id, b.host = o.host, b.port = o.port, b.type = 'mysql', b.tags = o.tags
						where o.id = $id and b.metrix_name = '$metrix_name';";
		}
		elseif($db_type == 'sqlserver'){
			$sql="update db_cfg_bigview b, db_cfg_sqlserver o
						set b.server_id = o.id, b.host = o.host, b.port = o.port, b.type = 'sqlserver', b.tags = o.tags
						where o.id = $id and b.metrix_name = '$metrix_name';";
		}
	}

	
	public function update_core_db($data){
		$id = substr($data,0,strrpos($data, ":"));
		$db_type = substr($data,strrpos($data, ":") + 1);
		
		if($db_type == 'oracle'){
			$sql="update db_cfg_bigview b, db_cfg_oracle o
						set b.server_id = o.id, b.host = o.host, b.port = o.port, b.type = 'oracle', b.tags = o.tags
						where o.id = $id and b.metrix_name = 'core_db';";
	    $this->db->query($sql);
		}
		elseif($db_type == 'mysql'){
			$sql="update db_cfg_bigview b, db_cfg_mysql o
						set b.server_id = o.id, b.host = o.host, b.port = o.port, b.type = 'mysql', b.tags = o.tags
						where o.id = $id and b.metrix_name = 'core_db';";
		}
		elseif($db_type == 'sqlserver'){
			$sql="update db_cfg_bigview b, db_cfg_sqlserver o
						set b.server_id = o.id, b.host = o.host, b.port = o.port, b.type = 'sqlserver', b.tags = o.tags
						where o.id = $id and b.metrix_name = 'core_db';";
		}
	}
	
	
	public function update_core_os($id){
		$sql="update db_cfg_bigview b, db_cfg_os o
					set b.server_id = o.id, b.host = o.host, b.port = o.port, b.type = o.host_type, b.tags = o.tags
					where o.id = $id and b.metrix_name = 'core_os';";
    $this->db->query($sql);
	}
	
	
	public function clear_center_db($metrix_name){
		$sql="update db_cfg_bigview b
					set b.server_id = -1, b.host = '', b.port = '', b.type = '', b.tags = ''
					where b.metrix_name = '$metrix_name';";
    $this->db->query($sql);
	}
	
	public function clear_core_db(){
		$sql="update db_cfg_bigview b
					set b.server_id = -1, b.host = '', b.port = '', b.type = '', b.tags = ''
					where b.metrix_name = 'core_db';";
    $this->db->query($sql);
	}
	
	public function clear_core_os(){
		$sql="update db_cfg_bigview b
					set b.server_id = -1, b.host = '', b.port = '', b.type = '', b.tags = ''
					where b.metrix_name = 'core_os';";
    $this->db->query($sql);
	}
	

    

    
}

/* End of file cfg_os_model.php */
/* Location: ./application/models/cfg_os_model.php */