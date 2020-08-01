<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Oracle'); ?></li>
</ul>

<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/layer3/layer.js"></script>

<div class="container-fluid">
<div class="row-fluid">

<form name="form" class="form-horizontal" method="post" action="<?php echo site_url('cfg_oracle/add') ?>" >
<input type="hidden" name="submit" value="add"/> 
<div class="btn-toolbar">
    <button type="submit" class="btn btn-primary"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>
    <a class="btn btn " href="<?php echo site_url('cfg_oracle/index') ?>"><i class="icon-list"></i> <?php echo $this->lang->line('list'); ?></a>
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
      <input type="text" id="host"  name="host" value="<?php echo set_value('host',''); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('port'); ?></label>
    <div class="controls">
      <input type="text" id="port"  name="port" value="<?php echo set_value('port','1521'); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('dsn'); ?></label>
    <div class="controls">
      <input type="text" id="dsn"  name="dsn" value="<?php echo set_value('dsn',''); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('db_user'); ?></label>
    <div class="controls">
      <input type="text" id="username"  name="username" value="<?php echo set_value('username',''); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('db_pwd'); ?></label>
    <div class="controls">
      <input type="password" id="password"  name="password" value="<?php echo set_value('password',''); ?>" >
      <button name="conn_check" type="button" value="conn_check" onclick="CheckConnect(this)"  class="btn btn-success"><?php echo $this->lang->line('connect_test'); ?></button>
    	<span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('tags'); ?></label>
    <div class="controls">
      <input type="text" id=""  name="tags" value="<?php echo set_value('tags',''); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>

   
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('host_type'); ?></label>
    <div class="controls">
        <select name="host_type" id="host_type" class="input-medium">
         <option value="0"  >Linux</option>
         <option value="1"  >AIX</option>
         <option value="2"  >HP-UX</option>
         <option value="3"  >Solaris</option>
         <option value="4"  >Windows</option>
        </select>
    </div>
    </div>
    
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('host_user'); ?></label>
    <div class="controls">
      <input type="text" id="host_user"  name="host_user" value="<?php echo set_value('host_user',''); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>

   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('host_pwd'); ?></label>
    <div class="controls">
      <input type="password" id="host_pwd"  name="host_pwd" value="<?php echo set_value('host_pwd',''); ?>" >
      <span class="help-inline"></span>
    </div>
   </div>

    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('host_protocol'); ?></label>
    <div class="controls">
        <select name="host_protocol" id="host_protocol" class="input-medium">
         <option value="0"  >ssh2</option>
         <option value="1"  >telnet</option>
        </select>
    </div>
    </div>
    
    
   <hr />
   
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('monitor'); ?></label>
    <div class="controls">
        <select name="monitor" id="status" class="input-small">
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
    <label class="control-label" for=""><?php echo $this->lang->line('session_total'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_session_total" id="alarm_session_total" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_session_total" class="input-small" placeholder="" name="threshold_warning_session_total" value="1000" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_session_total" class="input-small" placeholder="" name="threshold_critical_session_total" value="3000" >
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('session_actives'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_session_actives" id="alarm_session_actives" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_session_actives" class="input-small" placeholder="" name="threshold_warning_session_actives" value="10" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_session_actives" class="input-small" placeholder="" name="threshold_critical_session_actives" value="30" >
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('session_waits'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_session_waits" id="alarm_session_waits" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_session_waits" class="input-small" placeholder="" name="threshold_warning_session_waits" value="5" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_session_waits" class="input-small" placeholder="" name="threshold_critical_session_waits" value="15" >
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('tbs'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_tablespace" id="alarm_tablespace" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_tablespace" class="input-small" placeholder="" name="threshold_warning_tablespace" value="85" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_tablespace" class="input-small" placeholder="" name="threshold_critical_tablespace" value="95" >% &nbsp;&nbsp;<?php echo $this->lang->line('filter'); ?><?php echo $this->lang->line('tbs'); ?>&nbsp;<input type="text" id="filter_tbs" class="input-large" placeholder="" name="filter_tbs" value="" >
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('fb_space'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_fb_space" id="alarm_fb_space" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_fb_space" class="input-small" placeholder="" name="threshold_warning_fb_space" value="85" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_fb_space" class="input-small" placeholder="" name="threshold_critical_fb_space" value="95" >%
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('asm_space'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_asm_space" id="alarm_asm_space" class="input-small">
         <option value="1"  ><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_asm_space" class="input-small" placeholder="" name="threshold_warning_asm_space" value="85" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_asm_space" class="input-small" placeholder="" name="threshold_critical_asm_space" value="95" >%
    </div>
   </div>
   
</div>


</form>


<script type="text/javascript">
var target_url = "<?php echo site_url('cfg_oracle/check_connection') ?>";

 
function CheckConnect(e){
		var t_ip = $("#host").val();
		var t_port = $("#port").val();
		var t_dsn = $("#dsn").val();
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
				        	
		}else if($("#dsn").val() == ""){
				bootbox.alert({
		        		message: "请输入数据源!",
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
	                data: {"ip":t_ip,"port":t_port,"dsn":t_dsn,"username":t_username,"password":t_passwd},
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