<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Error extends Front_Controller {
    function __construct(){
		parent::__construct();
	
	}

    public function permission_denied(){

        $this->layout->view("error/permission_denied");
    }

    public function exprie(){

        $this->layout->view("error/exprie");
    }
    
    public function no_license(){

        $this->layout->view("error/no_license");
    }
    
    public function out_quota(){

        $this->layout->view("error/out_quota");
    }
}

/* End of file error.php */
/* Location: ./application/controllers/error.php */