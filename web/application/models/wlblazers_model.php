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
    

   /*
	 * 获取 oracle 相关统计信息
	 */
	function get_oracle_cfg_total(){
    $sql = "SELECT co.id
								FROM db_cfg_oracle co
								WHERE co.is_delete = 0
								AND co.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}
	
	function get_oracle_active_count(){
    $sql = "SELECT os.server_id
								FROM oracle_status os, db_cfg_oracle co
								WHERE os.server_id = co.id
								AND os.connect = 1
								AND co.is_delete = 0
								AND co.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}
	
	function get_oracle_inactive_count(){
    $sql = "SELECT os.server_id
								FROM oracle_status os, db_cfg_oracle co
								WHERE os.server_id = co.id
								AND os.connect != 1
								AND co.is_delete = 0
								AND co.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}

	function get_oracle_lines(){
    $sql = "SELECT DISTINCT server_id
							FROM oracle_status_his
							WHERE database_role = 'PHYSICAL STANDBY'
							AND create_time > date_add(sysdate(), INTERVAL - 1 DAY)";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	


	function get_oracle_yAxis(){
    $sql = "select *
						  from (SELECT server_id, create_time time, dg_delay delay
						          FROM oracle_status_his
						         WHERE database_role = 'PHYSICAL STANDBY'
                       AND server_id in (select id from db_cfg_oracle)
						           AND create_time > date_add(sysdate(), INTERVAL - 1 DAY)
						         order by server_id, time desc limit 1440) t
						 order by time";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
	
   /*
	 * 获取 mysql 相关统计信息
	 */
	function get_mysql_cfg_total($key){
    $sql = "SELECT cm.id
								FROM db_cfg_mysql cm
								WHERE cm.is_delete = 0
								AND cm.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}
	
	function get_mysql_active_count($key){
    $sql = "SELECT ms.server_id
								FROM mysql_status ms, db_cfg_mysql cm
								WHERE ms.server_id = cm.id
								AND ms.connect = 1
								AND cm.is_delete = 0
								AND cm.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}
	
	function get_mysql_inactive_count($key){
    $sql = "SELECT ms.server_id
								FROM mysql_status ms, db_cfg_mysql cm
								WHERE ms.server_id = cm.id
								AND ms.connect != 1
								AND cm.is_delete = 0
								AND cm.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}
	
	
   /*
	 * 获取 sqlserver 相关统计信息
	 */
	function get_sqlserver_cfg_total($key){
    $sql = "SELECT cs.id
								FROM db_cfg_sqlserver cs
								WHERE cs.is_delete = 0
								AND cs.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}
	
	function get_sqlserver_active_count($key){
    $sql = "SELECT ss.server_id
								FROM sqlserver_status ss, db_cfg_sqlserver cs
								WHERE ss.server_id = cs.id
								AND ss.connect = 1
								AND cs.is_delete = 0
								AND cs.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}
	
	function get_sqlserver_inactive_count($key){
    $sql = "SELECT ss.server_id
								FROM sqlserver_status ss, db_cfg_sqlserver cs
								WHERE ss.server_id = cs.id
								AND ss.connect != 1
								AND cs.is_delete = 0
								AND cs.monitor = 1";
		$count = $this->db->query($sql)->num_rows();
		return $count;
	}


   /*
	 * 获取 容灾库 延时信息
	 */
	function get_db_count_normal($db_type){
    $sql = "select id from db_status t where db_type = '$db_type' and role = 's' and t.repl = 1 and t.repl_delay = 1 ";
		$query = $this->db->query($sql);
		
		return $query->num_rows();
	}

	function get_db_count_waring($db_type){
    $sql = "select id from db_status t where db_type = '$db_type' and role = 's' and ((t.repl = 2 and t.repl_delay not in(-1, 3)) or t.repl_delay = 2) ";
		$query = $this->db->query($sql);
		
		return $query->num_rows();
	}
	
	function get_db_count_critical($db_type){
    $sql = "select id from db_status t where db_type = '$db_type' and role = 's' and t.repl_delay in(-1, 3) ";
		$query = $this->db->query($sql);
		
		return $query->num_rows();
	}
	
	
   /*
	 * 获取 主机 相关信息
	 */
	function get_os_paging($limit,$offset){
    $sql = "select tags, db_type, message from alerts_his t where server_id = 0 limit 0,5";
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
	
   /*
	 * 获取 告警 相关信息
	 */
	function get_alarm_paging($limit,$offset){
    $sql = "select tags, db_type, message 
    						from alerts t 
							where (t.db_type = 'os' and t.host in (select host from db_cfg_os))
							or (t.db_type = 'oracle' and t.server_id in (select id from db_cfg_oracle))
							or (t.db_type = 'mysql' and t.server_id in (select id from db_cfg_mysql))
							or (t.db_type = 'sqlserver' and t.server_id in (select id from db_cfg_sqlserver)); ";
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
	
}

/* End of file wlblazers_model.php */
/* Location: ./application/models/wlblazers_model.php */
