<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_OS'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">

<form name="form" class="form-horizontal" method="post" action="<?php echo site_url('cfg_os/add') ?>" >
<input type="hidden" name="submit" value="add"/> 
<div class="btn-toolbar">
    <button type="submit" class="btn btn-primary"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>
    <a class="btn btn " href="<?php echo site_url('cfg_os/index') ?>"><i class="icon-list"></i> <?php echo $this->lang->line('list'); ?></a>
  <div class="btn-group"></div>
</div>

<?php if ($error_code!==0) { ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">×</button>
<?php echo validation_errors(); ?>
</div>
<?php } ?>

<div class="well">
  
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('host'); ?></label>
    <div class="controls">
      <input type="text" id="host"  name="host" value="<?php echo set_value('host'); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <!--
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('protocol'); ?> </label>
    <div class="controls">
        <select id="protocol" name="protocol" onchange="protocol_change(this)">
         <option value="snmp"  ><?php echo $this->lang->line('snmp'); ?></option>
         <option value="winrm"  ><?php echo $this->lang->line('winrm'); ?></option>
        </select>
      <span class="help-inline"></span>
    </div>
   </div>-->
   
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('host_type'); ?> </label>
    <div class="controls">
        <select id="host_type" name="host_type" onchange="host_type_change(this)">
         <option value="0"  >Linux</option>
         <option value="1"  >AIX</option>
         <option value="2"  >HP-UX</option>
         <option value="3"  >Solaris</option>
         <option value="4"  >Windows</option>
        </select>
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div id="div_port" class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('port'); ?></label>
    <div class="controls">
      <input type="text" id="port"  name="port" value="<?php echo set_value('port'); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div id="div_user" class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('username'); ?></label>
    <div class="controls">
      <input type="text" id="username"  name="username" value="<?php echo set_value('username'); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div id="div_passwd" class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('password'); ?></label>
    <div class="controls">
      <input type="password" id="password"  name="password" value="<?php echo set_value('password'); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('tags'); ?></label>
    <div class="controls">
      <input type="text" id="tags"  name="tags" value="<?php echo set_value('tags'); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <hr />
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('monitor'); ?></label>
    <div class="controls">
        <select name="monitor" id="monitor" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
    </div>
   </div>
   
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('send_mail'); ?></label>
    <div class="controls">
        <select name="send_mail" id="send_mail" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
         &nbsp;&nbsp;<?php echo $this->lang->line('alarm_mail_to_list'); ?>
        <div class="input-prepend">
            <span class="add-on">@</span>
            <input type="text" id="send_mail_to_list"  class="input-xlarge" placeholder="<?php echo $this->lang->line('many_people_separation'); ?>" name="send_mail_to_list" value="" >
        </div>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('send_sms'); ?></label>
    <div class="controls">
        <select name="send_sms" id="send_sms" class="input-small">
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
        </select>
         &nbsp;&nbsp;<?php echo $this->lang->line('alarm_sms_to_list'); ?>
        <div class="input-prepend">
            <span class="add-on">@</span>
            <input type="text" id="send_sms_to_list"  class="input-xlarge" placeholder="<?php echo $this->lang->line('many_people_separation'); ?>" name="send_sms_to_list" value="" >
        </div>
    </div>
    </div>
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('send_wx'); ?></label>
    <div class="controls">
        <select name="send_wx" id="send_wx" class="input-small">
         <option value="1"  <?php echo set_selected(1,$record['send_wx']) ?>><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  <?php echo set_selected(0,$record['send_wx']) ?>><?php echo $this->lang->line('off'); ?></option>
        </select>
    </div>
   </div>
   
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('process'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_os_process" id="alarm_os_process" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_os_process" class="input-small" placeholder="" name="threshold_warning_os_process" value="300" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_os_process" class="input-small" placeholder="" name="threshold_critical_os_process" value="500" >
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('load'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_os_load" id="alarm_os_load" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_os_load" class="input-small" placeholder="" name="threshold_warning_os_load" value="3" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_os_load" class="input-small" placeholder="" name="threshold_critical_os_load" value="10" >
    </div>
   </div>
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('network'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_os_network" id="alarm_os_network" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_os_network" class="input-small" placeholder="" name="threshold_warning_os_network" value="2" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_os_network" class="input-small" placeholder="" name="threshold_critical_os_network" value="10" >
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('cpu'); ?> <?php echo $this->lang->line('usage_rate'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_os_cpu" id="alarm_os_cpu" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_os_cpu" class="input-small" placeholder="" name="threshold_warning_os_cpu" value="20" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_os_cpu" class="input-small" placeholder="" name="threshold_critical_os_cpu" value="60" >%
    </div>
   </div>
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('disk'); ?> <?php echo $this->lang->line('usage_rate'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_os_disk" id="alarm_os_disk" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_os_disk" class="input-small" placeholder="" name="threshold_warning_os_disk" value="75" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_os_disk" class="input-small" placeholder="" name="threshold_critical_os_disk" value="90" >% &nbsp;&nbsp;<?php echo $this->lang->line('filter'); ?> <?php echo $this->lang->line('disk'); ?>&nbsp;<input type="text" id="filter_os_disk" class="input-large" placeholder="" name="filter_os_disk" value="" >
    </div>
   </div>
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('memory'); ?> <?php echo $this->lang->line('usage_rate'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_os_memory" id="alarm_os_memory" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_os_memory" class="input-small" placeholder="" name="threshold_warning_os_memory" value="85" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_os_memory" class="input-small" placeholder="" name="threshold_critical_os_memory" value="95" >%
    </div>
   </div>
  
   
   
</div>


</form>

<script type="text/javascript">
$(document).ready(function(){
		$("#div_user").hide();
		$("#div_passwd").hide();
		$("#div_port").hide();
		$("#username").val("");
		$("#password").val("");
		$("#port").val("");
		
});

function host_type_change(e){
		if(e.value == "4"){
				$("#div_user").show();
				$("#div_passwd").show();
				$("#div_port").show();
				$("#port").val("5985");
		}
		else{
				$("#div_port").hide();
				$("#div_user").hide();
				$("#div_passwd").hide();
				$("#username").val("");
				$("#password").val("");
				$("#port").val("");
		}
}
</script>
