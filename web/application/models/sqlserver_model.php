<?php 
class Sqlserver_model extends CI_Model{

	
    
    function get_total_record_sql($sql){
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
		{
			$result['datalist']=$query->result_array();
            $result['datacount']=$query->num_rows();
            return $result;
		}
    }
    
	
    function get_status_total_record($health=''){
        
        $this->db->select('*');
        $this->db->from('sqlserver_status ');
        if($health==1){
            $this->db->where("connect", 1);
        }
        
        !empty($_GET["host"]) && $this->db->like("host", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        if(!empty($_GET["order"]) && !empty($_GET["order_type"])){
            $this->db->order_by($_GET["order"],$_GET["order_type"]);
        }
        else{
            $this->db->order_by('tags asc');
        }
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
    }
    


	function get_status_chart_record($server_id,$time){
        $query=$this->db->query("select * from sqlserver_status_his  where server_id=$server_id and YmdHi=$time limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }
    
    function check_has_record($server_id,$time){
        $query=$this->db->query("select id from sqlserver_status_his where server_id=$server_id and YmdHi=$time");
        if ($query->num_rows() > 0)
        {
           return true; 
        }
        else{
            return false;
        }
    }

    function get_chart_data($server_id, $begin_time){
        $query=$this->db->query("SELECT *
																	FROM(SELECT DATE_FORMAT(h.ymdhi, '%Y-%m-%d %H:%i') time, h.*
																					FROM sqlserver_status_his h
																				 WHERE server_id = $server_id
																					 AND YmdHi >= DATE_ADD(sysdate(), INTERVAL -$begin_time minute)
																		) t
																	GROUP BY time");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_replication_total_record(){
        $host=isset($_GET["host"]) ? $_GET["host"] : "";
        $tags=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        $sql = "SELECT * from(select m.* 
															from sqlserver_mirror m, db_cfg_sqlserver s
															where m.server_id = s.id) t
													where 1=1 ";
													        
				if($host != ""){
						$sql = $sql . " AND (t.`host` like '%" . $host . "%')";
				}
				
				if($tags != ""){
						$sql = $sql . " AND (t.`tags` like '%" . $tags . "%')";
				}
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
}

/* End of file sqlserver_model.php */
/* Location: ./application/models/sqlserver_model.php */