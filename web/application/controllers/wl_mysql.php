<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Wl_mySQL extends Front_Controller {

    function __construct(){
		parent::__construct();
        $this->load->model('cfg_mysql_model','server');
        $this->load->model("option_model","option");
        $this->load->model("mysql_model","mysql");
        $this->load->model("os_model","os");  
        $this->load->model("user_model","user");
	}
    
    public function index2(){
        $mysql_statistics = array();
        $mysql_statistics["mysql_cfg_up"] = $this->db->query("select count(*) as num from mysql_status where connect=1")->row()->num;
        $mysql_statistics["mysql_cfg_down"] = $this->db->query("select count(*) as num from mysql_status  where connect!=1")->row()->num;
        $mysql_statistics["master_mysql_instance"] = $this->db->query("select count(*) as num from mysql_status where role='master' ")->row()->num;
        $mysql_statistics["slave_mysql_instance"] = $this->db->query("select count(*) as num from mysql_status where role='slave' ")->row()->num;
        
        $mysql_statistics["normal_mysql_replication"] = $this->db->query("select count(*) as num from mysql_dr_s where slave_io_run='Yes' and slave_sql_run='Yes' ")->row()->num;
        $mysql_statistics["exception_mysql_replication"] = $this->db->query("select count(*) as num from mysql_dr_s where slave_io_run!='Yes' or slave_sql_run!='Yes' ")->row()->num;
        
        $data["mysql_statistics"] = $mysql_statistics;
        //print_r($mysql_statistics);
        $data["mysql_versions"] = $this->db->query("select version as versions, count(*) as num from mysql_status where version !='0' GROUP BY versions")->result_array();
        
        $data['mysql_qps_ranking'] = $this->db->query("select server.host,server.port,status.queries_persecond
        value from mysql_status status left join db_cfg_mysql server
on `status`.server_id=`server`.id order by queries_persecond desc limit 10;")->result_array();
        $data['mysql_tps_ranking'] = $this->db->query("select server.host,server.port,status.transaction_persecond value from mysql_status status left join db_cfg_mysql server
on `status`.server_id=`server`.id order by transaction_persecond desc limit 10;")->result_array();
        $data['mysql_threads_connected_ranking'] = $this->db->query("select server.host,server.port,status.threads_connected value from mysql_status status left join db_cfg_mysql server
on `status`.server_id=`server`.id order by threads_connected desc limit 10;")->result_array();
        $data['mysql_threads_running_ranking'] = $this->db->query("select server.host,server.port,status.threads_running value from mysql_status status left join db_cfg_mysql server
on `status`.server_id=`server`.id order by threads_running desc limit 10;")->result_array();
//print_r($data['mysql_thread_ranking']);
        $this->layout->view("mysql/index",$data);
    }
    

	public function index()
	{
        parent::check_privilege();
        $data["datalist"]=$this->mysql->get_status_total_record();

        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["connect"]=isset($_GET["connect"]) ? $_GET["connect"] : "";
        $setval["threads_connected"]=isset($_GET["threads_connected"]) ? $_GET["threads_connected"] : "";
        $setval["threads_running"]=isset($_GET["threads_running"]) ? $_GET["threads_running"] : "";
        $setval["order"]=isset($_GET["order"]) ? $_GET["order"] : "";
        $setval["order_type"]=isset($_GET["order_type"]) ? $_GET["order_type"] : "";
        $data["setval"]=$setval;

        $this->layout->view("mysql/index",$data);
	}
    
    public function chart()
    {
        parent::check_privilege();
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        $time_span = $this->uri->segment(5);
        $time_span=!empty($time_span) ? $time_span : "min";

        
        $data['begin_time']=$begin_time;
        $data['cur_server_id']=$server_id;
        $data["cur_server"] = $this->server->get_servers($server_id);
        $this->layout->view('mysql/chart',$data);
    }
    
   	public function key_cache()
	{
        parent::check_privilege();
        $data["datalist"]=$this->mysql->get_status_total_record(1);
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["order"]=isset($_GET["order"]) ? $_GET["order"] : "";
        $setval["order_type"]=isset($_GET["order_type"]) ? $_GET["order_type"] : "";
        $data["setval"]=$setval;

        $this->layout->view("mysql/key_cache",$data);
	}
    
    public function key_cache_chart()
    {
        parent::check_privilege();
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        $time_span = $this->uri->segment(5);
        $time_span=!empty($time_span) ? $time_span : "min";

        
        $data['begin_time']=$begin_time;
        $data['cur_server_id']=$server_id;
        $data["cur_server"] = $this->server->get_servers($server_id);
        $this->layout->view('mysql/key_cache_chart',$data);
    }
    
    
    public function innodb()
	{
        parent::check_privilege();
        $data["datalist"]=$this->mysql->get_status_total_record(1);
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["order"]=isset($_GET["order"]) ? $_GET["order"] : "";
        $setval["order_type"]=isset($_GET["order_type"]) ? $_GET["order_type"] : "";
        $data["setval"]=$setval;
       
        $this->layout->view("mysql/innodb",$data);
	}
    
    public function innodb_chart()
    {
        parent::check_privilege();
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        $time_span = $this->uri->segment(5);
        $time_span=!empty($time_span) ? $time_span : "min";

        
        $data['begin_time']=$begin_time;
        $data['cur_server_id']=$server_id;
        $data["cur_server"] = $this->server->get_servers($server_id);
        $this->layout->view('mysql/innodb_chart',$data);
    }
    
    public function resource()
	{
        parent::check_privilege();
        $data["datalist"]=$this->mysql->get_status_total_record(1);
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["order"]=isset($_GET["order"]) ? $_GET["order"] : "";
        $setval["order_type"]=isset($_GET["order_type"]) ? $_GET["order_type"] : "";
        $data["setval"]=$setval;
       
        $this->layout->view("mysql/resource",$data);
	}
    
    
    
    public function resource_chart()
    {
        parent::check_privilege();
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        $time_span = $this->uri->segment(5);
        $time_span=!empty($time_span) ? $time_span : "min";

        
        $data['begin_time']=$begin_time;
        $data['cur_server_id']=$server_id;
        $data["cur_server"] = $this->server->get_servers($server_id);
        $this->layout->view('mysql/resource_chart',$data);
        
    }
    
    public function chart_data()
		{
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";

        if($server_id!="0"){
        		$data['chart_data']=$this->mysql->get_chart_data($server_id, $begin_time);
        }
				
				$this->layout->setLayout("layout_blank");
        $this->layout->view("mysql/chart_data",$data);
    }

    
    
    public function replication()
	{
        
        parent::check_privilege();
        $data["datalist"]=$this->mysql->get_replication_total_record();
 
        $data["cur_nav"]="mysql_replication";
        $this->layout->view("mysql/replication",$data);
	}
    
   public function dr_switch()
   {
        #parent::check_privilege();
        $base_path=$_SERVER['DOCUMENT_ROOT'];
        
        $id = isset($_GET["group_id"]) ? $_GET["group_id"] : "";
        $setval["id"] = $id;
        
        
        $data["datalist"]=$this->mysql->get_standby_total();
        
        if($id != ""){
        		$pri_id = $this->mysql->get_pri_id_by_group_id($id);
        		$sta_id = $this->mysql->get_sta_id_by_group_id($id);

		        if(isset($_POST["op_action"])){
		            $op_action = $_POST["op_action"];
		
		            if($op_action == "Switchover"){
		            		$file_full_name = $base_path . '/application/scripts/mysql_switchover.py';
		            		$file_exists = file_exists($file_full_name);
		            		if($file_exists==1){
		                	$order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python mysql_switchover.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >mysql_switchover.log 2>&1';   
		            		}else{
		                	$order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python mysql_switchover.pyc -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >mysql_switchover.log 2>&1';   
		            		}
		                  
		                $result = shell_exec($order);
		                #$result = "Succes";
		            }
		            elseif($op_action == "Failover"){
		            		$file_full_name = $base_path . '/application/scripts/mysql_failover.py';
		            		$file_exists = file_exists($file_full_name);
		            		if($file_exists==1){
		                	$order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python mysql_failover.py -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >mysql_failover.log 2>&1';   
		            		}else{
		                	$order = 'cd ' . $base_path . '/application/scripts/ && ' . 'python mysql_failover.pyc -g ' . $id . ' -p ' . $pri_id . ' -s ' . $sta_id . ' >mysql_failover.log 2>&1';   
		            		}
		            		
		                $result = shell_exec($order);  
		                #$result = "Succes";
		            }
		            
		        }
		
		
		        $data["primary_db"] = $this->mysql->get_primary_info($pri_id);
		        $data["standby_db"] = $this->mysql->get_standby_info($sta_id);
		        
		        $data["setval"]=$setval;
		        
		        $data["userdata"] = $this->user->get_user_by_username('admin');
        }

        $this->layout->view("mysql/dr_switch",$data);
    }
    
    public function dr_progress()
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
        		$data["dr_group"]=$this->mysql->get_dr_group_by_id($group_id);
		        $data["op_process"]=$this->mysql->get_dr_process($group_id, $type);
		        $data["db_opration"]=$this->mysql->get_db_opration($group_id, $type);
        }
				

		    $data["items"] = $setval;
				$this->layout->setLayout("layout_blank");
        $this->layout->view("mysql/dr_progress",$data);
    }


    public function replication_chart(){
        parent::check_privilege();
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        $time_span = $this->uri->segment(5);
        $time_span=!empty($time_span) ? $time_span : "min";

        
        $data['begin_time']=$begin_time;
        $data['cur_server_id']=$server_id;
        $data["cur_server"] = $this->server->get_servers($server_id);
        $this->layout->view('mysql/replication_chart',$data);
        
    }

    public function replication_chart_data()
		{
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
       
        if($server_id!="0"){
        		$data['chart_data']=$this->mysql->get_replication_chart_data($server_id, $begin_time);
        }
				
				$this->layout->setLayout("layout_blank");
        $this->layout->view("mysql/chart_data",$data);
    }

  
    public function bigtable()
	{
        parent::check_privilege();
        $data["datalist"]=$this->mysql->get_bigtable_total_record();

        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $data["setval"]=$setval;

        $this->layout->view("mysql/bigtable",$data);
	}
    
    public function bigtable_chart(){
        parent::check_privilege();
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $tab_name = $this->uri->segment(4);
        $tab_name=!empty($tab_name) ? $tab_name : "null";
        $begin_time = $this->uri->segment(5);
        $begin_time=!empty($begin_time) ? $begin_time : "30";

        
        $data['cur_server_id']=$server_id;
        $data['tab_name']=$tab_name;
        $data['begin_time']=$begin_time;
        $data["cur_server"] = $this->server->get_servers($server_id);
        $this->layout->view('mysql/bigtable_chart',$data);
    }

    public function bigtable_chart_data()
		{
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        $tab_name = $this->uri->segment(4);
        $tab_name=!empty($tab_name) ? $tab_name : "null";
        $begin_time = $this->uri->segment(5);
        $begin_time=!empty($begin_time) ? $begin_time : "30";
        
        if($server_id!="0"){
        		$data['chart_data']=$this->mysql->get_bigtable_chart_data($server_id, $tab_name, $begin_time);
        }
				
				$this->layout->setLayout("layout_blank");
        $this->layout->view("mysql/chart_data",$data);
    }
    
        
    public function process()
	{
        parent::check_privilege();
        $data["datalist"]=$this->mysql->get_process_total_record();

        $setval["application_id"]=isset($_GET["application_id"]) ? $_GET["application_id"] : "";
        $setval["server_id"]=isset($_GET["server_id"]) ? $_GET["server_id"] : "";
        $setval["sleep"]=isset($_GET["sleep"]) ? $_GET["sleep"] : 0;
        $data["setval"]=$setval;
        
        $data["server"]=$this->server->get_total_record_usage();
        $data["application"]=$this->app->get_total_record_usage();
        $data["option_kill_process"]=$this->option->get_option_item('kill_process');
        $data["cur_nav"]="mysql_process";
        $this->layout->view("mysql/process",$data);
	}
    
    public function ajax_kill_process(){
        $server_id = $_GET['server_id'];
        $pid = $_GET['pid'];
        if(empty($server_id) || empty($pid)){
            echo "empty";
        }
        else{
            $data=array(
                'server_id'=>$server_id,
                'pid'=>$pid,
                'user_id'=>$this->session->userdata('uid'),
            );
            $this->mysql->insert('mysql_process_killed',$data);
            echo "success";
        }
        
    }
    
    public function slowquery(){
        parent::check_privilege();
        $data["server"]=$servers=$this->server->get_total_slowquery_server();
        
        $server_id=isset($_GET["server_id"]) ? $_GET["server_id"] : "";
        //$server_id=isset($_GET["tags"]) ? $_GET["tags"] : "";
        
        if(!empty($_GET["server_id"])){
            $current_url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
        else{
            $current_url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'?noparam=1';
        }
        
        $stime = !empty($_GET["stime"])? $_GET["stime"]: date('Y-m-d H:i',time()-3600*24*7);
        $etime = !empty($_GET["etime"])? $_GET["etime"]: date('Y-m-d H:i',time());
        $this->db->where("last_seen >=", $stime);
        $this->db->where("last_seen <=", $etime);
        $this->db->where("b.sample !=", 'commit');
        
        //分页
		$this->load->library('pagination');
		$config['base_url'] = $current_url;
		$config['total_rows'] = $this->mysql->get_slowquery_total_rows($server_id);
		$config['per_page'] = 25;
		$config['num_links'] = 5;
		$config['page_query_string'] = TRUE;
		$config['use_page_numbers'] = TRUE;
		$this->pagination->initialize($config);
		$offset = !empty($_GET['per_page']) ? $_GET['per_page'] : 1;
        
        $stime = !empty($_GET["stime"])? $_GET["stime"]: date('Y-m-d H:i',time()-3600*24*7);
        $etime = !empty($_GET["etime"])? $_GET["etime"]: date('Y-m-d H:i',time());
        $this->db->where("last_seen >=", $stime);
        $this->db->where("last_seen <=", $etime);
        $this->db->where("a.sample !=", 'commit');
		$this->db->where("b.db_max !=", 'information_schema');
        $setval["stime"]=$stime;
        $setval["etime"]=$etime;
        
        
        $order = !empty($_GET["order"])? $_GET["order"]: 'last_seen';
        $order_type = !empty($_GET["order_type"])? $_GET["order_type"]: 'desc';
        $this->db->order_by($order,$order_type);
        $setval["order"]=$order;
        $setval["order_type"]=$order_type;
        
        $data["datalist"]=$this->mysql->get_slowquery_total_record($config['per_page'],($offset-1)*$config['per_page'],$server_id);
        
        //慢查询图表
        if($server_id && $server_id!=0){
            $ext = '_'.$server_id;
        }
        else{
            $ext='';
        }
        //日图表
        $reslut_day=array();
        for($i=15;$i>=0;$i--){
            $time=time()-3600*24*$i;
            $reslut_day[$i]['day']=$date= date('Y-m-d',$time);
            $reslut_day[$i]['num'] = $this->db->query("select count(*) as num from mysql_slow_query_review where DATE_FORMAT(last_seen,'%Y-%m-%d')='$date' ")->row()->num;;
        }
        $data['analyze_day']=$reslut_day;
        //月图表
        $reslut_month=array();
        for($i=12;$i>=0;$i--){
            $time=time()-3600*24*$i*31;
            $reslut_month[$i]['month']=$date= date('Y-m',$time);
            $reslut_month[$i]['num'] = $this->db->query("select count(*) as num from mysql_slow_query_review where DATE_FORMAT(last_seen,'%Y-%m')='$date' ")->row()->num;;
        }
        $data['analyze_month']=$reslut_month;
        //print_r($reslut_month);exit;

        $setval["server_id"]=$server_id;

        
        $data["setval"]=$setval;
        $data["cur_servers"] = $this->server->get_servers($server_id);
        
        $this->layout->view("mysql/slowquery",$data);
    }
    
    public function slowquery_detail(){
        parent::check_privilege();
        $checksum=$this->uri->segment(3);
        $record = $this->mysql->get_slowquery_record_by_checksum($checksum);
		if(!$checksum || !$record){
			show_404();
		}
        else{
            $data['record']= $record;
        }
        $setval["server_id"]=$record['serverid_max'];
        $data["setval"]=$setval;
        $this->layout->view("mysql/slowquery_detail",$data);
    }
    
    public function awrreport(){
        parent::check_privilege();
        $setval["begin_time"] =  date('Y-m-d H:i:s',time()-3600*2);
        $setval["end_time"] =  date('Y-m-d H:i:s',time());
        $data["setval"]=$setval;
        $data["server"]=$this->server->get_total_record_awr();
        $this->layout->view("mysql/awrreport",$data);
    }
    
    public function awrreport_create(){
        parent::check_privilege('wl_mysql/awrreport');
        $server_id=isset($_POST["server_id"]) ? $_POST["server_id"] : "";
    
        $host = $this->server->get_host_by_id($server_id);
        $begin_time = !empty($_POST["begin_time"])? $_POST["begin_time"]: date('Y-m-d H:i:s',time()-3600*2);
        $end_time = !empty($_POST["end_time"])? $_POST["end_time"]: date('Y-m-d H:i:s',time());
        
        $begin_timestamp = strtotime($begin_time);
        $end_timestamp = strtotime($end_time);
        
            
        //Top10 SlowSQL      
        $data["top10_slowQuery"]=$this->mysql->get_slowquery_record_top10($server_id,$begin_time,$end_time);
        //print_r($data["top10_slowQuery"]);
        
        $data['mysql_info']=$this->mysql->get_mysql_info_by_server_id($server_id);
        
        $data['begin_time']=$begin_time;
        $data['end_time']=$end_time;
        $data['begin_timestamp']=$begin_timestamp;
        $data['end_timestamp']=$end_timestamp;
        $data['cur_host']=$host;
        $data['server_id']=$server_id;
        $data["server"]=$this->server->get_total_record_awr();
        $this->load->view("mysql/awrreport_result",$data);
    }
  

    public function awr_chart_data()
		{
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "0";
        
        $begin_time = $this->uri->segment(4);
        $begin_time=!empty($begin_time) ? $begin_time : strtotime(date('Y-m-d H:i:s',time()-3600*2));
        
        $end_time = $this->uri->segment(5);
        $end_time=!empty($end_time) ? $end_time : strtotime(date('Y-m-d H:i:s',time()));
        
        
        

        if($server_id!="0"){
        		$data['chart_data']=$this->mysql->get_awr_chart_data($server_id, $begin_time, $end_time);
        }
				
				$this->layout->setLayout("layout_blank");
        $this->layout->view("mysql/chart_data",$data);
    }
    

    #add by leox 2015-09-25
    public function backup() {
        parent::check_privilege();
        $setval['hostname']=isset($_GET["hostname"]) ? $_GET["hostname"] : "";
        $setval['host']=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval['hostinfo']=isset($_GET["hostinfo"]) ? $_GET["hostinfo"] : "";
        $data['setval']=$setval;
        $data['backupinfo']=$this->db->query("select * from mysql_backup where backuptype=1 or state<>1  order by hostname,id desc;")->result_array();
        $this->layout->view('mysql/backup',$data);
    }
    public function backup_get() {
        parent::check_privilege();
        $setval['hostname']=isset($_GET["hostname"]) ? $_GET["hostname"] : "";
        $setval['host']=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval['hostinfo']=isset($_GET["hostinfo"]) ? $_GET["hostinfo"] : "";
        $data['setval']=$setval;
        $hostname=$setval['host'];
        $value=$setval['hostinfo'];
        //var_dump($setval);
        if ($hostname=='hostname') {
            $data['backupinfo']=$this->db->query("select * from mysql_backup where hostname='$value' order by id desc;")->result_array();
        }
        if ($hostname=='ip') {
            $data['backupinfo']=$this->db->query("select * from mysql_backup where ip like '%$value%' order by id desc;")->result_array();
        }
        $this->layout->view('mysql/backup',$data);

    }
    //后台备份监控API接口
    public function backup_api() {
        //$this->output->enable_profiler(TRUE);
        //parent::check_privilege();
        parse_str($_SERVER['QUERY_STRING'], $_GET);

        $setval["hostname"]=isset($_GET["hostname"]) ? $_GET["hostname"] : "";
        $setval["ip"]=isset($_GET["ip"]) ? $_GET["ip"] : "";
        $setval["filename"]=isset($_GET["filename"]) ? $_GET["filename"] : "";
        $setval["filesize"]=isset($_GET["filesize"]) ? $_GET["filesize"] : "";
        $setval["filemd5"]=isset($_GET["filemd5"]) ? $_GET["filemd5"] : "";
        $setval["state"]=isset($_GET["state"]) ? $_GET["state"] : "";
        $setval["backuptype"]=isset($_GET["backuptype"]) ? $_GET["backuptype"] : "";
        $setval["completetime"]=isset($_GET["completetime"]) ? $_GET["completetime"] : "";
        $setval["transformtime"]=isset($_GET["transformtime"]) ? $_GET["transformtime"] : "";
        $data["setval"]=$setval;
        $status=$this->mysql->addbackupinfo($setval);
        return $status;

    }
    public function backup_api_edit(){
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        $where["hostname"]=isset($_GET["hostname"]) ? $_GET["hostname"] : "";
        $where["filemd5"]=isset($_GET["filemd5"]) ? $_GET["filemd5"] : "";
        $setval["state"]=isset($_GET["state"]) ? $_GET["state"] : "";
        $setval["transformtime"]=isset($_GET["transformtime"]) ? $_GET["transformtime"] : "";
        $setval["costtime"]=isset($_GET["costtime"]) ? $_GET["costtime"] : "";
        $status=$this->mysql->editbackupinfo($setval,$where);
        return $status;
    }
    #容量监控,根据备份的历史数据生成
    public function capacity() {
        parent::check_privilege();
        $data['hosts']=$this->db->query("select substring_index(filename,'_',2) as hostname from mysql_backup where state=1 and backuptype=1;")->result_array();
        $this->layout->view('mysql/capacity',$data);
    }

    public function capacity_chart() {
        #parent::check_privilege();
        $data=$this->db->query("select substring_index(filename,'_',2) as hostname,date(transformtime) as btime,filesize,costtime from mysql_backup_history where state=1 and backuptype=1 and date(transformtime)>=date_sub(curdate(),interval 30 day) and curdate() group by substring_index(filename,'_',2),date(transformtime);")->result_array();
        $result=array();
        foreach($data as $item){
            /*$result['hostname'][]=$item['hostname'];
            $result['btime'][]=$item['btime'];
            $result['filesize'][]=$item['filesize'];
            $result['costtime'][]=$item['costtime'];*/
            //$result['hostname'][]=$item['hostname'];
            $result[$item['hostname']]['btime'][]=$item['btime'];
            $result[$item['hostname']]['filesize'][]=$item['filesize'];
            $result[$item['hostname']]['costtime'][]=$item['costtime'];
        }
        $php_json = json_encode($result);
        print_r($php_json);
    }

}

/* End of file mysql.php */
/* Location: ./application/controllers/mysql.php */
