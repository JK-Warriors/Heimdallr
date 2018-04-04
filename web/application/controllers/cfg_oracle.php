<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class cfg_oracle extends Front_Controller {
    function __construct(){
		parent::__construct();
        $this->load->model('cfg_oracle_model','oracle');
        $this->load->model('cfg_oracle_dg_model','oracle_dgs');
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
        
        $sql="select * from db_cfg_oracle   where is_delete=0 $ext_where order by id asc";
        
        $result=$this->oracle->get_total_record_sql($sql);
        $data["datalist"]=$result['datalist'];
        $data["datacount"]=$result['datacount'];
        
        $this->layout->view("cfg_oracle/index",$data);
    }
    
    /**
     * 回收站
     */
    public function trash(){
        parent::check_privilege();
        $sql="select * from db_cfg_oracle  where is_delete=1 order by id asc";
        $result=$this->oracle->get_total_record_sql($sql);
        $data["datalist"]=$result['datalist'];
        $data["datacount"]=$result['datacount'];
        $this->layout->view("cfg_oracle/trash",$data);
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
            $this->form_validation->set_rules('dsn',  'lang:dsn', 'trim|required');
            $this->form_validation->set_rules('tags',  'lang:tags', 'trim|required');
            $this->form_validation->set_rules('threshold_warning_session_total',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_session_total',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_session_actives',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_session_actives',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_session_waits',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_session_waits',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_tablespace',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_tablespace',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_fb_space',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_fb_space',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_asm_space',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_asm_space',  'lang:alarm_threshold', 'trim|required|integer');
           
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
						'dsn'=>$this->input->post('dsn'),
						'host_type'=>$this->input->post('host_type'),
						'host_user'=>$this->input->post('host_user'),
						'host_pwd'=>$this->input->post('host_pwd'),
						'host_protocol'=>$this->input->post('host_protocol'),
						'monitor'=>$this->input->post('monitor'),
						'send_mail'=>$this->input->post('send_mail'),
						'send_sms'=>$this->input->post('send_sms'),
						'send_mail_to_list'=>$this->input->post('send_mail_to_list'),
						'send_sms_to_list'=>$this->input->post('send_sms_to_list'),
						'alarm_session_total'=>$this->input->post('alarm_session_total'),
						'alarm_session_actives'=>$this->input->post('alarm_session_actives'),
						'alarm_session_waits'=>$this->input->post('alarm_session_waits'),
						'alarm_tablespace'=>$this->input->post('alarm_tablespace'),
						'alarm_fb_space'=>$this->input->post('alarm_fb_space'),
						'alarm_asm_space'=>$this->input->post('alarm_asm_space'),
						'threshold_warning_session_total'=>$this->input->post('threshold_warning_session_total'),
						'threshold_warning_session_actives'=>$this->input->post('threshold_warning_session_actives'),
						'threshold_warning_session_waits'=>$this->input->post('threshold_warning_session_waits'),
						'threshold_warning_tablespace'=>$this->input->post('threshold_warning_tablespace'),
						'threshold_warning_fb_space'=>$this->input->post('threshold_warning_fb_space'),
						'threshold_warning_asm_space'=>$this->input->post('threshold_warning_asm_space'),
						'threshold_critical_session_total'=>$this->input->post('threshold_critical_session_total'),
						'threshold_critical_session_actives'=>$this->input->post('threshold_critical_session_actives'),
						'threshold_critical_session_waits'=>$this->input->post('threshold_critical_session_waits'),
						'threshold_critical_tablespace'=>$this->input->post('threshold_critical_tablespace'),
						'threshold_critical_fb_space'=>$this->input->post('threshold_critical_fb_space'),
						'threshold_critical_asm_space'=>$this->input->post('threshold_critical_asm_space'),
					);
                    $this->oracle->insert($data);
                    
                    $os_data = array(
												'host'=>$this->input->post('host'),
												'community'=> "public",
					   					  'tags'=>$this->input->post('tags'),
                        'monitor'=> 1,
                        'send_mail'=> 0,
												'send_sms'=> 0,
                        'send_mail_to_list'=> "",
												'send_sms_to_list'=> "",
												'alarm_os_process'=> 1,
												'alarm_os_load'=> 1,
												'alarm_os_cpu'=> 1,
												'alarm_os_network'=> 1,
												'alarm_os_disk'=> 1,
												'alarm_os_memory'=> 1,
												'threshold_warning_os_process'=> 300,
												'threshold_warning_os_load'=> 3,
												'threshold_warning_os_cpu'=> 70,
												'threshold_warning_os_network'=> 2,
												'threshold_warning_os_disk'=> 75,
												'threshold_warning_os_memory'=> 85,
												'threshold_critical_os_process'=> 500,
												'threshold_critical_os_load'=> 10,
												'threshold_critical_os_cpu'=> 80,
												'threshold_critical_os_network'=> 10,
												'threshold_critical_os_disk'=> 90,
												'threshold_critical_os_memory'=> 90,
                        'filter_os_disk'=> "",
					);
                    $this->cfg_os->insert($os_data);
                    
                    redirect(site_url('cfg_oracle/index'));
            }
        }
         
        $this->layout->view("cfg_oracle/add",$data);
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
            $this->form_validation->set_rules('dsn',  'lang:dsn', 'trim|required');
            $this->form_validation->set_rules('tags',  'lang:tags', 'trim|required');
            $this->form_validation->set_rules('threshold_warning_session_total',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_session_total',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_session_actives',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_session_actives',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_session_waits',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_session_waits',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_tablespace',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_tablespace',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_fb_space',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_fb_space',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_warning_asm_space',  'lang:alarm_threshold', 'trim|required|integer');
            $this->form_validation->set_rules('threshold_critical_asm_space',  'lang:alarm_threshold', 'trim|required|integer');
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
						'dsn'=>$this->input->post('dsn'),
						'host_type'=>$this->input->post('host_type'),
						'host_user'=>$this->input->post('host_user'),
						'host_pwd'=>$this->input->post('host_pwd'),
						'host_protocol'=>$this->input->post('host_protocol'),
						'monitor'=>$this->input->post('monitor'),
						'send_mail'=>$this->input->post('send_mail'),
						'send_sms'=>$this->input->post('send_sms'),
						'send_mail_to_list'=>$this->input->post('send_mail_to_list'),
						'send_sms_to_list'=>$this->input->post('send_sms_to_list'),
						'alarm_session_total'=>$this->input->post('alarm_session_total'),
						'alarm_session_actives'=>$this->input->post('alarm_session_actives'),
						'alarm_session_waits'=>$this->input->post('alarm_session_waits'),
						'alarm_tablespace'=>$this->input->post('alarm_tablespace'),
						'alarm_fb_space'=>$this->input->post('alarm_fb_space'),
						'alarm_asm_space'=>$this->input->post('alarm_asm_space'),
						'threshold_warning_session_total'=>$this->input->post('threshold_warning_session_total'),
						'threshold_warning_session_actives'=>$this->input->post('threshold_warning_session_actives'),
						'threshold_warning_session_waits'=>$this->input->post('threshold_warning_session_waits'),
						'threshold_warning_tablespace'=>$this->input->post('threshold_warning_tablespace'),
						'threshold_warning_fb_space'=>$this->input->post('threshold_warning_fb_space'),
						'threshold_warning_asm_space'=>$this->input->post('threshold_warning_asm_space'),
						'threshold_critical_session_total'=>$this->input->post('threshold_critical_session_total'),
						'threshold_critical_session_actives'=>$this->input->post('threshold_critical_session_actives'),
						'threshold_critical_session_waits'=>$this->input->post('threshold_critical_session_waits'),
						'threshold_critical_tablespace'=>$this->input->post('threshold_critical_tablespace'),
						'threshold_critical_fb_space'=>$this->input->post('threshold_critical_fb_space'),
						'threshold_critical_asm_space'=>$this->input->post('threshold_critical_asm_space'),
					);
					$this->oracle->update($data,$id);
					if($this->input->post('monitor')!=1){
						$this->oracle->db_status_remove($id);	
					}
                    redirect(site_url('cfg_oracle/index'));
            }
        }
        
         
		$record = $this->oracle->get_record_by_id($id);
		if(!$id || !$record){
			show_404();
		}
        else{
            $data['record']= $record;
        }
          
       
        $this->layout->view("cfg_oracle/edit",$data);
    }
    
    /**
     * 加入回收站
     */
    function delete($id){
        parent::check_privilege();
        if($id){
            $data = array(
				'is_delete'=>1
            );
		    $this->oracle->update($data,$id);
			$this->oracle->db_status_remove($id);
            redirect(site_url('cfg_oracle/index'));
        }
    }


    
    /**
     * 恢复
     */
    function recover($id){
        parent::check_privilege('cfg_oracle/trash');
        if($id){
            $data = array(
				'is_delete'=>0
            );
		    $this->oracle->update($data,$id);
            redirect(site_url('cfg_oracle/trash'));
        }
    }  
    
    /**
     * 彻底删除
     */
    function forever_delete($id){
        parent::check_privilege('cfg_mysql/trash');
        if($id){
            //检查该数据是否是回收站数据
            $record = $this->oracle->get_record_by_id($id);
            $is_delete = $record['is_delete'];
            if($is_delete==1){
                $this->oracle->delete($id);
            }
            redirect(site_url('cfg_oracle/trash'));
        }
        
    }
    
    /**
     * 批量添加
     */
     function batch_add(){
        parent::check_privilege();
		
        /*
		 * 提交批量添加后处理
		 */
		$data['error_code']=0;
		if(isset($_POST['submit']) && $_POST['submit']=='batch_add')
        {
            for($n=1;$n<=10;$n++){
			  $host = $this->input->post('host_'.$n);
              $port = $this->input->post('port_'.$n);
			  $username = $this->input->post('username_'.$n);
			  $password = $this->input->post('password_'.$n);
              $tags = $this->input->post('tags_'.$n);
			  $dsn = $this->input->post('dsn_'.$n);
              if(!empty($host) && !empty($port) && !empty($username) && !empty($password) && !empty($dsn) && !empty($tags)){
                
                 $data['error_code']=0;
					$data = array(
                        'host'=>$host,
						'port'=>$port,
					    'username'=>$username,
						'password'=>$password,
						'tags'=>$tags,
						'dsn'=>$dsn,
                        'monitor'=>$this->input->post('monitor_'.$n),
                        'send_mail'=>$this->input->post('send_mail_'.$n),
						'send_sms'=>$this->input->post('send_sms_'.$n),
                        'alarm_session_total'=>$this->input->post('alarm_session_total_'.$n),
                        'alarm_session_actives'=>$this->input->post('alarm_session_actives_'.$n),
						'alarm_session_waits'=>$this->input->post('alarm_session_waits_'.$n),
                        'alarm_tablespace'=>$this->input->post('alarm_tablespace_'.$n),
    
					);
					$this->oracle->insert($data);
              }
		   }
           redirect(site_url('cfg_oracle/index'));
        }
  
        $this->layout->view("cfg_oracle/batch_add",$data);
     }
    
    
     /**
     * 添加 DG
     */
    public function add_dg(){
        #parent::check_privilege();
        $sql="select * from db_cfg_oracle   where is_delete=0 order by id asc";
        $result=$this->oracle->get_total_record_sql($sql);
        $data["datalist"]=$result['datalist'];
        $data["datacount"]=$result['datacount'];
        
        $sql="select t.id,
                    t.group_name,
                    p.id   as pri_id,
                    p.host as pri_host,
                    p.port as pri_port,
                    p.dsn as pri_dsn,
                    p.tags as pri_tags,
                    s.id   as sta_id,
                    s.host as sta_host,
                    s.port as sta_port,
                    s.dsn as sta_dsn,
                    s.tags as sta_tags,
                    t.fb_retention as fb_retention
            from db_cfg_oracle_dg t, db_cfg_oracle p, db_cfg_oracle s
            where t.primary_db_id = p.id
                and t.standby_db_id = s.id
            order by t.display_order asc";
        $result=$this->oracle_dgs->get_total_record_sql($sql);
        $data["dglist"]=$result['datalist'];
        $data["dgcount"]=$result['datacount'];
		
        /*
		 * 提交添加后处理
		 */
		$data['error_code']=0;
		if(isset($_POST['submit']) && $_POST['submit']=='dg_manage')
        {
					$this->form_validation->set_rules('group_name',  'lang:group_name', 'trim|required');
					$this->form_validation->set_rules('primary_db',  'lang:primary_db', 'trim|required');
					$this->form_validation->set_rules('standby_db',  'lang:standby_db', 'trim|required');
					$this->form_validation->set_rules('fb_retention',  'lang:standby_db', 'trim|required');
           
			if ($this->form_validation->run() == FALSE)
			{
				$data['error_code']='validation_error';
			}
			else
			{
					$data['error_code']=0;
					$data = array(
						'group_name'=>$this->input->post('group_name'),
						'primary_db_id'=>$this->input->post('primary_db'),
						'standby_db_id'=>$this->input->post('standby_db'),
						'fb_retention'=>$this->input->post('fb_retention'),
					);
					$this->oracle_dgs->insert($data);
                    redirect(site_url('cfg_oracle/add_dg'));
            }
        }
         
        $this->layout->view("cfg_oracle/add_dg",$data);
    }

    
    
    public function edit_dg($id){
        //parent::check_privilege();
        $id  = !empty($id) ? $id : $_POST['id'];
        
        
        /*
				 * 提交编辑后处理
				*/
        $data['error_code']=0;
				if(isset($_POST['submit']) && $_POST['submit']=='dg_manage')
        {
					$this->form_validation->set_rules('group_name',  'lang:group_name', 'trim|required');
					$this->form_validation->set_rules('primary_db',  'lang:primary_db', 'trim|required');
					$this->form_validation->set_rules('standby_db',  'lang:standby_db', 'trim|required');
					$this->form_validation->set_rules('fb_retention',  'lang:fb_retention', 'trim|required');
					
					if ($this->form_validation->run() == FALSE)
					{
						$data['error_code']='validation_error';
					}
					else
					{
						$data['error_code']=0;
						$data = array(
							'group_name'=>$this->input->post('group_name'),
							'primary_db_id'=>$this->input->post('primary_db'),
							'standby_db_id'=>$this->input->post('standby_db'),
							'fb_retention'=>$this->input->post('fb_retention'),
						);
						
						$this->oracle_dgs->update($data,$id);
            redirect(site_url('cfg_oracle/add_dg'));
	        }
      	}
      	
      	$sql="select * from db_cfg_oracle where is_delete=0 order by id asc";
        $result=$this->oracle->get_total_record_sql($sql);
        $data["datalist"]=$result['datalist'];
        $data["datacount"]=$result['datacount'];
        
        $sql="select t.id,
                    t.group_name,
                    p.id   as pri_id,
                    p.host as pri_host,
                    p.port as pri_port,
                    p.dsn as pri_dsn,
                    p.tags as pri_tags,
                    s.id   as sta_id,
                    s.host as sta_host,
                    s.port as sta_port,
                    s.dsn as sta_dsn,
                    s.tags as sta_tags,
                    t.fb_retention as fb_retention
            from db_cfg_oracle_dg t, db_cfg_oracle p, db_cfg_oracle s
            where t.primary_db_id = p.id
                and t.standby_db_id = s.id
                and p.is_delete = 0
                and s.is_delete = 0
            order by t.display_order asc";
        $result=$this->oracle_dgs->get_total_record_sql($sql);
        $data["dglist"]=$result['datalist'];
        $data["dgcount"]=$result['datacount'];
        
        
        $data["group_id"]=$id;
        $sql="select * from db_cfg_oracle_dg  where is_delete=0 and id = $id ";
        $result=$this->oracle->get_total_record_sql($sql);
        $data["dg"]=$result['datalist'];
        
        
       
        $this->layout->view("cfg_oracle/add_dg",$data);
    }
    
    
    /**
    * 删除 DG 链路
    */
    function delete_dg($id){
        #parent::check_privilege();
        if($id){
		    $this->oracle_dgs->delete($id);
            redirect(site_url('cfg_oracle/add_dg'));
        }
    }

}

/* End of file cfg_oracle.php */
/* Location: ./application/controllers/cfg_oracle.php */