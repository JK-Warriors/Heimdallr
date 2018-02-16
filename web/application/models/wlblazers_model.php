<?php 
class Wlblazers_model extends CI_Model{

    protected $table='wlblazers_status';
    
	
   	/*
	 * 获取选项
	 */
	function get_wlblazers_status(){
		$query = $this->db->get($this->table);
		if ($query->num_rows() > 0)
		{
	        $result=$query->result_array();
            foreach($result as $r){
                $variables=$r['wl_variables'];
                $value=$r['wl_value'];
                $data[$variables]=$value;
		
            }
         
            return $data;
		}
	}
    
    /*
	 * 获取单个选项
	 */
	function get_wlblazers_item($key){
        $this->db->where('wl_variables',$key);
		$query = $this->db->get($this->table);
		if ($query->num_rows() > 0)
		{
	        $result=$query->row_array();
            if($result){
                return $result['value'];
            }
		}
	}
    
    /*
	 * 获取db_status
	 */
	function get_db_status(){
        
        $this->db->select('*');
        $this->db->from('db_status ');
        
        !empty($_GET["db_type"]) && $this->db->where("db_type", $_GET["db_type"]);
        !empty($_GET["host"]) && $this->db->like("host", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        if(!empty($_GET["order"]) && !empty($_GET["order_type"])){
            $this->db->order_by($_GET["order"],$_GET["order_type"]);
        }
        else{
            $this->db->order_by('db_type_sort asc,host asc');
        }
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
        
	}
    
    

	
    
}

/* End of file wlblazers_model.php */
/* Location: ./application/models/wlblazers_model.php */