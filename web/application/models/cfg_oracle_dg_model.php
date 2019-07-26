<?php 
class cfg_oracle_dg_model extends CI_Model{

    protected $table='db_cfg_oracle_dg';
    
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

    
    /*
    * 插入数据
    */
   	public function insert($data){		
		$this->db->insert($this->table, $data);
		}
    
    /*
		 * 更新信息
		*/
		public function update($data,$id){
			$this->db->where('id', $id);
			$this->db->update($this->table, $data);
		}
	    
	    /*
		 * 删除信息
		*/
		public function delete($id){
			$this->db->where('id', $id);
			$this->db->delete($this->table);
		}
	
	
    function name_exists($group_name,$id=''){
    	try{
    		if($id==''){
        	$sql="select * from db_cfg_oracle_dg where is_delete=0 and group_name='$group_name'; ";
    		}else{
        	$sql="select * from db_cfg_oracle_dg where is_delete=0 and group_name='$group_name' and id != $id; ";
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
    
    
    function group_exists($primary_db, $standby_db, $id=''){
    	try{
    		if($id==''){
        	$sql="select * from db_cfg_oracle_dg where is_delete=0 and primary_db_id='$primary_db' and standby_db_id='$standby_db'; ";
    		}else{
        	$sql="select * from db_cfg_oracle_dg where is_delete=0 and primary_db_id='$primary_db' and standby_db_id='$standby_db' and id != $id; ";
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
    
 
 
}

/* End of file cfg_mongodb_model.php */
/* Location: ./application/models/cfg_mongodb_model.php */