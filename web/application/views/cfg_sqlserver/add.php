<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_SQLServer'); ?></li>
</ul>

<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/layer3/layer.js"></script>

<div class="container-fluid">
<div class="row-fluid">

<form name="form" class="form-horizontal" method="post" action="<?php echo site_url('cfg_sqlserver/add') ?>" >
<input type="hidden" name="submit" value="add"/> 
<div class="btn-toolbar">
    <button type="submit" class="btn btn-primary"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>
    <a class="btn btn " href="<?php echo site_url('cfg_sqlserver/index') ?>"><i class="icon-list"></i> <?php echo $this->lang->line('list'); ?></a>
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
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('port'); ?></label>
    <div class="controls">
      <input type="text" id="port"  name="port" value="<?php echo set_value('port','1433'); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>

    <div class="control-group">
        <label class="control-label" for="">*<?php echo $this->lang->line('username'); ?></label>
        <div class="controls">
            <input type="text" id="username"  name="username" value="<?php echo set_value('username'); ?>" >
            <span class="help-inline"></span>
        </div>
    </div>
   
   
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('password'); ?></label>
    <div class="controls">
      <input type="password" id="password"  name="password" value="<?php echo set_value('password'); ?>" >
      <button name="conn_check" type="button" value="conn_check" onclick="CheckConnect(this)"  class="btn btn-success"><?php echo $this->lang->line('connect_test'); ?></button>
    <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('tags'); ?></label>
    <div class="controls">
      <input type="text" id=""  name="tags" value="<?php echo set_value('tags'); ?>" >
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
    <label class="control-label" for=""><?php echo $this->lang->line('processes'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_processes" id="alarm_processes" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_processes" class="input-small" placeholder="" name="threshold_warning_processes" value="1000" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_processes" class="input-small" placeholder="" name="threshold_critical_processes" value="3000" >
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('processes_running'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_processes_running" id="alarm_processes_running" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_processes_running" class="input-small" placeholder="" name="threshold_warning_processes_running" value="10" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_processes_running" class="input-small" placeholder="" name="threshold_critical_processes_running" value="30" >
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('processes_waits'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_processes_waits" id="alarm_processes_waits" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_processes_waits" class="input-small" placeholder="" name="threshold_warning_processes_waits" value="5" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_processes_waits" class="input-small" placeholder="" name="threshold_critical_processes_waits" value="15" >
    </div>
   </div>
   
   
</div>


</form>


<script type="text/javascript">
var target_url = "<?php echo site_url('cfg_sqlserver/check_connection') ?>";

 
function CheckConnect(e){
		var t_ip = $("#host").val();
		var t_port = $("#port").val();
		var t_username = $("#username").val();
		var t_passwd = $("#password").val();

		if($("#host").val() == ""){
				bootbox.alert({
		        		message: "请输入主机IP!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
				        	
		}else if($("#port").val() == ""){
				bootbox.alert({
		        		message: "请输入端口号!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
				        	
		}else if($("#username").val() == ""){
				bootbox.alert({
		        		message: "请输入用户名!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
				        	
		}else if($("#password").val() == ""){
				bootbox.alert({
		        		message: "请输入密码!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
				        	
		  }else{
	      $.ajax({
	                url: target_url,
	                data: $("#form").serializeArray(),
	                data: {"ip":t_ip,"port":t_port,"username":t_username,"password":t_passwd},
	                type: "POST",
	                success: function (data) {
	          			//回调函数，判断提交返回的数据执行相应逻辑
		          			if(data.connect==0){
		  									bootbox.alert({
								        		message: "数据库连接成功!",
								        		buttons: {
													        ok: {
													            label: '确定',
													            className: 'btn-success'
													        }
													    }
								        	});
		          			}else{
		  									bootbox.alert({
								        		message: "数据库连接失败，请联系管理员!",
								        		buttons: {
													        ok: {
													            label: '确定',
													            className: 'btn-success'
													        }
													    }
								        	});
		          			}
	                }
								});		
		  }
		  
		          	
	

}



</script>