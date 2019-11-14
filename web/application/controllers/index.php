<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Index extends Front_Controller {
    function __construct(){
		parent::__construct();
        $this->load->model("wlblazers_model","wlblazers"); 
        $this->load->model("oracle_model","oracle");
        $this->load->model("os_model","os");
	
	}
    
    public function index(){
        parent::check_license();
        
        //for mysql
        $mysql_statistics = array();
        $data["cfg_mysql_count"] = $this->db->query("select count(*) as num from db_cfg_mysql where is_delete=0")->row()->num;
		$data["cfg_oracle_count"] = $this->db->query("select count(*) as num from db_cfg_oracle where is_delete=0")->row()->num;
        $data["cfg_sqlserver_count"] = $this->db->query("select count(*) as num from db_cfg_sqlserver where is_delete=0")->row()->num;
		$data["cfg_mongodb_count"] = $this->db->query("select count(*) as num from db_cfg_mongodb where is_delete=0")->row()->num;
		$data["cfg_redis_count"] = $this->db->query("select count(*) as num from db_cfg_redis where is_delete=0")->row()->num;
		$data["cfg_os_count"] = $this->db->query("select count(*) as num from db_cfg_os where is_delete=0")->row()->num;
        
        
		$wlblazers_status=$this->wlblazers->get_wlblazers_status();
        $data['wlblazers_status']=$wlblazers_status;
        
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["db_type"]=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        $setval["order"]=isset($_GET["order"]) ? $_GET["order"] : "";
        $setval["order_type"]=isset($_GET["order_type"]) ? $_GET["order_type"] : "";
        $data["setval"]=$setval;

        //$data['db_status'] = $this->db->query("select db_status.* from db_status where db_status.db_type in('mysql', 'oracle', 'mongodb', 'redis') order by db_status.db_type_sort asc,db_status.host asc, db_status.tags asc,db_status.role asc;")->result_array();
        $data['db_status'] = $this->wlblazers->get_db_status();
        
        $this->layout->view("index/index",$data);
    }
    
    
		public function dashboard(){
        parent::check_license();
        
        //for oracle
        $data["oracle_cfg_total"] = $this->wlblazers->get_oracle_cfg_total();
        //$data["oracle_active_count"] = $this->wlblazers->get_oracle_active_count();
        //$data["oracle_inactive_count"] = $this->wlblazers->get_oracle_inactive_count();
        
        $data["oracle_active_instance"] = $this->wlblazers->get_oracle_active_instance();
        $data["oracle_inactive_instance"] = $this->wlblazers->get_oracle_inactive_instance();
        
        //for oracle delay
        $data["oracle_normal"] = $this->wlblazers->get_db_count_normal('oracle');
        $data["oracle_waring"] = $this->wlblazers->get_db_count_waring('oracle');
        $data["oracle_critical"] = $this->wlblazers->get_db_count_critical('oracle');
        
        //for oracle delay chart
        $data["chart_server"] = $this->wlblazers->get_oracle_chart_server();
        $data["oracle_yAxis"] = $this->wlblazers->get_oracle_yAxis();
        
        //for mysql
        $data["mysql_cfg_total"] = $this->wlblazers->get_mysql_cfg_total();
        //$data["mysql_active_count"] = $this->wlblazers->get_mysql_active_count();
        //$data["mysql_inactive_count"] = $this->wlblazers->get_mysql_inactive_count();
        
        $data["mysql_active_instance"] = $this->wlblazers->get_mysql_active_instance();
        $data["mysql_inactive_instance"] = $this->wlblazers->get_mysql_inactive_instance();
        
        
        //for mysql delay
        $data["mysql_normal"] = $this->wlblazers->get_db_count_normal('mysql');
        $data["mysql_waring"] = $this->wlblazers->get_db_count_waring('mysql');
        $data["mysql_critical"] = $this->wlblazers->get_db_count_critical('mysql');
        
        //for sqlserver
        $data["sqlserver_cfg_total"] = $this->wlblazers->get_sqlserver_cfg_total();
        //$data["sqlserver_active_count"] = $this->wlblazers->get_sqlserver_active_count();
        //$data["sqlserver_inactive_count"] = $this->wlblazers->get_sqlserver_inactive_count();
        
        $data["sqlserver_active_instance"] = $this->wlblazers->get_sqlserver_active_instance();
        $data["sqlserver_inactive_instance"] = $this->wlblazers->get_sqlserver_inactive_instance();
        
        //for sqlserver mirror
        $data["sqlserver_normal"] = $this->wlblazers->get_sqlserver_count_normal();
        $data["sqlserver_waring"] = $this->wlblazers->get_sqlserver_count_waring();
        $data["sqlserver_critical"] = $this->wlblazers->get_sqlserver_count_critical();
        
        //get db instance total
        $data["db_instance_total"] = $this->wlblazers->get_db_instance_total();
        
        $center_db1 = $this->wlblazers->get_center_db('center_db1');
        $center_db2 = $this->wlblazers->get_center_db('center_db2');
        $center_db3 = $this->wlblazers->get_center_db('center_db3');
        $center_db1_type = $this->wlblazers->get_center_dbtype('center_db1');
        $center_db2_type = $this->wlblazers->get_center_dbtype('center_db2');
        $center_db3_type = $this->wlblazers->get_center_dbtype('center_db3');
        
        $data["db1_type"] = $center_db1_type;
        $data["db2_type"] = $center_db2_type;
        $data["db3_type"] = $center_db3_type;
      
        $core_db_id = $this->wlblazers->get_core_db();
        $core_os_host = $this->wlblazers->get_core_os();
        
        $data["center_db_count"] = $this->wlblazers->get_center_db_count();
        
        //get db name|tags
        $data["db_tag_1"] = $this->wlblazers->get_db_tag('center_db1');
        $data["db_tag_2"] = $this->wlblazers->get_db_tag('center_db2');
        $data["db_tag_3"] = $this->wlblazers->get_db_tag('center_db3');
        
        //get db_time
        $data["db_time_1"] = $this->wlblazers->get_db_time($center_db1,$center_db1_type);
        $data["db_time_2"] = $this->wlblazers->get_db_time($center_db2,$center_db2_type);
        $data["db_time_3"] = $this->wlblazers->get_db_time($center_db3,$center_db3_type);
        
        //get db time per day
        $data["db_time_per_day"] = $this->wlblazers->get_db_time_per_day($core_db_id);
        
        //get os info
        $data["core_os"] = $this->os->get_os_info($core_os_host);
        $data["core_os_disk"] = $this->os->get_os_disk_info($core_os_host);
        		
        //get total session, active session
        $data["db_session_1"] = $this->wlblazers->get_db_session($center_db1,$center_db1_type);
        $data["db_session_2"] = $this->wlblazers->get_db_session($center_db2,$center_db2_type);
        $data["db_session_3"] = $this->wlblazers->get_db_session($center_db3,$center_db3_type);
        
        //get top 5 tablespace
        $data["space_1"] = $this->wlblazers->get_tablespace_top5($center_db1,$center_db1_type);
        $data["space_2"] = $this->wlblazers->get_tablespace_top5($center_db2,$center_db2_type);
        $data["space_3"] = $this->wlblazers->get_tablespace_top5($center_db3,$center_db3_type);
        
        //get first redo    
        $data["redo_1"] = $this->wlblazers->get_log_per_hour($center_db1,$center_db1_type);
        $data["redo_2"] = $this->wlblazers->get_log_per_hour($center_db2,$center_db2_type);
        $data["redo_3"] = $this->wlblazers->get_log_per_hour($center_db3,$center_db3_type);


        //for os
        $data["os"] = $this->wlblazers->get_os_paging(0,5);
        
        
        //for alarm
        $data["alarm"] = $this->wlblazers->get_alarm_paging(0,5);
        
        
        $wlblazers_status=$this->wlblazers->get_wlblazers_status();
        $data['wlblazers_status']=$wlblazers_status;
        

        $data['db_status'] = $this->wlblazers->get_db_status();
        
				$this->layout->setLayout("layout_blank");
        $this->layout->view("index/dashboard2",$data);
    }
    
    
    public function series(){
        //for oracle
        $data["oracle_chart_server"] = $this->wlblazers->get_oracle_chart_server();
        $data["oracle_xAxis"] = $this->wlblazers->get_oracle_xAxis();
        $data["oracle_yAxis"] = $this->wlblazers->get_oracle_yAxis();
        
        
        $this->layout->setLayout("layout_blank");
        $this->layout->view("index/series",$data);
    }
}

/* End of file index.php */
/* Location: ./application/controllers/index.php */