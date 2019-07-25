<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class cfg_license extends Front_Controller {
    function __construct(){
		parent::__construct();
    $this->load->model('cfg_license_model','license');
	 
	}
    
    /**
     * 首页
     */
    public function index(){
        parent::check_privilege('','no');				#这里只验证权限，不验证授权
        
        ##errorLog(PHP_OS);
        $data[license_data] = $this->license->get_license();  
        #errorLog($license_data['config_info']['ora_recover']); 
        
        
        $this->layout->view("cfg_license/index",$data);
    }
    
    ##获取机器码
    public function get_m_code()   
    {  
				$setval['machine_code'] = $this->license->getMacAddr(PHP_OS);
        $data["setval"]=$setval;
        	
				$this->layout->setLayout("layout_blank");
        $this->layout->view("cfg_license/json_data",$data);
		}
		
		public function license_active()   
    {  
    		$license_code = isset($_POST["license_code"]) ? $_POST["license_code"] : "";
    		
    		if($license_code){
    			$machine_code = $this->license->getMacAddr(PHP_OS);
    			
    			$license_data = json_decode($this->license->publicDecrypt($license_code),true);
    			
    			if($license_data){
    					if($license_data['key'] == $machine_code){
    							if($license_data['expiration_time'] > time()){
											$setval['active_result'] = $this->license->set_license($license_code);
											$setval['active_message'] = "License激活成功";
    							}else{
											$setval['active_result'] = -1;
											$setval['active_message'] = "激活失败：过期的License";
    							}
    					}else{
									$setval['active_result'] = -1;
									$setval['active_message'] = "激活失败：无效的License";
    					}
    			}else{
							$setval['active_result'] = -1;
							$setval['active_message'] = "激活失败：无效的License";
    			}
    			
    		}else{
							$setval['active_result'] = -1;
							$setval['active_message'] = "激活失败：无效的License";
    		}
    			
    		
        
        $data["setval"]=$setval;
        
				$this->layout->setLayout("layout_blank");
        $this->layout->view("cfg_license/json_data",$data);
		}



    
}

/* End of file cfg_license.php */
/* Location: ./application/controllers/cfg_license.php */