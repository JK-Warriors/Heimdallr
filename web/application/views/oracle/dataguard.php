<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- <div class="header">
            
            <h1 class="page-title"><?php echo $this->lang->line('_Oracle'); ?> <?php echo $this->lang->line('_DataGuard Monitor'); ?></h1>
</div> -->
        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_oracle/index'); ?>"><?php echo $this->lang->line('_Oracle Monitor'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><a href="<?php echo site_url('wl_oracle/dglist'); ?>"><?php echo $this->lang->line('_DataGuard List'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DataGuard Detail'); ?></li><span class="divider"></span></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<!-- <div class="container-fluid">
<div class="row-fluid"> -->
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;display:none;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 

<div class="control-group">

</div>
    
</div>



<div style="padding: 19px; <?php if($setval['id']!=""){echo "display:none;";} ?>" >
	<tr>
<td colspan="12">
<font color="red"><?php echo $this->lang->line('no_record'); ?></font>
</td>
</tr>
</div>


<div class="dg">
<div class="dg1">
<form name="form" class="form-inline" method="get" >
    <label class="control-label" for="">*<?php echo $this->lang->line('dg_group'); ?></label>
    
    <div class="controls" style="display:inline-block;" >
      <select name="dg_group_id" id="dg_group_id" class="input-large">
        <?php foreach ($dg_group as $item):?>
        <option value="<?php echo $item['id'];?>" <?php if ($item['id'] == $setval['id']) { ?>selected="selected"<?php } ?>><?php echo $item['group_name'];?></option>
        <?php endforeach;?>
        </select>
        <!-- <span class="help-inline"></span> -->
    </div>
    <button type="submit" class="btn btn-success" action="<?php site_url('wl_oracle/dataguard') ?>" > <?php echo $this->lang->line('detail'); ?></button>
    
    <a class="btn btn-success" href="<?php echo site_url('wl_oracle/dg_switch?dg_group_id='); echo $setval['id']; ?>" style="width:100px; float:right; margin-right:5px;" ><?php echo $this->lang->line('dg_management'); ?></a>

</form>
</div>
<div class="dg2  <?php if($setval['id']==""){echo "display:none;";} ?>">
<div class="dg2_1">
<div class="dgc1">
 <label name="pri_host" class="control-label" for="">IP：<?php  echo $primary_db[0]['p_host'] ?></label>
        <label name="pri_dbname" class="control-label" for=""><?php echo $this->lang->line('db_name'); ?>：<?php echo $primary_db[0]['db_name'] ?></label>
        <label name="pri_dbstatus" class="control-label" for=""><?php echo $this->lang->line('db_status'); ?>：<?php echo $primary_db[0]['open_mode'] ?></label>
        <label name="pri_port" class="control-label" for=""><?php echo $this->lang->line('db_port'); ?>：<?php echo $primary_db[0]['p_port'] ?></label>
</div>
<div class="dgc2"><img style="margin-right:70px;" src="<?php if($primary_db[0]['open_mode']==-1){echo "./images/connect_error.png";} else{echo "./images/primary_db.png";}  ?> "/></div>
<div class="dgc3">
<div class="dgc3c">
            <label name="pri_thread" class="control-label" for=""><?php echo $this->lang->line('primary_db'); ?>: </label>
            <label name="pri_scn" class="control-label" for="">当前SCN：<?php echo $primary_db[0]['p_scn'] ?></label>
            <label name="pri_time" class="control-label" for=""><?php echo $this->lang->line('db_time'); ?>：<?php echo $primary_db[0]['p_db_time'] ?></label>
            <hr>
            <?php foreach ($primary_db as $item):?>
                    <label class="control-label" for="">Thread <?php echo $item['p_thread'] ?>: sequence: <?php echo $item['p_sequence'] ?></label>
            <?php endforeach;?>
            <hr>
            <label name="pri_fb_status" class="control-label" style="<?php if($primary_db[0]['flashback_on']=='YES'){echo "display: none;";} ?>">生产库闪回状态：未启动</label>
            <label name="pri_fb_time" class="control-label" style="<?php if($primary_db[0]['flashback_on']=='NO'){echo "display: none;";} ?>">最早闪回时间：<?php echo $primary_db[0]['flashback_e_time'] ?></label>
            <label name="pri_fb_pct" class="control-label" for="">闪回空间使用率：<?php echo $primary_db[0]['flashback_space_used'] ?>%</label>
            </div>


</div>
</div>
<div class="dg2_2">
<div class="dgc2c">
<label class="control-label">Seq：<?php echo $standby_db[0]['s_sequence'] ?> block# <?php echo $standby_db[0]['s_block'] ?></label>
        <img src="
        <?php
        $second_dif=floor((strtotime($primary_db[0]['p_db_time'])-strtotime($standby_db[0]['s_db_time']))%86400%60);
        if($second_dif > 3600 ){echo "./images/trans_alarm.png";}   #时间差超过1小时，显示trans_error图片
        elseif($primary_db[0]['open_mode']==-1 or $standby_db[0]['open_mode']==-1){echo "./images/trans_error.png";}
        else{echo "./images/health_transfer.gif";}  ?> 
        "/>
</div>

</div>
<div class="dg2_3">
<div class="dgc1"><label name="sta_host" class="control-label" for="">IP：<?php  echo $standby_db[0]['s_host'] ?></label>
        <label name="sta_dbname" class="control-label" for=""><?php echo $this->lang->line('db_name'); ?>：<?php echo $standby_db[0]['db_name'] ?></label>
        <label name="sta_dbstatus" class="control-label" for=""><?php echo $this->lang->line('db_status'); ?>：<?php echo $standby_db[0]['open_mode'] ?></label>
        <label name="sta_port" class="control-label" for=""><?php echo $this->lang->line('db_port'); ?>：<?php echo $standby_db[0]['s_port'] ?></label></div>
		
<div class="dgc2"><img style="margin-left:60px;" src="<?php if($standby_db[0]['open_mode']==-1){echo "./images/connect_error.png";} else{echo "./images/standby_db.png";}  ?> "/></div>
<div class="dgc3">


<div class="dgc3c co2">
		  <label name="sta_status" class="control-label" ><?php echo $this->lang->line('standby_db'); ?>: </label>
      <label name="sta_scn" class="control-label" >当前SCN：<?php echo $standby_db[0]['s_scn'] ?></label>
      <label name="sta_time" class="control-label"  ><?php echo $this->lang->line('db_time'); ?>：<?php echo $standby_db[0]['s_db_time'] ?></label>
      <hr>
      <label name="sta_thread1" class="control-label" ><?php echo $this->lang->line('recovery_rate'); ?>: <?php echo $standby_db[0]['avg_apply_rate'] ?> KB/sec</label>
      <label name="sta_thread2" class="control-label" ><?php echo $this->lang->line('curr_recover'); ?>: thread#<?php echo $standby_db[0]['s_thread'] ?> sequence <?php echo $standby_db[0]['s_sequence'] ?> block# <?php echo $standby_db[0]['s_block'] ?></label>
      <hr>
      <label name="sta_fb_status" class="control-label" style="<?php if($standby_db[0]['flashback_on']=='YES'){echo "display: none;";} ?>">容灾库闪回状态：未启动</label>
      <label name="sta_fb_time" class="control-label" style="<?php if($standby_db[0]['flashback_on']=='NO'){echo "display: none;";} ?>">最早闪回时间：<?php echo $standby_db[0]['flashback_e_time'] ?></label>
      <label name="sta_fb_pct" class="control-label" >闪回空间使用率：<?php echo $standby_db[0]['flashback_space_used'] ?>%</label>

      <div id="mrp_warning" >
			<label id="lb_warning" class="control-label" style="color:red;"></label>
      </div>
</div>
</div>


</div>
<div style="clear:both"></div>
</div>


</div>


<script type="text/javascript">
var warningDiv = document.getElementById("mrp_warning");
var sta_db_role = "<?php echo $standby_db[0]['database_role'] ?>" ;
var mrp_status = "<?php echo $standby_db[0]['s_mrp_status'] ?>" ;

jQuery(document).ready(function(){
		if(sta_db_role=="SNAPSHOT STANDBY"){
			$("#lb_warning").html("The standby database is in Snapshot status.");
			warningDiv.style.display="block";
		}
		else if(mrp_status=="0"){
			$("#lb_warning").html("Warning: The MRP process is not running!!!");
			warningDiv.style.display="block";
		}
		else{
			warningDiv.style.display="none";
		}
		
		
});  


function refresh()
{
       window.location.reload();
}
setTimeout('refresh()',60000); //指定60秒刷新一次
</script>

