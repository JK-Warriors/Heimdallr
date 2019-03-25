<?php 
class Lock_model extends CI_Model{

    protected $table='db_status';
    

    
    /*
	 * 获取db_list
	 */
	function get_db_list(){
        $host=isset($_GET["host"]) ? $_GET["host"] : "";
        $dsn=isset($_GET["dsn"]) ? $_GET["dsn"] : "";
        
        $sql = "SELECT * from(select 'Oracle' as db_type,
														       co.host,
														       co.dsn,
														       (CASE
														         WHEN co.host_type = '0' THEN
														          'Linux'
														         WHEN co.host_type = '1' THEN
														          ' AIX'
														         WHEN co.host_type = '2' THEN
														          ' HP-UX'
														         WHEN co.host_type = '3' THEN
														          ' Solaris'
														         WHEN co.host_type = '4' THEN
														          ' Windows'
														         ELSE
														          ' 其他'
														       END) host_type
														  from db_cfg_oracle co, oracle_status os
														 where co.id = os.server_id
														   and os.database_role = 'PRIMARY'
														   and os.connect = 1
														) t
													where 1=1 ";
				if($host != ""){
						$sql = $sql . " AND (t.`host` like '%" . $host . "%')";
				}
				if($dsn != ""){
						$sql = $sql . " AND (t.`dsn` like '%" . $dsn . "%')";
				}
																
        $query=$this->db->query($sql);
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
        
	}
    

	
	
}

/* End of file lock_model.php */
/* Location: ./application/models/lock_model.php */
