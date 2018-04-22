<?php 
class Alarm_model extends CI_Model{

	protected $table='alerts';
    
    function get_alert_total_rows(){
		$this->db->from('alerts');
        return $this->db->count_all_results();
	}
    
    function get_alert_total_record(){
        $query = $this->db->get('alerts');
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
    
    function get_alert_total_record_paging($limit,$offset){
        $this->db->limit($limit,$offset);
        $query = $this->db->get('alerts');
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
    
    
    
    function get_his_total_rows(){
		$this->db->from('alerts_his');
        return $this->db->count_all_results();
	}
    
    function get_his_total_record(){
        $query = $this->db->get('alerts_his');
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
    
    function get_his_total_record_paging($limit,$offset){
        $this->db->limit($limit,$offset);
        $query = $this->db->get('alerts_his');
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	}
    
    
    function move_alerts_to_history($alert_ids){
    		$sql = "insert into alerts_his select *,sysdate() from alerts where id in($alert_ids); ";
    		$this->db->query($sql);
    		
    		$sql = "delete from alerts where id in($alert_ids); ";
    		$this->db->query($sql);
    }
}

/* End of file alarm_model.php */
/* Location: ./application/models/alarm_model.php */