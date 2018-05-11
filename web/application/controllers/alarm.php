<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Alarm extends Front_Controller {
    function __construct(){
		parent::__construct();
	    $this->load->model("alarm_model","alarm");
	}
    
    public function index(){
        parent::check_privilege();
        
        if(!empty($_POST["alert_ids"])){
            $alert_ids = $_POST["alert_ids"];
            $this->alarm->move_alerts_to_history($alert_ids);
        }
        
        
        if(!empty($_GET["stime"])){
            $current_url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
        else{
            $current_url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'?noparam=1';
        }
        
        //分页
				$this->load->library('pagination');
				$config['base_url'] = $current_url;
				$config['total_rows'] = $this->alarm->get_alert_total_rows();
				$config['per_page'] = 10;
				$config['num_links'] = 5;
				$config['page_query_string'] = TRUE;
				$config['use_page_numbers'] = TRUE;
				$this->pagination->initialize($config);
				$offset = !empty($_GET['per_page']) ? $_GET['per_page'] : 1;
        
        !empty($_GET["host"]) && $this->db->where("host", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->where("tags", $_GET["tags"]);
        !empty($_GET["db_type"]) && $this->db->where("db_type", $_GET["db_type"]);
        !empty($_GET["level"]) && $this->db->where("level", $_GET["level"]);
        $this->db->order_by("create_time", "desc");

        $data['datalist'] = $this->alarm->get_alert_total_record_paging($config['per_page'],($offset-1)*$config['per_page']);
        
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["db_type"]=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        $setval["level"]=isset($_GET["level"]) ? $_GET["level"] : "";
        $setval["stime"]=$stime;
        $setval["etime"]=$etime;
        $data["setval"]=$setval;
        
        $this->layout->view("alarm/index",$data);
    }



    public function history(){
        parent::check_privilege();
        
        $stime = !empty($_GET["stime"])? $_GET["stime"]: date('Y-m-d H:i',time()-3600*24*30);
        $etime = !empty($_GET["etime"])? $_GET["etime"]: date('Y-m-d H:i',time()+60);
        $this->db->where("create_time >=", $stime);
        $this->db->where("create_time <=", $etime);
        
        if(!empty($_GET["stime"])){
            $current_url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
        else{
            $current_url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'?noparam=1';
        }
        
        //分页
				$this->load->library('pagination');
				$config['base_url'] = $current_url;
				$config['total_rows'] = $this->alarm->get_his_total_rows();
				$config['per_page'] = 20;
				$config['num_links'] = 5;
				$config['page_query_string'] = TRUE;
				$config['use_page_numbers'] = TRUE;
				$this->pagination->initialize($config);
				$offset = !empty($_GET['per_page']) ? $_GET['per_page'] : 1;
        
        !empty($_GET["host"]) && $this->db->where("host", $_GET["host"]);
        !empty($_GET["tags"]) && $this->db->where("tags", $_GET["tags"]);
        !empty($_GET["db_type"]) && $this->db->where("db_type", $_GET["db_type"]);
        !empty($_GET["level"]) && $this->db->where("level", $_GET["level"]);
        $stime = !empty($_GET["stime"])? $_GET["stime"]: date('Y-m-d H:i',time()-3600*24*30);
        $etime = !empty($_GET["etime"])? $_GET["etime"]: date('Y-m-d H:i',time());
        $this->db->where("create_time >=", $stime);
        $this->db->where("create_time <=", $etime);
        $this->db->order_by("create_time", "desc");

        $data['datalist'] = $this->alarm->get_his_total_record_paging($config['per_page'],($offset-1)*$config['per_page']);
        
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["db_type"]=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        $setval["level"]=isset($_GET["level"]) ? $_GET["level"] : "";
        $setval["stime"]=$stime;
        $setval["etime"]=$etime;
        $data["setval"]=$setval;
        
        $this->layout->view("alarm/history",$data);
    }
        
}

/* End of file alarm.php */
/* Location: ./application/controllers/alarm.php */