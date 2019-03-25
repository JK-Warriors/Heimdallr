<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Wl_lock extends Front_Controller {

    function __construct(){
		parent::__construct();
        $this->load->model("lock_model","lock");
	}
    
   
	public function index()
	{
        parent::check_privilege();
        $data["datalist"]=$this->lock->get_db_list();
        
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["dsn"]=isset($_GET["dsn"]) ? $_GET["dsn"] : "";
        
        $data["setval"]=$setval;
        
        $this->layout->view("tool/index", $data);
	}
    
     
	public function view_lock()
	{
        parent::check_privilege();
        $data["datalist"]=$this->lock->get_db_list();
        
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["dsn"]=isset($_GET["dsn"]) ? $_GET["dsn"] : "";
        
        $data["setval"]=$setval;
        
        $this->layout->view("tool/lock_detail", $data);
	}
}	

/* End of file lock.php */
/* Location: ./application/controllers/lock.php */
