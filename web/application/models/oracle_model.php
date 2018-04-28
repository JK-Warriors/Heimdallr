<?php 
class Oracle_model extends CI_Model{

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
        $this->db->from('oracle_status ');
        if($health==1){
            $this->db->where("connect", 1);
        }
        
        !empty($_GET["host"]) && $this->db->like("host", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        !empty($_GET["connect"]) && $this->db->where("connect", $_GET["connect"]);
        !empty($_GET["session_total"]) && $this->db->where("session_total >", (int)$_GET["session_total"]);
        !empty($_GET["session_actives"]) && $this->db->where("session_actives >", (int)$_GET["session_actives"]);
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
    
    
    function get_dg_status_total(){
        
        $host=isset($_GET["host"]) ? $_GET["host"] : "";
        $dsn=isset($_GET["dsn"]) ? $_GET["dsn"] : "";
        $sql = "SELECT * from(SELECT
																	dg.id as group_id,
																	dg.group_name,
																	pdb.`id`  as p_id,
																	pdb.`host`  as p_host,
																	pdb.`port`  as p_port,
																	pdb.dsn		as p_dsn,
																	pdb.tags  as p_tags,
																	sdb.`id`  as s_id,
																	sdb.`host`  as s_host,
																	sdb.`port`  as s_port,
																	sdb.dsn    as s_dsn,
																	sdb.tags   as s_tags
																FROM db_cfg_oracle_dg dg,
																	db_cfg_oracle pdb,
																	db_cfg_oracle sdb
																WHERE dg.primary_db_id = pdb.id
																AND dg.standby_db_id = sdb.id
																AND dg.is_switch = 0
														union ALL
															SELECT
																	dg.id as group_id,
																	dg.group_name,
																	pdb.`id`  as p_id,
																	pdb.`host`  as p_host,
																	pdb.`port`  as p_port,
																	pdb.dsn		as p_dsn,
																	pdb.tags  as p_tags,
																	sdb.`id`  as s_id,
																	sdb.`host`  as s_host,
																	sdb.`port`  as s_port,
																	sdb.dsn    as s_dsn,
																	sdb.tags   as s_tags
																FROM db_cfg_oracle_dg dg,
																	db_cfg_oracle pdb,
																	db_cfg_oracle sdb
																WHERE dg.primary_db_id = sdb.id
																AND dg.standby_db_id = pdb.id
																AND dg.is_switch = 1) t
													where 1=1 ";
				if($host != ""){
						$sql = $sql . " AND (t.`p_host` like '%" . $host . "%' or t.`s_host` like '%" . $host . "%')";
				}
				if($dsn != ""){
						$sql = $sql . " AND (t.`p_dsn` like '%" . $dsn . "%' or t.`s_dsn` like '%" . $dsn . "%')";
				}
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    
    function get_standby_total(){
        $sql = "select * from oracle_dg_s_status 
				        where server_id in (select id from db_cfg_oracle)
				          and (server_id, id) in (select server_id, max(id) from oracle_dg_s_status t group by server_id)";
										
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    
    function get_tablespace_total_record(){
        
        $host=isset($_GET["host"]) ? $_GET["host"] : "";
        $tags=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $sql = "SELECT t.*
									FROM oracle_tablespace t,
										db_cfg_oracle o
									WHERE t.server_id = o.id";
				if($host != ""){
						$sql = $sql . " AND t.`host` like '%" . $host . "%'";
				}
				if($tags != ""){
						$sql = $sql . " AND t.`tags` like '%" . $tags . "%'";
				}
				
				$sql = $sql . " order by host, max_rate desc";
				
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }


    function get_diskgroup_total(){
        
        $host=isset($_GET["host"]) ? $_GET["host"] : "";
        $tags=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $sql = "SELECT t.*
									FROM oracle_diskgroup t,
										db_cfg_oracle o
									WHERE t.server_id = o.id";
				if($host != ""){
						$sql = $sql . " AND t.`host` like '%" . $host . "%'";
				}
				if($tags != ""){
						$sql = $sql . " AND t.`tags` like '%" . $tags . "%'";
				}
				
				$sql = $sql . " order by host, used_rate desc";
				
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    
    function get_tablespace_by_id($id){
        $query=$this->db->query("select tablespace_name from oracle_tablespace where server_id = $id order by id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_flashback_db_list(){
        $query=$this->db->query("select o.* 
																	 from oracle_status o, db_cfg_oracle_dg d
																	where database_role = 'PHYSICAL STANDBY'
																	  and (o.server_id = d.primary_db_id or o.server_id = d.standby_db_id)
																	order by id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_users_by_id($id){
        $query=$this->db->query("select distinct owner from oracle_tables where server_id = $id order by 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_tables_by_id($id){
        $query=$this->db->query("select owner, table_name from oracle_tables where server_id = $id order by 1,2; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    

    function get_fb_process($server_id){
        $query=$this->db->query("select * from oracle_fb_process where server_id = $server_id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
            
    function get_restorepoint($id){
        $query=$this->db->query("select name from oracle_flashback where server_id = $id order by id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    
    function get_dataguard_total_record(){
        $query=$this->db->query("select t.id, 
                                        t.group_name,
                                        d1.`host` 			as p_host,
                                        d1.`port` 			as p_port,
                                        d1.dsn 				as p_dsn,
                                        p.`thread#` 		as p_thread,
                                        p.`sequence#` 	as p_sequence,
                                        p.curr_scn			as p_scn,
                                        p.curr_db_time as p_db_time,
                                        d2.`host`			as s_host,
                                        d2.`port`			as s_port,
                                        d2.dsn					as s_dsn,
                                        s.`thread#`		as s_thread,
                                        s.`sequence#`	as s_sequence,
                                        s.`block#`			as s_block,
                                        s.delay_mins,
                                        s.avg_apply_rate,
                                        s.curr_scn			as s_scn,
                                        s.curr_db_time	as s_db_time
                                from db_cfg_oracle_dg t, oracle_dg_p_status p, oracle_dg_s_status s, db_cfg_oracle d1, db_cfg_oracle d2
                                where t.primary_db_id = p.server_id
                                    and t.standby_db_id = s.server_id
                                    and t.primary_dest_id = p.dest_id
                                    and p.server_id = d1.id
                                    and s.server_id = d2.id
                                order by t.display_order; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    
    function get_dataguard_group(){
        $query=$this->db->query("select * from db_cfg_oracle_dg where is_delete = 0 order by display_order, id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_dg_group_by_id($id){
        $query=$this->db->query("select * from db_cfg_oracle_dg where is_delete = 0 and id = $id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    
    function get_dg_process_info($group_id,$type){
        $query=$this->db->query("select * from oracle_dg_process where group_id = $group_id and process_type = '$type' order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_mrp_status_by_id($id){
        $query=$this->db->query("select mrp_status from oracle_dg_s_status where server_id = '$id' order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
        	 $result=$query->row();
           return $result->mrp_status; 
        }
    }
    
    function get_db_role_by_id($id){
        $query=$this->db->query("select database_role from oracle_status where server_id = '$id' order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
        	 $result=$query->row();
           return $result->database_role; 
        }
    }

    function get_pri_id_by_group_id($id){
        $query=$this->db->query("select CASE is_switch
                                            WHEN 0 THEN primary_db_id
                                            ELSE standby_db_id
                                        END as pri_id
                                   from db_cfg_oracle_dg
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
                                   from db_cfg_oracle_dg
                                  where id = $id ");
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->sta_id;
        }
    }


    function get_pri_id_by_sta_id($id){
        $query=$this->db->query("select CASE is_switch
                                            WHEN 0 THEN primary_db_id
                                            ELSE standby_db_id
                                        END as pri_id
                                   from db_cfg_oracle_dg
                                  where primary_db_id = $id 
                                    or standby_db_id = $id ");
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->pri_id;
        }
    }
    
    
    function get_dg_id_by_id($id){
        $query=$this->db->query("select id
                                   from db_cfg_oracle_dg
                                  where primary_db_id = $id 
                                    or standby_db_id = $id ");
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->id;
        }
    }
    
    
    function get_primary_info($pri_id){
        $query=$this->db->query("select d.id,
                                    d.host         as p_host,
                                    d.port         as p_port,
                                    s.db_name      as db_name,
                                    s.open_mode    as open_mode,
                                    s.flashback_on    as flashback_on,
                                    s.flashback_earliest_time    as flashback_e_time,
                                    s.flashback_space_used    as flashback_space_used,
                                    p.`thread#`    as p_thread,
                                    p.`sequence#`  as p_sequence,
                                    p.curr_scn     as p_scn,
                                    p.curr_db_time as p_db_time
                            from (select * from db_cfg_oracle where id = $pri_id) d
                            left join oracle_status s
                                on d.id = s.server_id
                            left join (select *
                                    from oracle_dg_p_status
                                    where check_seq in (select max(check_seq)
                                                    from oracle_dg_p_status t
                                                    where server_id = $pri_id)
                                      and server_id = $pri_id) p
                                on d.id = p.server_id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_standby_info($sta_id){
        $query=$this->db->query("select d.host as s_host,
                                        d.port as s_port,
                                        os.db_name  as db_name,
                                        os.database_role  as database_role,
                                        os.version  as db_version,
                                        os.open_mode  as open_mode,
                                        os.flashback_on    as flashback_on,
                                        os.flashback_earliest_time    as flashback_e_time,
                                        os.flashback_space_used    as flashback_space_used,
                                        s.`thread#` as s_thread,
                                        s.`sequence#` as s_sequence,
                                        s.`block#` as s_block,
                                        s.delay_mins,
                                        s.avg_apply_rate,
                                        s.curr_scn       as s_scn,
                                        s.curr_db_time   as s_db_time,
                                        s.mrp_status     as s_mrp_status
                                  from (select * from db_cfg_oracle where id = $sta_id) d
                                left join oracle_status os
                                    on d.id = os.server_id
                                left JOIN oracle_dg_s_status s
                                    on d.id = s.server_id
                                    order by s.id desc 
                                    limit 1; ");
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
        $query=$this->db->query("select * from oracle_status_his  where server_id=$server_id and YmdHi=$time limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }
    
    

    
   
    function check_has_record($server_id,$time){
        $query=$this->db->query("select id from oracle_status_his where server_id=$server_id and YmdHi=$time");
        if ($query->num_rows() > 0)
        {
           return true; 
        }
        else{
            return false;
        }
    }
    
    

}

/* End of file oracle_model.php */
/* Location: ./application/models/oracle_model.php */