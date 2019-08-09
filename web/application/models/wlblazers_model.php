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
	
	function get_oracle_active_instance(){
    $sql = "SELECT os.server_id, os.tags
								FROM oracle_status os, db_cfg_oracle co
								WHERE os.server_id = co.id
								AND os.connect = 1
								AND co.is_delete = 0
								AND co.monitor = 1";
		
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
	function get_oracle_inactive_instance(){
    $sql = "SELECT os.server_id, os.tags
								FROM oracle_status os, db_cfg_oracle co
								WHERE os.server_id = co.id
								AND os.connect != 1
								AND co.is_delete = 0
								AND co.monitor = 1";
		
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}


	function get_db_instance_total(){
    $sql = "SELECT os.server_id, os.tags, os.connect, 'oracle' as 'db_type'
								FROM oracle_status os, db_cfg_oracle co
								WHERE os.server_id = co.id
								AND co.is_delete = 0
								AND co.monitor = 1
						UNION ALL
						SELECT ms.server_id, ms.tags, ms.connect, 'mysql' as 'db_type'
														FROM mysql_status ms, db_cfg_mysql cm
														WHERE ms.server_id = cm.id
														AND cm.is_delete = 0
														AND cm.monitor = 1
						UNION ALL
						SELECT ss.server_id, ss.tags, ss.connect, 'sqlserver' as 'db_type'
														FROM sqlserver_status ss, db_cfg_sqlserver cs
														WHERE ss.server_id = cs.id
														AND cs.is_delete = 0
														AND cs.monitor = 1
						order by db_type, server_id";
		
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}



	function get_oracle_chart_server(){
    $sql = "select o.*, @rownum:=@rownum+1 rownum 
		from(SELECT DISTINCT s.server_id, s.tags, d.group_name
					FROM oracle_status_his h, oracle_status s, db_cfg_oracle_dg d
					WHERE h.server_id = s.server_id
					AND s.database_role = 'PHYSICAL STANDBY'
					AND (h.server_id = d.primary_db_id or h.server_id = d.standby_db_id)
					AND h.create_time > date_add(sysdate(), INTERVAL - 1 DAY)
				 ) o, (select @rownum:=-1) t";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
	function get_oracle_xAxis(){
    $sql = "select distinct time
						  from (SELECT h.server_id, h.host, h.port, h.tags, DATE_FORMAT(h.create_time, '%Y-%m-%d %H:%i') time, h.dg_delay delay
						          FROM oracle_status_his h, db_cfg_oracle_dg d
						         WHERE h.database_role = 'PHYSICAL STANDBY'
                       AND h.server_id in (select id from db_cfg_oracle)
                       AND (h.server_id = d.primary_db_id or h.server_id = d.standby_db_id)
						           AND h.create_time > date_add(sysdate(), INTERVAL - 1 DAY)
						         order by server_id, time desc) t
						 order by time";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}

	function get_oracle_yAxis(){
    $sql = "select *
						  from (SELECT h.server_id, h.host, h.port, h.tags, DATE_FORMAT(h.create_time, '%Y-%m-%d %H:%i') time, h.dg_delay delay
						          FROM oracle_status_his h, db_cfg_oracle_dg d
						         WHERE h.database_role = 'PHYSICAL STANDBY'
                       AND h.server_id in (select id from db_cfg_oracle)
                       AND (h.server_id = d.primary_db_id or h.server_id = d.standby_db_id)
						           AND h.create_time > date_add(sysdate(), INTERVAL - 1 DAY)
						         order by server_id, time desc) t
						 order by time";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
   /*
	 * 获取 空间 相关统计信息
	 */
    function get_tablespace_top5($server_id){
        $sql = "SELECT t.* FROM oracle_tablespace t WHERE t.server_id = $server_id order by max_rate desc limit 5";
        
        $query=$this->db->query($sql);
        
				if ($query->num_rows() > 0)
				{
					return $query->result_array();
				}
    }

   /*
	 * 获取 center db 
	 */
    function get_center_db($metrix_name){
        $query=$this->db->query("select server_id from db_cfg_bigview t where metrix_name = '$metrix_name'; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array()['server_id']; 
        }
    }
    
    function get_center_db_count(){
        $query=$this->db->query("select * from db_cfg_bigview t where metrix_name like 'center_db%' and server_id > 0; ");
        if ($query->num_rows() > 0)
        {
           return $query->num_rows(); 
        }else{
           return 0; 
        }
    }
    
    function get_core_db(){
        $query=$this->db->query("select server_id from db_cfg_bigview t where metrix_name = 'core_db'; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array()['server_id']; 
        }
    }
    function get_core_os(){
        $query=$this->db->query("select host from db_cfg_bigview t where metrix_name = 'core_os'; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array()['host']; 
        }
    }
    
   /*
	 * 获取 db tag
	 */
    function get_db_tag($metrix_name){
        $query=$this->db->query("select tags from db_cfg_bigview t where metrix_name = '$metrix_name'; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array()[0]; 
        }
    }

    
    
   /*
	 * 获取 db time
	 */
    function get_db_time($server_id){
        $query=$this->db->query("select * from (select id, server_id, snap_id, end_time, db_time, elapsed, rate from oracle_db_time where server_id = $server_id order by snap_id desc limit 10) a order by snap_id  ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

   /*
	 * 按天获取 核心库 db time
	 */
    function get_db_time_per_day($server_id){
        $query=$this->db->query("select a.server_id, a.end_time, a.db_time
																		from (
																		select server_id, substr(end_time, 1, 10) as end_time, sum(db_time) as db_time
																		from oracle_db_time t 
																		where t.server_id = $server_id
																		group by substr(end_time, 1, 10)
																		order by end_time desc
																		limit 7) a
																		order by a.end_time ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
   /*
	 * 获取 db session
	 */
    function get_db_session($server_id){
        $query=$this->db->query("select * from (select id, server_id, snap_id, end_time, total_session, active_session from oracle_session where server_id = $server_id order by snap_id desc limit 10) a order by snap_id ");
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

	function get_mysql_active_instance($key){
    $sql = "SELECT ms.server_id, ms.tags
								FROM mysql_status ms, db_cfg_mysql cm
								WHERE ms.server_id = cm.id
								AND ms.connect = 1
								AND cm.is_delete = 0
								AND cm.monitor = 1";
		
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
	function get_mysql_inactive_instance($key){
    $sql = "SELECT ms.server_id, ms.tags
								FROM mysql_status ms, db_cfg_mysql cm
								WHERE ms.server_id = cm.id
								AND ms.connect != 1
								AND cm.is_delete = 0
								AND cm.monitor = 1";
		
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
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

	function get_sqlserver_active_instance($key){
    $sql = "SELECT ss.server_id, ss.tags
								FROM sqlserver_status ss, db_cfg_sqlserver cs
								WHERE ss.server_id = cs.id
								AND ss.connect = 1
								AND cs.is_delete = 0
								AND cs.monitor = 1";
		
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
	
	function get_sqlserver_inactive_instance($key){
    $sql = "SELECT ss.server_id, ss.tags
								FROM sqlserver_status ss, db_cfg_sqlserver cs
								WHERE ss.server_id = cs.id
								AND ss.connect != 1
								AND cs.is_delete = 0
								AND cs.monitor = 1";
		
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
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
		if($db_type == 'oracle'){
				$sql = "select t.id 
		    				 from db_status t, db_cfg_oracle_dg d 
								where t.db_type = 'oracle' 
									and t.role = 's'
									and (t.server_id = d.primary_db_id or t.server_id = d.standby_db_id)
									and d.is_delete = 0
									and t.repl_delay in(-1, 3) ";
		}
		else{
				$sql = "select id from db_status t where db_type = '$db_type' and role = 's' and t.repl_delay in(-1, 3) ";
		}
		
		$query = $this->db->query($sql);
		
		return $query->num_rows();
	}

   /*
	 * 获取 sqlserver镜像信息
	 */
	function get_sqlserver_count_normal(){
    $sql = "select * from sqlserver_mirror_s t where mirroring_role = 2 and mirroring_state = 4 ";
		$query = $this->db->query($sql);
		
		return $query->num_rows();
	}

	function get_sqlserver_count_waring(){
    $sql = "select * from sqlserver_mirror_s t where mirroring_role = 2 and mirroring_state = 2 ";
		$query = $this->db->query($sql);
		
		return $query->num_rows();
	}
	
	function get_sqlserver_count_critical(){
    $sql = "select a.num - b.num from 
						(select count(*) num from db_cfg_sqlserver_mirror t where is_delete = 0) a, 
						(select count(*) num from sqlserver_mirror_s t where mirroring_role = 2 and mirroring_state in(2,4)) b ";
		
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
    $sql = "select create_time,tags, db_type, message, level
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
	
	 /*
	 * 获取 license 信息
	 */
	/*
	function get_license_exprie_date(){
		$key = 'qZe60QZFxuirub2ey4+7+Q==';
		
    $sql = "select license_info from wlblazers_license; ";
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0)
		{
			$result=$query->row();
      $license_info = $result->license_info;
      
    	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    	$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    	mcrypt_generic_init($td, $key, $iv);
    	$text = mdecrypt_generic($td, hex2bin($license_info));
    	mcrypt_generic_deinit($td);
    	mcrypt_module_close($td);
    	
    	return substr($text,0,10);
		}
	}
	
	
	function get_license_quota(){
		$key = 'qZe60QZFxuirub2ey4+7+Q==';
		
		$sql = "select license_info from wlblazers_license order by serial_id desc limit 1; ";
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0)
		{
			$result=$query->row();
      $license_info = $result->license_info;
      
    	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    	$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    	mcrypt_generic_init($td, $key, $iv);
    	$text = mdecrypt_generic($td, hex2bin($license_info));
    	mcrypt_generic_deinit($td);
    	mcrypt_module_close($td);
    	
    	return substr($text,strpos($text, '|')+1);
		}
		
		
	}
	*/
	
}

/* End of file wlblazers_model.php */
/* Location: ./application/models/wlblazers_model.php */
