<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
   
<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_MySQL'); ?></li>
</ul>

<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/layer3/layer.js"></script>

<div class="container-fluid">
<div class="row-fluid">

<form name="form" class="form-horizontal" method="post" action="<?php echo site_url('cfg_mysql/edit') ?>" >
<input type="hidden" name="submit" value="edit"/> 
<input type='hidden'  name='id' value=<?php echo $record['id'] ?> />
<div class="btn-toolbar">
    <button type="submit" class="btn btn-primary"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>
    <a class="btn btn " href="<?php echo site_url('cfg_mysql/index') ?>"><i class="icon-list"></i> <?php echo $this->lang->line('list'); ?></a>
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
    <label class="control-label" for="">*<?php echo $this->lang->line('username'); ?></label>
    <div class="controls">
      <input type="text" id="username"  name="username" value="<?php echo $record['username']; ?>" >
      <span class="help-inline"></span>
    </div>
   </div>
   
   <div class="control-group">
    <label class="control-label" for="">*<?php echo $this->lang->line('password'); ?></label>
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
    <label class="control-label" for=""><?php echo $this->lang->line('send_wx'); ?></label>
    <div class="controls">
        <select name="send_wx" id="send_wx" class="input-small">
         <option value="1"  <?php echo set_selected(1,$record['send_wx']) ?>><?php echo $this->lang->line('on'); ?></option>
         <option value="0"  <?php echo set_selected(0,$record['send_wx']) ?>><?php echo $this->lang->line('off'); ?></option>
        </select>
    </div>
   </div>
   
   <div class="control-group" style="display:none">
    <label class="control-label" for=""><?php echo $this->lang->line('slowquery'); ?></label>
    <div class="controls">
        <select name="slow_query" id="slow_query" class="input-small">
         <option value="1"  <?php echo set_selected(1,$record['slow_query']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['slow_query']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('slowquery_send_mail_to'); ?>
        <div class="input-prepend">
            <span class="add-on">@</span>
            <input type="text" id="send_slowquery_to_list"  class="input-xlarge" placeholder="<?php echo $this->lang->line('many_people_separation'); ?>" name="send_slowquery_to_list" value="<?php echo $record['send_slowquery_to_list']; ?>" >
        </div>
    </div>
    </div>
     <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('_BigTable Analysis'); ?></label>
    <div class="controls">
        <select name="bigtable_monitor" id="bigtable_monitor" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['bigtable_monitor']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['bigtable_monitor']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold'); ?>&nbsp;<input type="text" id="bigtable_size" class="input-small" placeholder="" name="bigtable_size" value="<?php echo $record['bigtable_size']; ?>" >&nbsp;&nbsp;MB
    </div>
    </div>
    
    <div class="control-group" style="display:none">
    <label class="control-label" for=""><?php echo $this->lang->line('binlog_auto_purge'); ?></label>
    <div class="controls">
        <select name="binlog_auto_purge" id="binlog_auto_purge" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['binlog_auto_purge']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['binlog_auto_purge']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('binlog_store_days'); ?>&nbsp;<input type="text" id="binlog_store_days" class="input-small"  name="binlog_store_days" value="<?php echo $record['binlog_store_days']; ?>" >
    </div>
   </div>
    
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('threads_connected'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_threads_connected" id="alarm_threads_connected" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_threads_connected']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_threads_connected']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_threads_connected" class="input-small" placeholder="" name="threshold_warning_threads_connected" value="<?php echo $record['threshold_warning_threads_connected']; ?>" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_threads_connected" class="input-small" placeholder="" name="threshold_critical_threads_connected" value="<?php echo $record['threshold_critical_threads_connected']; ?>" >
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('threads_running'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_threads_running" id="alarm_threads_running" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_threads_running']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_threads_running']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_threads_running" class="input-small" placeholder="" name="threshold_warning_threads_running" value="<?php echo $record['threshold_warning_threads_running']; ?>" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_threads_running" class="input-small" placeholder="" name="threshold_critical_threads_running" value="<?php echo $record['threshold_critical_threads_running']; ?>" >
    </div>
   </div>
   <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('threads_waits'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_threads_waits" id="alarm_threads_waits" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_threads_waits']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_threads_waits']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_threads_waits" class="input-small" placeholder="" name="threshold_warning_threads_waits" value="<?php echo $record['threshold_warning_threads_waits']; ?>" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_threads_waits" class="input-small" placeholder="" name="threshold_critical_threads_waits" value="<?php echo $record['threshold_critical_threads_waits']; ?>" >
    </div>
   </div>
   
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('replication'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_repl_status" id="alarm_repl_status" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_repl_status']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_repl_status']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
    </div>
   </div>
    <div class="control-group">
    <label class="control-label" for=""><?php echo $this->lang->line('delay'); ?> <?php echo $this->lang->line('alarm'); ?></label>
    <div class="controls">
        <select name="alarm_repl_delay" id="alarm_repl_delay" class="input-small">
         <option value="1" <?php echo set_selected(1,$record['alarm_repl_delay']) ?> ><?php echo $this->lang->line('on'); ?></option>
         <option value="0" <?php echo set_selected(0,$record['alarm_repl_delay']) ?> ><?php echo $this->lang->line('off'); ?></option>
        </select>
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_warning'); ?>&nbsp;<input type="text" id="threshold_warning_repl_delay" class="input-small" placeholder="" name="threshold_warning_repl_delay" value="<?php echo $record['threshold_warning_repl_delay']; ?>" >
        &nbsp;&nbsp;<?php echo $this->lang->line('threshold_critical'); ?>&nbsp;<input type="text" id="threshold_critical_repl_delay" class="input-small" placeholder="" name="threshold_critical_repl_delay" value="<?php echo $record['threshold_critical_repl_delay']; ?>" >
    </div>
    </div>
  
   
</div>

</form>


<script type="text/javascript">
var target_url = "<?php echo site_url('cfg_mysql/check_connection') ?>";

 
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
