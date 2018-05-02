<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Wl_oracle extends Front_Controller {

    function __construct(){
		parent::__construct();
        $this->load->model('cfg_oracle_model','server');
        $this->load->model("option_model","option");
		$this->load->model("oracle_model","oracle");
        $this->load->model("os_model","os");  
        $this->load->model("user_model","user");
	}
    
   
	public function index()
	{
        parent::check_privilege();
        $data["datalist"]=$this->oracle->get_status_total_record();

        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        $setval["connect"]=isset($_GET["connect"]) ? $_GET["connect"] : "";
        $setval["session_total"]=isset($_GET["session_total"]) ? $_GET["session_total"] : "";
        $setval["session_actives"]=isset($_GET["session_actives"]) ? $_GET["session_actives"] : "";
        $setval["order"]=isset($_GET["order"]) ? $_GET["order"] : "";
        $setval["order_type"]=isset($_GET["order_type"]) ? $_GET["order_type"] : "";
        $data["setval"]=$setval;

        $this->layout->view("oracle/index",$data);
	}
    
   	
    public function tablespace()
	{
        parent::check_privilege();
        $data["datalist"]=$this->oracle->get_tablespace_total_record();

        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        $setval["order"]=isset($_GET["order"]) ? $_GET["order"] : "";
        $setval["order_type"]=isset($_GET["order_type"]) ? $_GET["order_type"] : "";
        $data["setval"]=$setval;

        $this->layout->view("oracle/tablespace",$data);
	}
    

    public function diskgroup()
	{
        parent::check_privilege();
        $data["datalist"]=$this->oracle->get_diskgroup_total();

        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        $setval["order"]=isset($_GET["order"]) ? $_GET["order"] : "";
        $setval["order_type"]=isset($_GET["order_type"]) ? $_GET["order_type"] : "";
        $data["setval"]=$setval;

        $this->layout->view("oracle/diskgroup",$data);
	}
	
	
	public function dglist()
	{
        #parent::check_privilege();
        $data["datalist"]=$this->oracle->get_dg_status_total();
        $data["sta_list"]=$this->oracle->get_standby_total();

        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["dsn"]=isset($_GET["dsn"]) ? $_GET["dsn"] : "";
        
        $data["setval"]=$setval;

        $this->layout->view("oracle/dglist",$data);
	}
	
	
    public function dataguard()
	{
        parent::check_privilege();
        $data["datalist"]=$this->oracle->get_status_total_record();
        $data["dg_group"]=$this->oracle->get_dataguard_group();
        
        if(isset($_GET["dg_group_id"])){
            $id = $_GET["dg_group_id"];
        }
        else{
            $id = $data["dg_group"][0]["id"];
        }
        
        $setval["id"] = $id;
        
        if($id != ""){
        		$pri_id = $this->oracle->get_pri_id_by_group_id($id);
        		$sta_id = $this->oracle->get_sta_id_by_group_id($id);

						if($pri_id != "" && $sta_id != ""){
        				$data["primary_db"] = $this->oracle->get_primary_info($pri_id);
        				$data["standby_db"] = $this->oracle->get_standby_info($sta_id);
						}
						
        }
        
        $data["setval"]=$setval;

        $this->layout->view("oracle/dataguard",$data);
    }
    

    public function dg_switch()
	{
        parent::check_privilege();
        $data["datalist"]=$this->oracle->get_status_total_record();
        $base_path=$_SERVER['DOCUMENT_ROOT'];
        
        $data["dg_group"]=$this->oracle->get_dataguard_group();
                
        if(isset($_GET["dg_group_id"])){
            $id = $_GET["dg_group_id"];
        }
        else{
            $id = $data["dg_group"][0]["id"];
        }
        $setval["id"] = $id;
        
        if($id != ""){
        		$pri_id = $this->oracle->get_pri_id_by_group_id($id);
        		$sta_id = $this->oracle->get_sta_id_by_group_id($id);

		        if(isset($_POST["dg_action"])){
		            $dg_action = $_POST["dg_action"];
		
		            if($dg_action == "Switchover"){
		                $order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python switchover.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >switchover.log 2>&1';    
		                $result = shell_exec($order);
		                #$result = "Succes";
		            }
		            elseif($dg_action == "Failover"){
		                $order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python failover.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >failover.log 2>&1';   
		                $result = shell_exec($order);  
		                #$result = "Succes";
		            }
		            elseif($dg_action == "MRPStart"){
		                $order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python mrp_start.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >mrp_start.log 2>&1';    
		                $result = shell_exec($order);
		                #$result = "Succes";
		            }
		            elseif($dg_action == "MRPStop"){
		                $order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python mrp_stop.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >mrp_stop.log 2>&1';   
		                $result = shell_exec($order);  
		            }
		            elseif($dg_action == "SnapshotStart"){
		                $order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python snapshot_start.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >snapshot_start.log 2>&1';    
		                $result = shell_exec($order);
		                #$result = "Succes";
		            }
		            elseif($dg_action == "SnapshotStop"){
		                $order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python snapshot_stop.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >snapshot_stop.log 2>&1';   
		                $result = shell_exec($order);  
		            }
		        }
        }
        
        
        if($id != ""){
		        $pri_id = $this->oracle->get_pri_id_by_group_id($id);
		        $sta_id = $this->oracle->get_sta_id_by_group_id($id);
		
		        $data["primary_db"] = $this->oracle->get_primary_info($pri_id);
		        $data["standby_db"] = $this->oracle->get_standby_info($sta_id);
		        
		        $data["setval"]=$setval;
		        
		        $data["userdata"] = $this->user->get_user_by_username('admin');
        }
				
        $this->layout->view("oracle/dg_switch",$data);
      
    }


    public function dg_progress()
	{
        $group_id=isset($_POST["group_id"]) ? $_POST["group_id"] : "-1";
        $dg_group=$this->oracle->get_dg_group_by_id($group_id);
        $data["dg_group"]=$dg_group;
        
        #get log from oracle_dg_process
        $type="";
        if($dg_group[0]['on_process'] == 1 && $dg_group[0]['on_switchover'] == '1'){
        		$type="SWITCHOVER";
        }
        else if($dg_group[0]['on_process'] == 1 && $dg_group[0]['on_failover'] == '1'){
        		$type="FAILOVER";
        }
        else if($dg_group[0]['on_process'] == 1 && $dg_group[0]['on_startmrp'] == '1'){
        		$type="MRP_START";
        }
        else if($dg_group[0]['on_process'] == 1 && $dg_group[0]['on_stopmrp'] == '1'){
        		$type="MRP_STOP";
        }
        else if($dg_group[0]['on_process'] == 1 && $dg_group[0]['on_startsnapshot'] == '1'){
        		$type="SNAPSHOT_START";
        }
        else if($dg_group[0]['on_process'] == 1 && $dg_group[0]['on_stopsnapshot'] == '1'){
        		$type="SNAPSHOT_STOP";
        }
        
        if($group_id!="-1"){
		        $data["dg_process"]=$this->oracle->get_dg_process_info($group_id, $type);
						
						# get mrp status by group id
						$sta_id = $this->oracle->get_sta_id_by_group_id($group_id);
						$mrp_status=$this->oracle->get_mrp_status_by_id($sta_id);
						$sta_role=$this->oracle->get_db_role_by_id($sta_id);
						
						$setval["mrp_status"] = $mrp_status;
						$setval["sta_role"] = $sta_role;
		        $data["items"] = $setval;
        }
				

				$this->layout->setLayout("layout_blank");
        $this->layout->view("oracle/dg_progress",$data);
    }
    

    public function flashback()
	{
        parent::check_privilege();
        $data["datalist"]=$this->oracle->get_flashback_db_list();

        if(isset($_GET["server_id"])){
            $id = $_GET["server_id"];
        }
        else{
            $id = $data["datalist"][0]["server_id"];
        }
        
        $setval["id"] = $id;
        
        if($id != ""){
        		$pri_id = $this->oracle->get_pri_id_by_sta_id($id);
        		$group_id = $this->oracle->get_dg_id_by_id($id);
		        $data["userdata"] = $this->user->get_user_by_username('admin');
        		$data["restore_point"] = $this->oracle->get_restorepoint($id);
        		
        		if($pri_id == ""){
        				$tablespaces = $this->oracle->get_tablespace_by_id($id);
        				$schemas = $this->oracle->get_users_by_id($id);
        				$tables = $this->oracle->get_tables_by_id($id);
        		}
        		else{
        				$tablespaces = $this->oracle->get_tablespace_by_id($pri_id);
        				$schemas = $this->oracle->get_users_by_id($pri_id);
        				$tables = $this->oracle->get_tables_by_id($pri_id);
        		}
        		
        		$data["tablespace"] = $tablespaces;
        		$data["users"] = $schemas;
        		$data["tables"] = $tables;
        		
        		
		        if(isset($_POST["fb_type"])){
		            $fb_type = $_POST["fb_type"];
		            $fb_method = $_POST["fb_method"];
		            $fb_point = $_POST["fb_point"];
		            $fb_time = "'" . $_POST["fb_time"] . "'";
		            $restore_table = "'" . $_POST["restore_table"] . "'";
		
        				$base_path=$_SERVER['DOCUMENT_ROOT'];
        				
        				if($fb_method == 1){
        						$value = $fb_point;
        				}
        				else{
        						$value = $fb_time;
        				}
        				$order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python fb_database.py -d ' . $id . ' -t ' . $fb_type . ' -m ' . $fb_method . ' -v ' . $value . ' -n ' . $restore_table . ' >fb_database.log 2>&1'; 
		            $result = shell_exec($order);
		              
		        }
		        
        }
        
        $setval["order"] = $order;
        $setval["group_id"] = $group_id;
        $data["setval"]=$setval;
        

        $this->layout->view("oracle/flashback",$data);
    }


    public function flashback_process()
	{
        $server_id=isset($_POST["server_id"]) ? $_POST["server_id"] : "-1";

        if($server_id!="-1"){
		        $data["fb_process"]=$this->oracle->get_fb_process($server_id);
						
        }
				

				$this->layout->setLayout("layout_blank");
        $this->layout->view("oracle/fb_progress",$data);
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
        $this->layout->view('oracle/chart',$data);
    }
    
    
    public function chart_data()
	{
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        

        if($server_id!="-1"){
        		$data['chart_data']=$this->oracle->get_chart_data($server_id, $begin_time);
        }
				
				$this->layout->setLayout("layout_blank");
        $this->layout->view("oracle/chart_data",$data);
    }
    
    
}

/* End of file oracle.php */
/* Location: ./application/controllers/oracle.php */
