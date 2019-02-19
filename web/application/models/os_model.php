<?php 
class Os_model extends CI_Model{
    
    protected $table='os_status';
    
	function get_total_rows($table){
		$this->db->from($table);
		return $this->db->count_all_results();
	}
    

    function get_status_total_record(){
        $this->db->select('*');
        $this->db->from('os_status');
       
        !empty($_GET["host"]) && $this->db->like("ip", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        $this->db->order_by('ip asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
				{
						$result['datalist']=$query->result_array();
            $result['datacount']=$query->num_rows();
            return $result;
				}
	}
    


    
    function get_os_chart_record($host,$time){
        $query=$this->db->query("select * from os_status_his where ip='$host' and YmdHi=$time limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }
    
    function get_os_diskio_chart_record($host,$time){
        $query=$this->db->query("select * from os_diskio_his where ip='$host' and YmdHi=$time limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }
    
    function get_last_record($host){
        $query=$this->db->query("select * from os_status_his where ip='$host' order by id desc limit 1; ");
        if ($query->num_rows() > 0)
        {
           return $query->row_array(); 
        }
    }

     function get_disk_record($host){
        $query=$this->db->query("select * from os_disk where ip='$host'; ");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_total_host(){
        
        !empty($_GET["host"]) && $this->db->like("ip", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        $this->db->where("snmp",1);
        $this->db->from('os_status');
        $query=$this->db->get();
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }

    function get_total_disk_record(){
        $this->db->select('*');
        $this->db->from('os_disk');

        !empty($_GET["host"]) && $this->db->like("ip", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        if(!empty($_GET["order"]) && !empty($_GET["order_type"])){
            $this->db->order_by($_GET["order"],$_GET["order_type"]);
        }
        else{
            $this->db->order_by('id asc');
        }
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
				{
            return $query->result_array();
				}
				
    }

 
    function get_disk_data($host,$disk, $begin_time){
        $query=$this->db->query("SELECT *
																	FROM(SELECT DATE_FORMAT(h.ymdhi, '%Y-%m-%d %H:%i') time, h.*
																					FROM os_disk_his h
																				 WHERE ip = '$host'
                                           AND mounted = '$disk'
																					 AND YmdHi >= DATE_ADD(sysdate(), INTERVAL -$begin_time minute)
																		) t
																	GROUP BY time");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    


    function get_kernel_by_host($host){
        $query=$this->db->query("select kernel from os_status where ip='$host';");
        if ($query->num_rows() > 0)
        {
            $result=$query->row();
            return $result->kernel;
        }
    }
    
    
    function get_diskinfo_record($host){
        $query=$this->db->query("select * from os_disk where ip='$host';");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    function get_total_diskio_record(){
        $this->db->select('*');
        $this->db->from('os_diskio');

        !empty($_GET["host"]) && $this->db->like("ip", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->like("tags", $_GET["tags"]);
        
        if(!empty($_GET["order"]) && !empty($_GET["order_type"])){
            $this->db->order_by($_GET["order"],$_GET["order_type"]);
        }
        else{
            $this->db->order_by('id asc');
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0)
				{
						$result['datalist']=$query->result_array();
            $result['datacount']=$query->num_rows();
            return $result;
				}
	}

	function check_has_record($host,$time){
        $query=$this->db->query("select id from os_status_his where ip='$host' and YmdHi=$time");
        if ($query->num_rows() > 0)
        {
           return true; 
        }
        else{
            return false;
        }
    }
    
    
    function get_diskio_data($host,$fdisk, $begin_time){
        $query=$this->db->query("SELECT *
																	FROM(SELECT DATE_FORMAT(h.ymdhi, '%Y-%m-%d %H:%i') time, h.*
																					FROM os_diskio_his h
																				 WHERE ip = '$host'
                                           AND fdisk = '$fdisk'
																					 AND YmdHi >= DATE_ADD(sysdate(), INTERVAL -$begin_time minute)
																		) t
																	GROUP BY time");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
    
    
    function get_chart_data($host, $begin_time){
        $query=$this->db->query("SELECT *
																	FROM(SELECT DATE_FORMAT(h.ymdhi, '%Y-%m-%d %H:%i') time, ip, process, load_1, load_5, load_15, 
																							cpu_user_time, cpu_system_time, cpu_idle_time, 
																							mem_usage_rate, round((swap_avail/swap_total)*100,0) as swap_avail_rate, 
																							disk_io_reads_total, disk_io_writes_total, 
																							net_in_bytes_total, net_out_bytes_total
																					FROM os_status_his h
																				 WHERE ip = '$host'
																					 AND YmdHi >= DATE_ADD(sysdate(), INTERVAL -$begin_time minute)
																		) t
																	GROUP BY time");
        if ($query->num_rows() > 0)
        {
           return $query->result_array(); 
        }
    }
}

/* End of file os_model.php */
/* Location: ./application/models/os_model.php */