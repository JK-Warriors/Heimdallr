<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class cfg_bigview extends Front_Controller {
    function __construct(){
		parent::__construct();
    $this->load->model('cfg_bigview_model','bgv');
		$this->load->library('form_validation');
	
	}
    
    /**
     * 首页
     */
    public function index(){
        //parent::check_privilege();
        
        $data["total_db"]= $this->bgv->get_total_db();
        $data["total_os"]= $this->bgv->get_total_os();
        
        $data["unselect_db"] = $this->bgv->get_unselect_db();
        $data["select_db"] = $this->bgv->get_select_db();
        $data["core_db"] = $this->bgv->get_core_db();
        $data["core_os"] = $this->bgv->get_core_os();
        
        $this->layout->view("cfg_bigview/index",$data);
    }


    public function save(){
        //parent::check_privilege();
        $data["total_db"]= $this->bgv->get_total_db();
        $data["total_os"]= $this->bgv->get_total_os();
        
        
        //$center_db = $this->input->post('leftSelect');
        
        $center_db1 = $_POST['center_db1'];
        $center_db2 = $_POST['center_db2'];
        $center_db3 = $_POST['center_db3'];
        $core_db = $_POST['core_db'];
        $core_os = $_POST['core_os'];
        
        $setval["center_db1"] = $_POST["center_db1"];
        $setval["center_db2"] = $_POST["center_db2"];
        $setval["center_db3"] = $_POST["center_db3"];
        $setval["core_db"] = $_POST["core_db"];
        $setval["core_os"] = $_POST["core_os"];
        $data["items"] = $setval;
        
        
        if($center_db1 != ""){
        	$this->bgv->update_center_db($center_db1, 'center_db1');
        }
        else{
        	$this->bgv->clear_center_db('center_db1');
        }
        
        if($center_db2 != ""){
        	$this->bgv->update_center_db($center_db2, 'center_db2');
        }
        else{
        	$this->bgv->clear_center_db('center_db2');
        }
        
        if($center_db3 != ""){
        	$this->bgv->update_center_db($center_db3, 'center_db3');
        }
        else{
        	$this->bgv->clear_center_db('center_db3');
        }
            
        if($core_db != ""){
        	$this->bgv->update_core_db($core_db);
        }
        else{
        	$this->bgv->clear_core_db();
        }
        
        
        if($core_os != ""){
        	$this->bgv->update_core_os($core_os);
        }
        else{
        	$this->bgv->clear_core_os();
        }
        
        //$data["setval"]=$setval;
        //$data["return_code"]=-2;
        //redirect(site_url('cfg_bigview/index'));
        
				$this->layout->setLayout("layout_blank");
        $this->layout->view("cfg_bigview/save",$data);
    }
    
}

/* End of file cfg_os.php */
/* Location: ./application/controllers/cfg_os.php */