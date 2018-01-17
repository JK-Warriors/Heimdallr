<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Oracle Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DataGuard Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DataGuard Switch'); ?></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<!-- <div class="container-fluid">
<div class="row-fluid"> -->
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>




<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 


<div>
<form name="form" class="form-inline" method="get" onsubmit="disable_btn()" action="<?php site_url('lp_oracle/dg_switch') ?>" >
    <a class="btn btn " href="<?php echo site_url('lp_oracle/dataguard') ?>"><i class="icon-return"></i> <?php echo $this->lang->line('return'); ?></a>

    <input name="Failover" type="submit" value="Failover" class="btn btn-success" style="width:100px; float:right; margin-right:5px;"></button>
    <input name="Switchover" type="submit" value="Switchover" class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"></button>


</form>
</div>
</div>






<div style="padding: 19px;" >
    <div style='padding: 20px 120px 0px 60px; height:100px; overflow:hidden'>
        <div style='float:left; height:100px; width:280px;'>
        <label name="pri_host" class="control-label" for="">IP：<?php  echo $primary_db[0]['p_host'] ?></label>
        <label name="pri_dbname" class="control-label" for=""><?php echo $this->lang->line('db_name'); ?>：<?php echo $primary_db[0]['db_name'] ?></label>
        <label name="pri_dbstatus" class="control-label" for=""><?php echo $this->lang->line('db_status'); ?>：<?php echo $primary_db[0]['open_mode'] ?></label>
        <label name="pri_port" class="control-label" for=""><?php echo $this->lang->line('db_port'); ?>：<?php echo $primary_db[0]['p_port'] ?></label>
        </div>
        <div style='float:right; height:100px; width:280px;'>
        <label name="sta_host" class="control-label" for="">IP：<?php  echo $standby_db[0]['s_host'] ?></label>
        <label name="sta_dbname" class="control-label" for=""><?php echo $this->lang->line('db_name'); ?>：<?php echo $standby_db[0]['db_name'] ?></label>
        <label name="sta_dbstatus" class="control-label" for=""><?php echo $this->lang->line('db_status'); ?>：<?php echo $standby_db[0]['open_mode'] ?></label>
        <label name="sta_port" class="control-label" for=""><?php echo $this->lang->line('db_port'); ?>：<?php echo $standby_db[0]['s_port'] ?></label>
        </div>
    </div>


<div style='padding: 5px 0px 0px 200px; height:150px;'>
    <div style="float:left;"><img src="./images/primary_db.png"/></div> 

        <div style="float:left;">
        <label style='padding: 0px 0px 0px 120px;' class="control-label" for="">ddd</label>
        <img src="./images/left_arrow.png"/>
        <img src="./images/health_status.png"/>
        <img src="./images/right_arrow.png"/>
        </div> 
        
        <div style="float:left;"><img src="./images/standby_db.png"/></div> 
    </div>

</div>  

<label name="test1" class="control-label" for=""><?php echo $this->lang->line('db_time'); ?>：<?php echo $setval['python'] ?></label>
<label name="test2" class="control-label" for=""><?php echo $this->lang->line('db_time'); ?>：<?php echo $setval['test'] ?></label>

<script type="text/javascript">
function disable_btn()
{
    document.getElementById('Switchover').disabled=true
    document.getElementById('Failover').disabled=true
}
</script>
