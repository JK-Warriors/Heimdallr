<?php 
class MySQL_model extends CI_Model{

	function insert($table,$data){		
		$this->db->insert($table, $data);
	}   

	function get_total_rows($table){
		$this->db->from($table);
		return $this->db->count_all_results();
	}


    
    function get_total_record($table){
        $query = $this->db->get($table);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
    
    function get_total_record_paging($table,$limit,$offset){
        $query = $this->db->get($table,$limit,$offset);
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
    
	
    function get_status_total_record($health=''){
        
        $this->db->select('*');
        $this->db->from('mysql_status');

        if($health==1){
            $this->db->where("connect", 1);
        }

        !empty($_GET["host"]) && $this->db->like("host", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);

        !empty($_GET["connect"]) && $this->db->where("connect", $_GET["connect"]);
        !empty($_GET["threads_connected"]) && $this->db->where("threads_connected >", (int)$_GET["threads_connected"]);
        !empty($_GET["threads_running"]) && $this->db->where("threads_running >", (int)$_GET["threads_running"]);
        
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
    
    function get_process_total_record(){
        
        $this->db->select('process.*,servers.host as server,servers.port,application.display_name application');
        $this->db->from('mysql_process process');
        $this->db->join('db_cfg_mysql servers', 'process.server_id=servers.id', 'left');
        $this->db->join('db_application application', 'servers.application_id=application.id', 'left');
        
        !empty($_GET["application_id"]) && $this->db->where("process.application_id", $_GET["application_id"]);
        !empty($_GET["server_id"]) && $this->db->where("process.server_id", $_GET["server_id"]);
        if(!empty($_GET["sleep"]) && $_GET["sleep"]=1){
            $this->db->where("process.command","Sleep");
        }
        else{
            $this->db->where("process.command <>","Sleep");
			$this->db->where("process.status <>","");
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
    }
    
    function get_replication_total_record(){
        $host=isset($_GET["host"]) ? $_GET["host"] : "";
        $tags=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        $sql = "SELECT t.*, drs.* from(SELECT
																	dr.id as group_id,
																	dr.group_name,
																	sdb.`id`  as s_id,
																	sdb.`host`  as s_host,
																	sdb.`port`  as s_port,
																	sdb.tags   as s_tags
																FROM db_cfg_mysql_dr dr,
																	db_cfg_mysql pdb,
																	db_cfg_mysql sdb
																WHERE dr.primary_db_id = pdb.id
																AND dr.standby_db_id = sdb.id
																AND dr.is_switch = 0
														union ALL
															SELECT
																	dr.id as group_id,
																	dr.group_name,
																	sdb.`id`  as s_id,
																	sdb.`host`  as s_host,
																	sdb.`port`  as s_port,
																	sdb.tags   as s_tags
																FROM db_cfg_mysql_dr dr,
																	db_cfg_mysql pdb,
																	db_cfg_mysql sdb
																WHERE dr.primary_db_id = sdb.id
																AND dr.standby_db_id = pdb.id
																AND dr.is_switch = 1) t left join mysql_dr_s drs on t.s_id = drs.server_id
													where 1=1 ";
				if($host != ""){
						$sql = $sql . " AND (t.`p_host` like '%" . $host . "%' or t.`s_host` like '%" . $host . "%')";
				}
				if($tags != ""){
						$sql = $sql . " AND (t.`p_tags` like '%" . $tags . "%' or t.`s_tags` like '%" . $tags . "%')";
				}
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

        
    function get_standby_total(){
        $sql = "select * from mysql_dr_s 
				        where server_id in (select id from db_cfg_mysql)
				          and (server_id, id) in (select server_id, max(id) from mysql_dr_s t group by server_id)";
										
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_pri_id_by_group_id($id){
        $query=$this->db->query("select CASE is_switch
                                            WHEN 0 THEN primary_db_id
                                            ELSE standby_db_id
                                        END as pri_id
                                   from db_cfg_mysql_dr
                                  where id = $id ");
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->pri_id;
        }
    }


    function get_sta_id_by_group_id($id){
        $query=$this->db->query("select CASE is_switch
                                            WHEN 0 THEN standby_db_id 
                                            ELSE primary_db_id
                                        END as sta_id
                                   from db_cfg_mysql_dr
                                  where id = $id ");
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->sta_id;
        }
    }


    function get_primary_info($pri_id, $db_name){
        $query=$this->db->query("select d.id,
                                    d.host         as p_host,
                                    d.port         as p_port,
                                    s.version      as p_db_version,
                                    s.connect      as p_connect,
                                    p.gtid_mode		as p_gtid_mode,
                                    p.read_only as p_read_only
                            from (select * from db_cfg_mysql where id = $pri_id) d
                            left join mysql_status s
                                on d.id = s.server_id
                            left join (select *
                                    from mysql_dr_p
                                    where server_id = $pri_id) p
                                on d.id = p.server_id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_standby_info($sta_id, $db_name){
        $query=$this->db->query("select d.id,
                                    d.host         as s_host,
                                    d.port         as s_port,
                                    s.version      as s_db_version,
                                    s.connect      as s_connect,
                                    ds.gtid_mode   as s_gtid_mode,
                                    ds.read_only	 as s_read_only,
                                    ds.master_server	 as master_server,
                                    ds.master_port	 as master_port,
                                    ds.slave_io_run	 as slave_io_run,
                                    ds.slave_sql_run	 as slave_sql_run,
                                    ds.delay	 as delay,
                                    ds.current_binlog_file	 as s_binlog_file,
                                    ds.current_binlog_pos	 as s_binlog_pos,
                                    ds.master_binlog_file	 as m_binlog_file,
                                    ds.master_binlog_pos	 as m_binlog_pos,
                                    ds.master_binlog_space	 as m_binlog_space
                            from (select * from db_cfg_mysql where id = $sta_id) d
                            left join mysql_status s
                                on d.id = s.server_id
                            left join (select *
                                    from mysql_dr_s
                                    where server_id = $sta_id) ds
                                on d.id = ds.server_id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
         
    function get_bigtable_total_record(){
        
        $this->db->select('*');
        $this->db->from('mysql_bigtable');
        
        !empty($_GET["host"]) && $this->db->like("host", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        $this->db->order_by('table_size','desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
    }
    
    
    
    function get_slowquery_total_rows($server_id){
	    if($server_id && $server_id!=0){
            $ext = ' and b.serverid_max='.$server_id;
        }
        else{
            $ext='';
        }
        
		$this->db->select('*');
        $this->db->from("mysql_slow_query_review a");
        $this->db->join("mysql_slow_query_review_his b", "a.checksum=b.checksum $ext ",'');
		return $this->db->count_all_results();
	}
    
 
	
    function get_slowquery_total_record($limit,$offset,$server_id){
        if($server_id && $server_id!=0){
            $ext = ' and b.serverid_max='.$server_id;
        }
        else{
            $ext='';
        }
        
        $this->db->select('a.checksum,a.fingerprint,a.sample,a.first_seen,a.last_seen,
b.serverid_max,b.db_max,b.user_max,b.ts_min,b.ts_max,sum(b.ts_cnt) ts_cnt, sum(b.Query_time_sum)/sum(b.ts_cnt) Query_time_avg, max(b.Query_time_max) Query_time_max, min(b.Query_time_min) Query_time_min,b.Query_time_sum Query_time_sum,
max(b.Lock_time_max) Lock_time_max, min(b.Lock_time_min) Lock_time_min,sum(b.Lock_time_sum) Lock_time_sum');
        $this->db->from("mysql_slow_query_review a");
        $this->db->join("mysql_slow_query_review_his b", "a.checksum=b.checksum $ext ",'');
		$this->db->group_by('a.checksum');
        $this->db->order_by('Query_time_sum','desc');
        
        $this->db->limit($limit,$offset);
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
    }
    
    function get_slowquery_record_top10($server_id,$begin_time,$end_time){
   
        $this->db->where("last_seen >=", $begin_time);
        $this->db->where("last_seen <=", $end_time);
        $this->db->select('s.*,sh.*');
        $this->db->from("mysql_slow_query_review s");
        $this->db->join("mysql_slow_query_review_his sh", "s.checksum=sh.checksum and sh.serverid_max=$server_id",'');
        $this->db->group_by('s.checksum');
        $this->db->order_by('Query_time_sum','desc');
        $this->db->limit(10);
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
    }
    
     
    
   

	function get_slowquery_record_by_checksum($checksum){
	   
	    $this->db->select('s.*,sh.*');
        $this->db->from("mysql_slow_query_review s");
        $this->db->join("mysql_slow_query_review_his sh", 's.checksum=sh.checksum');
		$this->db->where('s.checksum',$checksum);
        $query = $this->db->get();
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
	}
    
    function get_slowquery_analyze_day($server_id){
        if($server_id && $server_id!=0){
            $ext = '_'.$server_id;
        }
        else{
            $ext='';
        }
        $query=$this->db->query("select * from (select DATE_FORMAT(last_seen,'%Y-%m-%d') as days,count(*) as count from mysql_slow_query_review$ext  group by days order by days desc limit 10) as total order by days asc ;");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    
    function get_total_host(){
        $query=$this->db->query("select host  from mysql_status order by host;");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_total_application(){
        $query=$this->db->query("select application from mysql_status group by application order by application;");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

	function get_status_chart_record($server_id,$time){
        $query=$this->db->query("select * from mysql_status_his  where server_id=$server_id and YmdHi=$time limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }
    
    

    
    function get_replication_chart_record($server_id,$time){
        $query=$this->db->query("select slave_io_run,slave_sql_run,delay from mysql_dr_s_his where server_id=$server_id and YmdHi=$time limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }
    
    function get_mysql_info_by_server_id($server_id){
        $query=$this->db->query("select * from mysql_status_his where server_id=$server_id order by id desc limit 1;");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }
    
    function get_bigtable_chart_record($server_id,$table_name,$time){
        $query=$this->db->query("select table_size from mysql_bigtable_his where server_id=$server_id and table_name='$table_name' and Ymd=$time order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }

    function check_has_record($server_id,$time){
        $query=$this->db->query("select id from mysql_status_his where server_id=$server_id and YmdHi=$time");
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
																					FROM mysql_status_his h
																				 WHERE server_id = $server_id
																					 AND YmdHi >= DATE_ADD(sysdate(), INTERVAL -$begin_time minute)
																		) t
																	GROUP BY time");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }  
      
		function get_replication_chart_data($server_id, $begin_time){
        $query=$this->db->query("SELECT *
																	FROM(SELECT DATE_FORMAT(h.ymdhi, '%Y-%m-%d %H:%i') time, h.*
																					FROM mysql_dr_s_his h
																				 WHERE server_id = $server_id
																					 AND YmdHi >= DATE_ADD(sysdate(), INTERVAL -$begin_time minute)
																		) t
																	GROUP BY time");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
      
		function get_bigtable_chart_data($server_id, $tab_name, $begin_time){
        $query=$this->db->query("SELECT *
																	FROM(SELECT DATE_FORMAT(h.ymdhi, '%Y-%m-%d %H:%i') time, h.*
																					FROM mysql_bigtable_his h
																				 WHERE server_id = $server_id
																					 AND table_name = '" . $tab_name . "'
																					 AND YmdHi >= DATE_ADD(sysdate(), INTERVAL -$begin_time minute)
																		) t
																	GROUP BY time");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }


		function get_awr_chart_data($server_id, $begin_time, $end_time){
        $query=$this->db->query("SELECT *
																	FROM(SELECT DATE_FORMAT(h.ymdhi, '%Y-%m-%d %H:%i') time, h.*
																					FROM mysql_status_his h
																				 WHERE server_id = $server_id
																					 AND create_time >= from_unixtime(" . $begin_time . ")
																					 AND create_time <= from_unixtime(" . $end_time . ")
																		) t
																	GROUP BY time");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }  
    
    function get_dr_group_by_id($id){
        $query=$this->db->query("select * from db_cfg_mysql_dr where is_delete = 0 and id = $id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_dr_process($group_id,$type){
        $query=$this->db->query("select * from db_op_process where db_type='mysql' and group_id = $group_id and process_type = '$type' order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
          
    function get_db_opration($group_id, $type){
        $query=$this->db->query("select * from db_opration where db_type='mysql' and group_id = $group_id and op_type = '$type' order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    } 
    
    
}

/* End of file mysql_model.php */
/* Location: ./application/models/mysql_model.php */