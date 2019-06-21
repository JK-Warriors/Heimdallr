<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Wl_sqlserver extends Front_Controller {

    function __construct(){
		parent::__construct();
        $this->load->model('cfg_sqlserver_model','server');
        $this->load->model("option_model","option");
				$this->load->model("sqlserver_model","sqlserver");
        $this->load->model("os_model","os");  
	}

    
    public function index()
	{
        parent::check_privilege();
        $data["datalist"]=$this->sqlserver->get_status_total_record();

        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        $data["setval"]=$setval;
  
        $this->layout->view("sqlserver/index",$data);
    }


    
    public function chart()
    {
        parent::check_privilege('');
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        $time_span = $this->uri->segment(5);
        $time_span=!empty($time_span) ? $time_span : "min";

        
        $data['begin_time']=$begin_time;
        $data['cur_server_id']=$server_id;
        $data["cur_server"] = $this->server->get_servers($server_id);
        $this->layout->view('sqlserver/chart',$data);
    }


    public function chart_data()
	{
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        

        if($server_id!="0"){
        		$data['chart_data']=$this->sqlserver->get_chart_data($server_id, $begin_time);
        }
				
				$this->layout->setLayout("layout_blank");
        $this->layout->view("sqlserver/chart_data",$data);
    }
    
   
   public function replication()
   {
        parent::check_privilege();
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        $datalist=$this->sqlserver->get_replication_total_record();
        
        if(empty($_GET["search"])){
            $datalist = get_mirror_tree($datalist);
        }
        
        $data["setval"]=$setval;
        $data['datalist']=$datalist;

        $this->layout->view("sqlserver/replication",$data);
    }
    
}

/* End of file sqlserver.php */
/* Location: ./application/controllers/sqlserver.php */
