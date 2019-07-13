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
        
        $sql = "SELECT * from(SELECT
																	sm.id as group_id,
																	sm.mirror_name,
																	sm.db_name,
																	pdb.`id`  as p_id,
																	pdb.`host`  as p_host,
																	pdb.`port`  as p_port,
																	pdb.tags  as p_tags,
																	sdb.`id`  as s_id,
																	sdb.`host`  as s_host,
																	sdb.`port`  as s_port,
																	sdb.tags   as s_tags
																FROM db_cfg_sqlserver_mirror sm,
																	db_cfg_sqlserver pdb,
																	db_cfg_sqlserver sdb
																WHERE sm.primary_db_id = pdb.id
																AND sm.standby_db_id = sdb.id
																AND sm.is_switch = 0
														union ALL
															SELECT
																	sm.id as group_id,
																	sm.mirror_name,
																	sm.db_name,
																	pdb.`id`  as p_id,
																	pdb.`host`  as p_host,
																	pdb.`port`  as p_port,
																	pdb.tags  as p_tags,
																	sdb.`id`  as s_id,
																	sdb.`host`  as s_host,
																	sdb.`port`  as s_port,
																	sdb.tags   as s_tags
																FROM db_cfg_sqlserver_mirror sm,
																	db_cfg_sqlserver pdb,
																	db_cfg_sqlserver sdb
																WHERE sm.primary_db_id = sdb.id
																AND sm.standby_db_id = pdb.id
																AND sm.is_switch = 1) t
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
    
    
    function get_pri_id_by_group_id($id){
        $query=$this->db->query("select CASE is_switch
                                            WHEN 0 THEN primary_db_id
                                            ELSE standby_db_id
                                        END as pri_id
                                   from db_cfg_sqlserver_mirror
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
                                   from db_cfg_sqlserver_mirror
                                  where id = $id ");
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->sta_id;
        }
    }


    function get_mirror_group_by_id($id){
        $query=$this->db->query("select * from db_cfg_sqlserver_mirror where is_delete = 0 and id = $id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }


    function get_mirror_process($group_id,$type){
        $query=$this->db->query("select * from db_op_process where db_type='sqlserver' and group_id = $group_id and process_type = '$type' order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_db_opration($group_id, $type){
        $query=$this->db->query("select * from db_opration where db_type='sqlserver' and group_id = $group_id and op_type = '$type' order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }  

    function get_db_name_by_group_id($id){
        $query=$this->db->query("select db_name from db_cfg_sqlserver_mirror where id = $id ");
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->db_name;
        }
    }
        
    function get_standby_total(){
        $sql = "select * from sqlserver_mirror_s 
				        where server_id in (select id from db_cfg_sqlserver)
				          and (server_id, id) in (select server_id, max(id) from sqlserver_mirror_s t group by server_id)";
										
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_primary_info($pri_id, $db_name){
        $query=$this->db->query("select d.id,
                                    d.host         as p_host,
                                    d.port         as p_port,
                                    '$db_name'     as p_db_name,
                                    s.version      as p_db_version,
                                    s.connect      as p_connect,
                                    p.mirroring_role		as p_role,
                                    p.mirroring_state		as p_state,
                                    p.mirroring_end_of_log_lsn as p_end_log_lsn
                            from (select * from db_cfg_sqlserver where id = $pri_id) d
                            left join sqlserver_status s
                                on d.id = s.server_id
                            left join (select *
                                    from sqlserver_mirror_p
                                    where db_name = '$db_name'
                                      and server_id = $pri_id) p
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
                                    '$db_name'     as s_db_name,
                                    s.version      as s_db_version,
                                    s.connect      as s_connect,
                                    ms.mirroring_role     as s_role,
                                    ms.mirroring_state as s_state,
                                    ms.mirroring_end_of_log_lsn as s_end_log_lsn
                            from (select * from db_cfg_sqlserver where id = $sta_id) d
                            left join sqlserver_status s
                                on d.id = s.server_id
                            left join (select *
                                    from sqlserver_mirror_s
                                    where db_name = '$db_name'
                                      and server_id = $sta_id) ms
                                on d.id = ms.server_id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
}

/* End of file sqlserver_model.php */
/* Location: ./application/models/sqlserver_model.php */