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
    
    function get_tablespace_total_record(){
        
        $this->db->select('*');
        $this->db->from('oracle_tablespace ');
       
        !empty($_GET["host"]) && $this->db->like("host", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        if(!empty($_GET["order"]) && !empty($_GET["order_type"])){
            $this->db->order_by($_GET["order"],$_GET["order_type"]);
        }
        else{
            $this->db->order_by('avail_size asc');
        }
        
        $query = $this->db->get();
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
                                from db_servers_oracle_dg t, oracle_dg_p_status p, oracle_dg_s_status s, db_servers_oracle d1, db_servers_oracle d2
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
        $query=$this->db->query("select * from db_servers_oracle_dg where is_delete = 0 order by display_order, id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_primary_db_by_group_id($id){
        $query=$this->db->query("select d.host         as p_host,
                                        d.port         as p_port,
                                        d.dsn          as db_name,
                                        s.open_mode    as open_mode,
                                        p.`thread#` as p_thread,
                                        p.`sequence#` as p_sequence,
                                        p.curr_scn     as p_scn,
                                        p.curr_db_time as p_db_time
                                from db_servers_oracle_dg g
                                join db_servers_oracle d
                                    on g.primary_db_id = d.id
                                    and g.id = $id
                                left join oracle_status s
                                    on g.primary_db_id = s.server_id
                                left JOIN oracle_dg_p_status p
                                    on g.primary_db_id = p.server_id; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_standby_db_by_group_id($id){
        $query=$this->db->query("select d.host as s_host,
                                        d.port as s_port,
                                        d.dsn  as db_name,
                                        os.open_mode  as open_mode,
                                        s.`thread#` as s_thread,
                                        s.`sequence#` as s_sequence,
                                        s.`block#` as s_block,
                                        s.delay_mins,
                                        s.avg_apply_rate,
                                        s.curr_scn       as s_scn,
                                        s.curr_db_time   as s_db_time
                                from db_servers_oracle_dg g
                                join db_servers_oracle d
                                    on g.id = $id
                                    and g.standby_db_id = d.id
                                left join oracle_status os
                                    on g.standby_db_id = os.server_id
                                left join oracle_dg_s_status s
                                    on g.standby_db_id = s.server_id; ");
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
        $query=$this->db->query("select * from oracle_status_history  where server_id=$server_id and YmdHi=$time limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }
    
    

    
   
    function check_has_record($server_id,$time){
        $query=$this->db->query("select id from oracle_status_history where server_id=$server_id and YmdHi=$time");
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