<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_mysql/index'); ?>"><?php echo $this->lang->line('_Oracle Monitor'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><a href="<?php echo site_url('wl_mysql/replication'); ?>"><?php echo $this->lang->line('_DisasterRecovery List'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DisasterRecovery Manage'); ?></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>: <?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<!-- <div class="container-fluid">
<div class="row-fluid"> -->
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/layer3/layer.js"></script>
<script src="lib/bootstrap/js/md5.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>


<style>

.layblack {background:#000 !important;}
.layblack .layui-layer-content{padding:20px !important;color:#fff !important;}
</style>

<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 


<div>
<form id="form_switch" class="form-inline" method="post" action="" >
    <a class="btn btn " href="<?php echo site_url('wl_mysql/replication') ?>"><i class="icon-return"></i> <?php echo $this->lang->line('return'); ?></a>
    <button name="trans_type" type="button" value="Failover" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right:5px;"><?php echo $this->lang->line('failover'); ?></button>
    <button name="trans_type" type="button" value="Switchover" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"><?php echo $this->lang->line('switchover'); ?></button>

</form>
</div>
</div>



<div style="padding: 19px; <?php if($setval['id']!=""){echo "display:none;";} ?>" >
	<tr>
<td colspan="12">
<font color="red"><?php echo $this->lang->line('no_record'); ?></font>
</td>
</tr>
</div>


<div style="padding: 19px; <?php if($setval['id']==""){echo "display:none;";} ?>" >
    <div style='padding: 20px 120px 0px 60px; height:200px; overflow:hidden'>
        <div style='float:left; height:200px; width:280px;'>
        <label name="pri_host" class="control-label" for="">IP：<?php  echo $primary_db[0]['p_host'] ?></label>
        <label name="pri_port" class="control-label" for=""><?php echo $this->lang->line('db_port'); ?>：<?php echo $primary_db[0]['p_port'] ?></label>
        <label name="pri_version" class="control-label" for=""><?php echo $this->lang->line('db_version'); ?>：<?php echo $primary_db[0]['p_db_version'] ?></label>
        <label name="pri_binlog_file" class="control-label" for=""><?php echo $this->lang->line('binary_logs'); ?>：<?php echo $standby_db[0]['m_binlog_file'] ?></label>
        <label name="pri_binlog_pos" class="control-label" for=""><?php echo $this->lang->line('postion'); ?>：<?php echo $standby_db[0]['m_binlog_pos'] ?></label>
        <label name="pri_binlog_space" class="control-label" for=""><?php echo $this->lang->line('binlog_space'); ?>：<?php echo $primary_db[0]['p_binlog_space'] ?></label>
        </div>
        <div style='float:right; height:200px; width:280px;'>
        <label name="sta_host" class="control-label" for="">IP：<?php  echo $standby_db[0]['s_host'] ?></label>
        <label name="sta_port" class="control-label" for=""><?php echo $this->lang->line('db_port'); ?>：<?php echo $standby_db[0]['s_port'] ?></label>
        <label name="sta_version" class="control-label" for=""><?php echo $this->lang->line('db_version'); ?>：<?php echo $standby_db[0]['s_db_version'] ?></label>
        <label name="sta_binlog_file" class="control-label" for=""><?php echo $this->lang->line('binary_logs'); ?>：<?php echo $standby_db[0]['s_binlog_file'] ?></label>
        <label name="sta_binlog_pos" class="control-label" for=""><?php echo $this->lang->line('postion'); ?>：<?php echo $standby_db[0]['s_binlog_pos'] ?></label>
        <label name="sta_delay" class="control-label" for=""><?php echo $this->lang->line('delay'); ?>：<?php echo $standby_db[0]['delay'] ?></label>
        </div>
    </div>


<div style='padding: 5px 0px 0px 200px; height:150px;'>
    <div style="float:left;"><img src="<?php if($primary_db[0]['p_connect']==0){echo "./images/connect_error.png";} else{echo "./images/primary_db.png";}  ?> "/></div> 

        <div style="float:left;">
        <label style='padding: 0px 0px 0px 120px;' class="control-label" for="">Binlog: <?php echo $standby_db[0]['s_binlog_file'] ?></label>
        <img src="
        <?php
        $delay=$standby_db[0]['delay'];
        if($delay > 3600*24 ){echo "./images/trans_error.png";}   #时间差超过1小时，显示trans_error图片
        elseif($delay > 3600){echo "./images/trans_alarm.png";}
        else{echo "./images/health_transfer.gif";}  ?> 
        "/>
        </div> 

        
        <div style="float:left;"><img src="<?php if($standby_db[0]['s_connect']==0){echo "./images/connect_error.png";} else{echo "./images/standby_db.png";}  ?> "/></div> 
    </div>


		<div style="float:left; width:265px; height:30px; border:0px solid red;">
		</div>
		<div id="mrp_warning" style="float:left; width:400px; height:30px; border:0px solid red; color:red; display: none;} ?>">
			<label id="lb_warning" class="control-label" style="font-size:18px;color:red; padding: 5px 0px 0px 20px;"></label>
		</div>
		
</div>  

<div id="div_layer" style="display:none" ></div>


<script type="text/javascript">
var base_url = "<?php echo site_url('wl_mysql/dr_switch?group_id=') ?>";
var group_id = "<?php echo $setval['id'] ?>";
var target_url = base_url.toString() + group_id.toString();
var user_pwd = "<?php echo $userdata['password'] ?>" ;


var mylay = null;
var oTimer = null; 
var last_time = null;
var current_time = null;

var div_layer = document.getElementById("div_layer");
var query_url="<?php echo site_url('wl_mysql/dr_progress?group_id=') ?>" + group_id.toString();
var on_process="<?php echo $mirror_group[0]['on_process'] ?>" ;
var on_switchover="<?php echo $mirror_group[0]['on_switchover'] ?>" ;
var on_failover="<?php echo $mirror_group[0]['on_failover'] ?>" ;
    
function checkUser(e){

		if(e.value == "Switchover"){
			_message = "确认要开始主备切换吗？";
		}
		else if(e.value == "Failover"){
			_message = "确认要开始灾难切换吗？";
		}
		else{
			_message = "";
		}

    	
		        	
		bootbox.prompt({
		    title: "请输入管理员密码!",
		    inputType: 'password',
		    callback: function (result) {
		    	if(result)
		    	{ 
		        if (md5(result) == user_pwd)
		        {
							bootbox.dialog({
							    message: _message,
							    buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-danger',
													callback: function(){
																
		                            $.ajax({
											                    url: target_url,
											                    data: $("#form_switch").serializeArray(),
											                    data: "op_action=" + e.value,
											                    type: "POST",
											                    success: function (data) {
											              			//回调函数，判断提交返回的数据执行相应逻辑
											                        if (data.Success) {
											                        }
											                        else {
											                        }
											                    }
		                										});
		            
																	
																	$('#div_layer').html("");			//初始化div
																	mylay = layer.open({
																	  type: 1,
																	  skin: 'layui-layer-demo layblack', //样式类名
																	  closeBtn: 0, //不显示关闭按钮
																	  anim: 1,
																	  title: '详细步骤',
																	  area: ['450px', '240px'],
																	  shadeClose: false, //开启遮罩关闭
																	  content: $('#div_layer')
																	});
																	
																	query_act_url = query_url + "&op_action=" + e.value;
																	oTimer = setInterval("queryHandle(query_act_url)",2000);
		                        }
							        },
							        cancel: {
							            label: '取消',
							            className: 'btn-default',
							            callback: function () {
		                      }
							        }
							    }
							});
		        }
		        else
		        {
		        	bootbox.alert({
		        		message: "密码不对，请确认后重新尝试!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
		        }
		      }
		
		    }
		});

}

  
jQuery(document).ready(function(){

});  
  
function queryHandle(url){
    $.post(url, {group_id:group_id}, function(json){
        //var status = 1;
        
        
        if(json.on_process == '0'){
        		if(json.op_type != ""){
		        		var l_reason = JSON.stringify(json.op_reason)
		        		//alert(l_reason);
		        		
		        		if(json.op_type == "SWITCHOVER"){
		    						if(l_reason == 'null'){
		    								error_message = "主备切换失败，详细原因请查看相关日志";
		    						}else{
		    								error_message = "主备切换失败，原因是：" + json.op_reason;
		    						}
		    						
		    						ok_message = "主备切换成功";
		        		}else if(json.op_type == "FAILOVER"){
		    						if(l_reason == 'null'){
		    								error_message = "灾难切换失败，详细原因请查看相关日志";
		    						}else{
		    								error_message = "灾难切换失败，原因是：" + json.op_reason;
		    						}
		    						
		    						ok_message = "灾难切换成功";
		        		}
		        		
		        		
        				if(json.op_result == '-1'){
				        		bootbox.alert({
						        		message: error_message,
						        		buttons: {
											        ok: {
											            label: '确定',
											            className: 'btn-success'
											        }
											    },
										    callback: function () {
										        window.location.reload();
										    }
						        	});
						        	
				        		if(mylay!=null){
				        			layer.close(mylay);
				        		}
		        				clearInterval(oTimer); 
						        	
        				}else if(json.op_result == '0'){
				        		bootbox.alert({
						        		message: ok_message,
						        		buttons: {
											        ok: {
											            label: '确定',
											            className: 'btn-success'
											        }
											    },
										    callback: function () {
										        window.location.reload();
										    }
						        	});
						        	
				        		if(mylay!=null){
				        			layer.close(mylay);
				        		}
		        				clearInterval(oTimer); 
        				}
        		}
        			
        		
        		
        		
            
        }else{

        	current_time = json.process_time;
        	if(current_time != last_time){
        			$("#div_layer").append("<p>" + json.process_time + ": " + json.process_desc + "</p>");
        			$(".layui-layer-content").scrollTop($(".layui-layer-content")[0].scrollHeight);

        	}
        	last_time = current_time;
        }  
    },'json');  
}  

</script>


