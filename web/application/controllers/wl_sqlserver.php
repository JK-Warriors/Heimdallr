<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Wl_sqlserver extends Front_Controller {

    function __construct(){
		parent::__construct();
        $this->load->model('cfg_sqlserver_model','server');
        $this->load->model("option_model","option");
				$this->load->model("sqlserver_model","sqlserver");
        $this->load->model("os_model","os");  
        $this->load->model("user_model","user");
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
        
        $data["datalist"]=$this->sqlserver->get_replication_total_record();
        #$data["pri_list"]=$this->sqlserver->get_primary_total();
        $data["sta_list"]=$this->sqlserver->get_standby_total();
        
        #$datalist=$this->sqlserver->get_replication_total_record();
        
        #if(empty($_GET["search"])){
        #    $datalist = get_mirror_tree($datalist);
        #}
        
        #$data["setval"]=$setval;
        #$data['datalist']=$datalist;

        $this->layout->view("sqlserver/replication",$data);
    }
    
    
   public function mirror_switch()
   {
        #parent::check_privilege();
        $base_path=$_SERVER['DOCUMENT_ROOT'];
        
        $id = isset($_GET["group_id"]) ? $_GET["group_id"] : "";
        $setval["id"] = $id;
        
        #$setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        #$setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        $data["datalist"]=$this->sqlserver->get_standby_total();
        
        if($id != ""){
        		$db_name = $this->sqlserver->get_db_name_by_group_id($id);
        		
        		$pri_id = $this->sqlserver->get_pri_id_by_group_id($id);
        		$sta_id = $this->sqlserver->get_sta_id_by_group_id($id);

		        if(isset($_POST["op_action"])){
		            $op_action = $_POST["op_action"];
		
		            if($op_action == "Switchover"){
		                $order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python mssql_switchover.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >mssql_switchover.log 2>&1';    
		                $result = shell_exec($order);
		                #$result = "Succes";
		            }
		            elseif($op_action == "Failover"){
		                $order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python mssql_failover.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >mssql_failover.log 2>&1';   
		                $result = shell_exec($order);  
		                #$result = "Succes";
		            }
		            
		        }
		
		
		        $data["primary_db"] = $this->sqlserver->get_primary_info($pri_id, $db_name);
		        $data["standby_db"] = $this->sqlserver->get_standby_info($sta_id, $db_name);
		        
		        $data["setval"]=$setval;
		        
		        $data["userdata"] = $this->user->get_user_by_username('admin');
        }

        $this->layout->view("sqlserver/mirror_switch",$data);
    }
    
    public function mirror_progress()
		{
        $group_id=isset($_GET["group_id"]) ? $_GET["group_id"] : "-1";
		    $op_action = isset($_GET["op_action"]) ? $_GET["op_action"] : "-1";
		    
				$setval["group_id"] = $group_id;
				$setval["op_action"] = $op_action;
						
        
        
        $type="";
        if($op_action == "Switchover"){
        		$type="SWITCHOVER";
        }
        else if($op_action == "Failover"){
        		$type="FAILOVER";
        }
        
        if($group_id!="-1"){
        		$data["mirror_group"]=$this->sqlserver->get_mirror_group_by_id($group_id);
		        $data["op_process"]=$this->sqlserver->get_mirror_process($group_id, $type);
		        $data["db_opration"]=$this->sqlserver->get_db_opration($group_id, $type);
						
        }
				

		    $data["items"] = $setval;
				$this->layout->setLayout("layout_blank");
        $this->layout->view("sqlserver/mirror_progress",$data);
    }
}

/* End of file sqlserver.php */
/* Location: ./application/controllers/sqlserver.php */
