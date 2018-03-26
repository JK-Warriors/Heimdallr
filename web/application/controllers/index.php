<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Index extends Front_Controller {
    function __construct(){
		parent::__construct();
        $this->load->model("wlblazers_model","wlblazers"); 
	
	}
    
    public function index(){
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
        
        $this->layout->view("index/dashboard",$data);
    }
    
}

/* End of file index.php */
/* Location: ./application/controllers/index.php */