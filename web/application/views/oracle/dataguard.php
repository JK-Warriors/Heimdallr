<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- <div class="header">
            
            <h1 class="page-title"><?php echo $this->lang->line('_Oracle'); ?> <?php echo $this->lang->line('_DataGuard Monitor'); ?></h1>
</div> -->
        
<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Oracle Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DataGuard Monitor'); ?></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<!-- <div class="container-fluid">
<div class="row-fluid"> -->
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 

<div class="control-group">
<form name="form" class="form-inline" method="get" >
    <label class="control-label" for="">*<?php echo $this->lang->line('dg_group'); ?></label>
    
    <div class="controls" style="display:inline-block;" >
      <select name="dg_group_id" id="dg_group_id" class="input-large">
        <?php foreach ($dg_group as $item):?>
        <option value="<?php echo $item['id'];?>" <?php if ($item['id'] == $setval['id']) { ?>selected="selected"<?php } ?>><?php echo $item['id'];?>(<?php echo $item['group_name'];?>)</option>
        <?php endforeach;?>
        </select>
        <!-- <span class="help-inline"></span> -->
    </div>
    <button type="submit" class="btn btn-success" action="<?php site_url('lp_oracle/dataguard') ?>" > <?php echo $this->lang->line('detail'); ?></button>
    
    <a class="btn btn-success" href="<?php echo site_url('lp_oracle/dg_switch?dg_group_id='); echo $setval['id']; ?>" style="width:100px; float:right; margin-right:5px;"><?php echo $this->lang->line('switch_dg'); ?></a>

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
    <div style="float:left;"><img src="<?php if($primary_db[0]['open_mode']==-1){echo "./images/connect_error.png";} else{echo "./images/primary_db.png";}  ?> "/></div> 

        <div style="float:left;">
        <label style='padding: 0px 0px 0px 120px;' class="control-label" for="">Seq：<?php echo $standby_db[0]['s_sequence'] ?> block# <?php echo $standby_db[0]['s_block'] ?></label>
        <img src="./images/left_arrow.png"/>
        <img src="
        <?php
        $second_dif=floor((strtotime($primary_db[0]['p_db_time'])-strtotime($standby_db[0]['s_db_time']))%86400%60);
        if($second_dif > 3600 ){echo "./images/trans_alarm.png";}   #时间差超过1小时，显示trans_error图片
        elseif($primary_db[0]['open_mode']==-1 or $standby_db[0]['open_mode']==-1){echo "./images/trans_error.png";}
        else{echo "./images/health_status.png";}  ?> 
        "/>
        <img src="./images/right_arrow.png"/>
        </div> 
        
        <!-- <div style="float:left;"><img src="./images/standby_db.png"/></div>  -->
        
        <div style="float:left;"><img src="<?php if($standby_db[0]['open_mode']==-1){echo "./images/connect_error.png";} else{echo "./images/standby_db.png";}  ?> "/></div> 
    </div>

    <div style='padding: 5px 200px 0px 60px; height:150px;'>
        <div style="float:left; width:350px; height:100px; border:1px solid blue; color:blue"> 
            <div style='padding: 5px 0px 0px 10px;'>
            <label name="pri_thread" class="control-label" for=""><?php echo $this->lang->line('primary_db'); ?>: </label>
            <?php foreach ($primary_db as $item):?>
                    <label class="control-label" for="">Thread <?php echo $item['p_thread'] ?>: sequence: <?php echo $item['p_sequence'] ?></label>
            <?php endforeach;?>
            <label name="pri_time" class="control-label" for=""><?php echo $this->lang->line('db_time'); ?>：<?php echo $primary_db[0]['p_db_time'] ?></label>
            </div>
        </div>

        <div style="float:right; width:350px; height:100px; border:1px solid blue; color:blue"> 
            <div style='padding: 5px 0px 0px 10px;'>
            <label name="sta_thread" class="control-label" for=""><?php echo $this->lang->line('standby_db'); ?>: </label>
            <label name="sta_thread1" class="control-label" for=""><?php echo $this->lang->line('recovery_rate'); ?>: <?php echo $standby_db[0]['avg_apply_rate'] ?> KB/sec</label>
            <label name="sta_thread2" class="control-label" for=""><?php echo $this->lang->line('curr_recover'); ?>: thread#<?php echo $standby_db[0]['s_thread'] ?> sequence <?php echo $standby_db[0]['s_sequence'] ?> block# <?php echo $standby_db[0]['s_block'] ?></label>
            <label name="sta_time" class="control-label" for=""><?php echo $this->lang->line('db_time'); ?>：<?php echo $standby_db[0]['s_db_time'] ?></label>
            </div>
        </div>
    </div>
</div>  


<script type="text/javascript">
function refresh()
{
       window.location.reload();
}
setTimeout('refresh()',60000); //指定60秒刷新一次
</script>

