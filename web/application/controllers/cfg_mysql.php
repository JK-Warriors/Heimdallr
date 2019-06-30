<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class cfg_mysql extends Front_Controller {
    function __construct(){
		parent::__construct();
        $this->load->model('cfg_mysql_model','mysql');
        $this->load->model('cfg_os_model','cfg_os');
		$this->load->library('form_validation');
	
	}
    
    /**
     * 首页
     */
    public function index(){
        parent::check_privilege();
        
        $host=isset($_GET["host"]) ? $_GET["host"] : "";
        $tags=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["tags"]=$tags;
        $setval["host"]=$host;
        $data["setval"]=$setval;
        $ext_where=''; 
        if(!empty($host)){
            $ext_where=$ext_where."  and host like '%$host%' ";
        }
        if(!empty($tags)){
            $ext_where=" and tags like '%$tags%' ";
        }
        
        $sql="select * from db_cfg_mysql  where is_delete=0 $ext_where order by id asc";
        
        $result=$this->mysql->get_total_record_sql($sql);
        $data["datalist"]=$result['datalist'];
        $data["datacount"]=$result['datacount'];
        $this->layout->view("cfg_mysql/index",$data);
    }
    
    /**
     * 回收站
     */
    public function trash(){
        parent::check_privilege();
        $sql="select * from db_cfg_mysql  where is_delete=1 order by id asc";
        $result=$this->mysql->get_total_record_sql($sql);
        $data["datalist"]=$result['datalist'];
        $data["datacount"]=$result['datacount'];
        $this->layout->view("cfg_mysql/trash",$data);
    }
    
    /**
     * 添加
     */
    public function add(){
        parent::check_privilege();
        
        /*
		 * 提交添加后处理
		 */
		$data['error_code']=0;
		if(isset($_POST['submit']) && $_POST['submit']=='add')
        {
			$this->form_validation->set_rules('host',  'lang:host', 'trim|required');
            $this->form_validation->set_rules('port',  'lang:port', 'trim|required|min_length[4]|max_length[6]|integer');
            $this->form_validation->set_rules('username',  'lang:username', 'trim|required');
			$this->form_validation->set_rules('password',  'lang:password', 'trim|required');
			$this->form_validation->set_rules('tags',  'lang:tags', 'trim|required');
            $this->form_validation->set_rules('binlog_store_days',  'lang:binlog_store_days', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_threads_connected',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_threads_running',  'lang:alarm_threshold', 'trim|required|integer');
			$this->form_validation->set_rules('threshold_warning_threads_waits',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_repl_delay',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_threads_connected',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_threads_running',  'lang:alarm_threshold', 'trim|required|integer');
			$this->form_validation->set_rules('threshold_critical_threads_waits',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_repl_delay',  'lang:alarm_threshold', 'trim|required|integer');
			if ($this->form_validation->run() == FALSE)
			{
				$data['error_code']='validation_error';
			}
			else
			{
					$data['error_code']=0;
					$data = array(
                        'host'=>$this->input->post('host'),
                        'port'=>$this->input->post('port'),
                        'username'=>$this->input->post('username'),
                        'password'=>$this->input->post('password'),
                        'tags'=>$this->input->post('tags'),
                        'monitor'=>$this->input->post('monitor'),
                        'send_mail'=>$this->input->post('send_mail'),
                        'send_sms'=>$this->input->post('send_sms'),
                        'slow_query'=>$this->input->post('slow_query'),
                        'send_mail_to_list'=>$this->input->post('send_mail_to_list'),
                        'send_sms_to_list'=>$this->input->post('send_sms_to_list'),
                        'send_slowquery_to_list'=>$this->input->post('send_slowquery_to_list'),
                        'bigtable_monitor'=>$this->input->post('bigtable_monitor'),
                        'bigtable_size'=>$this->input->post('bigtable_size'),
                        'binlog_auto_purge'=>$this->input->post('binlog_auto_purge'),
                        'binlog_store_days'=>$this->input->post('binlog_store_days'),
                        'alarm_threads_connected'=>$this->input->post('alarm_threads_connected'),
                        'alarm_threads_running'=>$this->input->post('alarm_threads_running'),
                        'alarm_threads_waits'=>$this->input->post('alarm_threads_waits'),
                        'alarm_repl_status'=>$this->input->post('alarm_repl_status'),
                        'alarm_repl_delay'=>$this->input->post('alarm_repl_delay'),
                        'threshold_warning_threads_connected'=>$this->input->post('threshold_warning_threads_connected'),
                        'threshold_critical_threads_connected'=>$this->input->post('threshold_critical_threads_connected'),
                        'threshold_warning_threads_running'=>$this->input->post('threshold_warning_threads_running'),
                        'threshold_critical_threads_running'=>$this->input->post('threshold_critical_threads_running'),
                        'threshold_warning_threads_waits'=>$this->input->post('threshold_warning_threads_waits'),
                        'threshold_critical_threads_waits'=>$this->input->post('threshold_critical_threads_waits'),
                        'threshold_warning_repl_delay'=>$this->input->post('threshold_warning_repl_delay'),
                        'threshold_critical_repl_delay'=>$this->input->post('threshold_critical_repl_delay'),
					);
					$this->mysql->insert($data);
                    redirect(site_url('cfg_mysql/index'));
            }
        }
           
        $this->layout->view("cfg_mysql/add",$data);
    }
    
    /**
     * 编辑
     */
    public function edit($id){
        parent::check_privilege();
        $id  = !empty($id) ? $id : $_POST['id'];
        /*
		 * 提交编辑后处理
		 */
        $data['error_code']=0;
        if(isset($_POST['submit']) && $_POST['submit']=='edit')
        {
            $this->form_validation->set_rules('host',  'lang:host', 'trim|required');
            $this->form_validation->set_rules('port',  'lang:port', 'trim|required|min_length[4]|max_length[6]|integer');
            $this->form_validation->set_rules('username',  'lang:username', 'trim|required');
            $this->form_validation->set_rules('password',  'lang:password', 'trim|required');
            $this->form_validation->set_rules('tags',  'lang:tags', 'trim|required');
            $this->form_validation->set_rules('binlog_store_days',  'lang:binlog_store_days', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_threads_connected',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_threads_running',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_threads_waits',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_repl_delay',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_threads_connected',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_threads_running',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_threads_waits',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_repl_delay',  'lang:alarm_threshold', 'trim|required|integer');
			if ($this->form_validation->run() == FALSE)
			{
				$data['error_code']='validation_error';
			}
			else
			{
					$data['error_code']=0;
					$data = array(
                        'host'=>$this->input->post('host'),
                        'port'=>$this->input->post('port'),
                        'username'=>$this->input->post('username'),
                        'password'=>$this->input->post('password'),
                        'tags'=>$this->input->post('tags'),
                        'monitor'=>$this->input->post('monitor'),
                        'send_mail'=>$this->input->post('send_mail'),
                        'send_sms'=>$this->input->post('send_sms'),
                        'slow_query'=>$this->input->post('slow_query'),
                        'send_mail_to_list'=>$this->input->post('send_mail_to_list'),
                        'send_sms_to_list'=>$this->input->post('send_sms_to_list'),
                        'send_slowquery_to_list'=>$this->input->post('send_slowquery_to_list'),
                        'bigtable_monitor'=>$this->input->post('bigtable_monitor'),
                        'bigtable_size'=>$this->input->post('bigtable_size'),
                        'binlog_auto_purge'=>$this->input->post('binlog_auto_purge'),
                        'binlog_store_days'=>$this->input->post('binlog_store_days'),
                        'alarm_threads_connected'=>$this->input->post('alarm_threads_connected'),
                        'alarm_threads_running'=>$this->input->post('alarm_threads_running'),
                        'alarm_threads_waits'=>$this->input->post('alarm_threads_waits'),
                        'alarm_repl_status'=>$this->input->post('alarm_repl_status'),
                        'alarm_repl_delay'=>$this->input->post('alarm_repl_delay'),
                        'threshold_warning_threads_connected'=>$this->input->post('threshold_warning_threads_connected'),
                        'threshold_critical_threads_connected'=>$this->input->post('threshold_critical_threads_connected'),
                        'threshold_warning_threads_running'=>$this->input->post('threshold_warning_threads_running'),
                        'threshold_critical_threads_running'=>$this->input->post('threshold_critical_threads_running'),
                        'threshold_warning_threads_waits'=>$this->input->post('threshold_warning_threads_waits'),
                        'threshold_critical_threads_waits'=>$this->input->post('threshold_critical_threads_waits'),
                        'threshold_warning_repl_delay'=>$this->input->post('threshold_warning_repl_delay'),
                        'threshold_critical_repl_delay'=>$this->input->post('threshold_critical_repl_delay'),
					);
					$this->mysql->update($data,$id);
					if($this->input->post('monitor')!=1){
						$this->mysql->db_status_remove($id);	
					}
                    redirect(site_url('cfg_mysql/index'));
            }
        }
        
		$record = $this->mysql->get_record_by_id($id);
		if(!$id || !$record){
			show_404();
		}
        else{
            $data['record']= $record;
        }
          
        $this->layout->view("cfg_mysql/edit",$data);
    }
    
    /**
     * 删除
     */
    function delete($id){
        parent::check_privilege();
        if($id){
            $this->mysql->delete($id);
            redirect(site_url('cfg_mysql/index'));
        }
    }
    
    /**
     * 恢复
     */
    function recover($id){
        parent::check_privilege('cfg_mysql/trash');
        
        if($id){
            $data = array(
				'is_delete'=>0
            );
		    $this->mysql->update($data,$id);
            redirect(site_url('cfg_mysql/trash'));
        }
    }  
    
    /**
     * 彻底删除
     */
    function forever_delete($id){
        parent::check_privilege('cfg_mysql/trash');
        if($id){
            //检查该数据是否是回收站数据
            $record = $this->mysql->get_record_by_id($id);
            $is_delete = $record['is_delete'];
            if($is_delete==1){
                $this->mysql->delete($id);
            }
            redirect(site_url('cfg_mysql/trash'));
        }
        
    }
    
    /**
     * 连接测试
     */
    function check_connection(){
        $ip = $_POST["ip"];
        $port = $_POST["port"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        $servername = $ip . ":" . $port;
				try {
 					#errorLog('begin');
 					
 					$conn = mysqli_connect($servername, $username, $password);
  				if (!$conn) {
    				#errorLog('Error: Unable to connect to MySQL.' . mysqli_connect_error());
        		$setval["connect"] = 1;
					}else{
						#errorLog('Succ'); 
        		$setval["connect"] = 0;
        		mysqli_close($conn);
					}
					
        	$data["setval"]=$setval;
        	
					$this->layout->setLayout("layout_blank");
        	$this->layout->view("cfg_mysql/json_data",$data);
					
				}
				catch(Exception $e){
 					errorLog($e->getMessage());
				}
    }    
    
    
}

/* End of file cfg_mysql.php */
/* Location: ./application/controllers/cfg_mysql.php */