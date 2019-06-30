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

<form name="form" class="form-horizontal" method="post" action="<?php echo site_url('cfg_oracle/edit') ?>" >
<input type="hidden" name="submit" value="edit"/> 
<input type='hidden'  name='id' value=<?php echo $record['id'] ?> />
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
      <input type="text" id="host"  name="host" value="<?php echo $record['host']; ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('port'); ?></label>
    <div class="controls">
      <input type="text" id="port"  name="port" value="<?php echo $record['port']; ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('dsn'); ?></label>
    <div class="controls">
      <input type="text" id="dsn"  name="dsn" value="<?php echo $record['dsn']; ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('db_user'); ?></label>
    <div class="controls">
      <input type="text" id="username"  name="username" value="<?php echo $record['username']; ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('db_pwd'); ?></label>
    <div class="controls">
      <input type="password" id="password"  name="password" value="<?php echo $record['password']; ?>" >
      <span class="help-inline"></span>
    	<button name="conn_check" type="button" value="conn_check" onclick="CheckConnect(this)"  class="btn btn-success"><?php echo $this->lang->line('connect_test'); ?></button>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('tags'); ?></label>
    <div class="controls">
      <input type="text" id=""  name="tags" value="<?php echo $record['tags']; ?>" >
      <span class="help-inline"></span>
    </div>
   </div>


   
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('host_type'); ?></label>
    <div class="controls">
        <select name="host_type" id="host_type" class="input-medium">
         <option value="0" <?php echo set_selected(0,$record['host_type']) ?>>Linux</option>
         <option value="1" <?php echo set_selected(1,$record['host_type']) ?>>AIX</option>
         <option value="2" <?php echo set_selected(2,$record['host_type']) ?>>HP-UX</option>
         <option value="3" <?php echo set_selected(3,$record['host_type']) ?>>Solaris</option>
         <option value="4" <?php echo set_selected(4,$record['host_type']) ?>>Windows</option>
        </select>
    </div>
    </div>
    
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('host_user'); ?></label>
    <div class="controls">
      <input type="text" id="host_user"  name="host_user" value="<?php echo $record['host_user']; ?>" >
      <span class="help-inline"></span>
    </div>
   </div>

   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('host_pwd'); ?></label>
    <div class="controls">
      <input type="password" id="host_pwd" name="host_pwd" value="<?php echo $record['host_pwd']; ?>" >
      <span class="help-inline"></span>
    </div>
   </div>

    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('host_protocol'); ?></label>
    <div class="controls">
        <select name="host_protocol" id="host_protocol" class="input-medium">
         <option value="0" <?php echo set_selected(0,$record['host_protocol']) ?>>ssh2</option>
         <option value="1" <?php echo set_selected(1,$record['host_protocol']) ?>>telnet</option>
        </select>
    </div>
    </div>
    
   <hr />
   
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('monitor'); ?></label>
    <div class="controls">
        <select name="monitor" id="monitor" class="input-small">
         <option value="1"  <?php echo set_selected(1,$record['monitor']) ?>><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  <?php echo set_selected(0,$record['monitor']) ?>><?php echo $this->lang->line('off'); ?></option>
        </select>
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('send_mail'); ?></label>
    <div class="controls">
        <select name="send_mail" id="send_mail" class="input-small">
         <option value="1"  <?php echo set_selected(1,$record['send_mail']) ?>><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  <?php echo set_selected(0,$record['send_mail']) ?>><?php echo $this->lang->line('off'); ?></option>
        </select>
         &nbsp;&nbsp;<?php echo $this->lang->line('alarm_mail_to_list'); ?>
        <div class="input-prepend">
            <span class="add-on">@</span>
            <input type="text" id="send_mail_to_list"  class="input-xlarge" placeholder="<?php echo $this->lang->line('many_people_separation'); ?>" name="send_mail_to_list" value="<?php echo $record['send_mail_to_list']; ?>" >
        </div>
    </div>
   </div>
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('send_sms'); ?></label>
    <div class="controls">
        <select name="send_sms" id="send_sms" class="input-small">
         <option value="1"  <?php echo set_selected(1,$record['send_sms']) ?>><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  <?php echo set_selected(0,$record['send_sms']) ?>><?php echo $this->lang->line('off'); ?></option>
        </select>
         &nbsp;&nbsp;<?php echo $this->lang->line('alarm_sms_to_list'); ?>
        <div class="input-prepend">
            <span class="add-on">@</span>
            <input type="text" id="send_sms_to_list"  class="input-xlarge" placeholder="<?php echo $this->lang->line('many_people_separation'); ?>" name="send_sms_to_list" value="<?php echo $record['send_sms_to_list']; ?>" >
        </div>
    </div>
   </div>
   
   
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('session_total'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_session_total" id="alarm_session_total" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_session_total']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_session_total']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_session_total" class="input-small" placeholder="" name="threshold_warning_session_total" value="<?php echo $record['threshold_warning_session_total']; ?>" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_session_total" class="input-small" placeholder="" name="threshold_critical_session_total" value="<?php echo $record['threshold_critical_session_total']; ?>" >
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('session_actives'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_session_actives" id="alarm_session_actives" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_session_actives']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_session_actives']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_session_actives" class="input-small" placeholder="" name="threshold_warning_session_actives" value="<?php echo $record['threshold_warning_session_actives']; ?>" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_session_actives" class="input-small" placeholder="" name="threshold_critical_session_actives" value="<?php echo $record['threshold_critical_session_actives']; ?>" >
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('session_waits'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_session_waits" id="alarm_session_waits" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_session_waits']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_session_waits']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_session_waits" class="input-small" placeholder="" name="threshold_warning_session_waits" value="<?php echo $record['threshold_warning_session_waits']; ?>" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_session_waits" class="input-small" placeholder="" name="threshold_critical_session_waits" value="<?php echo $record['threshold_critical_session_waits']; ?>" >
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('tbs'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_tablespace" id="alarm_tablespace" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_tablespace']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_tablespace']) ?>><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_tablespace" class="input-small" placeholder="" name="threshold_warning_tablespace" value="<?php echo $record['threshold_warning_tablespace']; ?>" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_tablespace" class="input-small" placeholder="" name="threshold_critical_tablespace" value="<?php echo $record['threshold_critical_tablespace']; ?>" >% &nbsp;&nbsp;<?php echo $this->lang->line('filter'); ?><?php echo $this->lang->line('tbs'); ?>&nbsp;<input type="text" id="filter_tbs" class="input-large" placeholder="" name="filter_tbs" value="<?php echo $record['filter_tbs']; ?>" >
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('fb_space'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_fb_space" id="alarm_fb_space" class="input-small">
         <option value="1"  <?php echo set_selected(1,$record['alarm_fb_space']) ?>><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  <?php echo set_selected(0,$record['alarm_fb_space']) ?>><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_fb_space" class="input-small" placeholder="" name="threshold_warning_fb_space" value="<?php echo $record['threshold_warning_fb_space']; ?>" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_fb_space" class="input-small" placeholder="" name="threshold_critical_fb_space" value="<?php echo $record['threshold_critical_fb_space']; ?>" >%
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('asm_space'); ?>  <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_asm_space" id="alarm_asm_space" class="input-small">
         <option value="1"  <?php echo set_selected(1,$record['alarm_asm_space']) ?>><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  <?php echo set_selected(0,$record['alarm_asm_space']) ?>><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_asm_space" class="input-small" placeholder="" name="threshold_warning_asm_space" value="<?php echo $record['threshold_warning_asm_space']; ?>" >%
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_asm_space" class="input-small" placeholder="" name="threshold_critical_asm_space" value="<?php echo $record['threshold_critical_asm_space']; ?>" >%
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