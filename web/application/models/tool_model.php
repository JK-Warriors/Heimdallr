<?php 
class Tool_model extends CI_Model{

    protected $table='db_status';
    

    
    /*
	 * 获取db_list
	 */
	function get_db_list(){
        $host=isset($_GET["host"]) ? $_GET["host"] : "";
        $tags=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $db_type=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        
        $sql = "SELECT * from db_status t where 1=1 ";
				if($host != ""){
						$sql = $sql . " AND (t.`host` like '%" . $host . "%')";
				}
				if($tags != ""){
						$sql = $sql . " AND (t.`tags` like '%" . $tags . "%')";
				}
				if($db_type != ""){
						$sql = $sql . " AND (t.`db_type` like '%" . $db_type . "%')";
				}
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
        
	}

	function get_ora_conn_str_by_id($id){
        $sql = "select concat(host, ':', port, '/', dsn) as conn_str from db_cfg_oracle where id = ". $id . "; ";
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->conn_str;
        }
	}
	
	function get_ora_username_by_id($id){
        $sql = "select username from db_cfg_oracle where id = ". $id . "; ";
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->username;
        }
	}

	function get_ora_passwd_by_id($id){
        $sql = "select password from db_cfg_oracle where id = ". $id . "; ";
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->password;
        }
	}
	
}

/* End of file tool_model.php */
/* Location: ./application/models/tool_model.php */
