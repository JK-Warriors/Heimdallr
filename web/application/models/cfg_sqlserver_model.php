<?php 
class cfg_sqlserver_model extends CI_Model{

    protected $table='db_cfg_sqlserver';
    protected $table_mirror='db_cfg_sqlserver_mirror';
    
	function get_total_rows(){
		$this->db->from($this->table);
        return $this->db->count_all_results();
	}
    
    function get_total_record(){
        $query = $this->db->get($this->table);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
    
    function get_total_record_usage(){
        $this->db->where('is_delete',0);
        $query = $this->db->get($this->table);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
    
    
    
    function get_total_record_paging($limit,$offset){
        $query = $this->db->get($this->table,$limit,$offset);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
    
    function get_total_record_sql($sql){
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
		{
			$result['datalist']=$query->result_array();
            $result['datacount']=$query->num_rows();
            return $result;
		}
    }
    
    
   	/*
	 * 根据id获取单条记录
	 */
	function get_record_by_id($id){
		$query = $this->db->get_where($this->table, array('id' =>$id));
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
	}
    
    function get_host_by_id($id)
    {
		$query = $this->db->get_where($this->table, array('id' =>$id));
		if ($query->num_rows() > 0)
		{
			 $result=$query->row_array();
             return $result['host'];
		}
	}
    
    function get_servers($server_id){
        $query = $this->db->get_where($this->table, array('id' =>$server_id));
		if ($query->num_rows() > 0)
		{
			$data=$query->row_array();
            return $data['host'].":".$data['port'];
		}
    }
    
    
    function mirror_name_exists($mirror_name,$id=''){
    	try{
    		if($id==''){
        	$sql="select * from db_cfg_sqlserver_mirror where is_delete=0 and mirror_name='$mirror_name'; ";
    		}else{
        	$sql="select * from db_cfg_sqlserver_mirror where is_delete=0 and mirror_name='$mirror_name' and id != $id; ";
    		}
    		
				$query = $this->db->query($sql);
				if($query->num_rows() > 0){
						return 1;
				}else{
						return 0;
				};
			}catch(Exception $e){
				errorLog($e->getMessage());
				return -1;
			}
    }
    
    
    function mirror_group_exists($primary_db, $standby_db, $db_name,$id=''){
    	try{
    		if($id==''){
        	$sql="select * from db_cfg_sqlserver_mirror where is_delete=0 and primary_db_id='$primary_db' and standby_db_id='$standby_db' and db_name='$db_name'; ";
    		}else{
        	$sql="select * from db_cfg_sqlserver_mirror where is_delete=0 and primary_db_id='$primary_db' and standby_db_id='$standby_db' and db_name='$db_name' and id != $id; ";
    		}
    		
		    $query = $this->db->query($sql);
				if($query->num_rows() > 0){
						return 1;
				}else{
						return 0;
				};
			}catch(Exception $e){
				errorLog($e->getMessage());
				return -1;
			}
    }
    
    
    /*
    * 插入数据
    */
   	public function insert($data){		
			$this->db->insert($this->table, $data);
		}
		
   	public function insert_mirror($data){		
			$this->db->insert($this->table_mirror, $data);
		}
    
    /*
	 * 更新信息
	*/
	public function update($data,$id){
		$this->db->where('id', $id);
		$this->db->update($this->table, $data);
	}
	
	public function update_mirror($data,$id){
		$this->db->where('id', $id);
		$this->db->update($this->table_mirror, $data);
	}
    
    /*
	 * 删除信息
	*/
	public function delete($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}
	
	public function delete_mirror($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table_mirror);
	}
	
	/*
	 * 删除db_status里面不监控的主机
	*/
	public function db_status_remove($id){
		$this->db->where('server_id', $id);
		$this->db->where('db_type', 'redis');
		$this->db->delete('db_status');
	}
    
}

/* End of file cfg_sqlserver_model.php */
/* Location: ./application/models/cfg_sqlserver_model.php */