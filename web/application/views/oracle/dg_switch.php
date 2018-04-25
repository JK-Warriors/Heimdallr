<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_oracle/index'); ?>"><?php echo $this->lang->line('_Oracle Monitor'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><a href="<?php echo site_url('wl_oracle/dglist'); ?>"><?php echo $this->lang->line('_DataGuard List'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><a href="<?php echo site_url('wl_oracle/dataguard'); ?>"><?php echo $this->lang->line('_DataGuard Detail'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DataGuard Manage'); ?></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
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
    <a class="btn btn " href="<?php echo site_url('wl_oracle/dataguard') ?>"><i class="icon-return"></i> <?php echo $this->lang->line('return'); ?></a>

    
    <button name="trans_type" type="button" value="Failover" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right:5px;"><?php echo $this->lang->line('failover'); ?></button>
    <button name="trans_type" type="button" value="Switchover" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"><?php echo $this->lang->line('switchover'); ?></button>

		<button name="mrp_action" type="button" value="MRPStop" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"><?php echo $this->lang->line('stop_mrp'); ?></button>
		<button name="mrp_action" type="button" value="MRPStart" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right:5px;"><?php echo $this->lang->line('start_mrp'); ?></button>
    
		<button name="mrp_action" type="button" value="SnapshotStop" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"><?php echo $this->lang->line('stop_snapshot'); ?></button>
		<button name="mrp_action" type="button" value="SnapshotStart" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right:5px;"><?php echo $this->lang->line('start_snapshot'); ?></button>
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
        <img src="
        <?php
        $second_dif=floor((strtotime($primary_db[0]['p_db_time'])-strtotime($standby_db[0]['s_db_time']))%86400%60);
        if($second_dif > 3600 ){echo "./images/trans_alarm.png";}   #时间差超过1小时，显示trans_error图片
        elseif($primary_db[0]['open_mode']==-1 or $standby_db[0]['open_mode']==-1){echo "./images/trans_error.png";}
        else{echo "./images/health_transfer.gif";}  ?> 
        "/>
        </div> 
        
        <!-- <div style="float:left;"><img src="./images/standby_db.png"/></div>  -->
        
        <div style="float:left;"><img src="<?php if($standby_db[0]['open_mode']==-1){echo "./images/connect_error.png";} else{echo "./images/standby_db.png";}  ?> "/></div> 
    </div>


		<div style="float:left; width:265px; height:30px; border:0px solid red;">
		</div>
		<div id="mrp_warning" style="float:left; width:400px; height:30px; border:0px solid red; color:red; <?php if($standby_db[0]['s_mrp_status']==1){echo "display: none;";} ?>">
			<label id="lb_warning" class="control-label" style="font-size:18px;color:red; padding: 5px 0px 0px 20px;"></label>
		</div>
		
</div>  

<div id="div_layer" style="display:none" ></div>
<label name="test1" class="control-label" style="display:none;">调试信息1：<?php echo $setval['python'] ?></label>
<label name="test2" class="control-label" style="display:none;">调试信息2：<?php echo $setval['test'] ?></label>


<script type="text/javascript">
var base_url = "<?php echo site_url('wl_oracle/dg_switch?dg_group_id=') ?>";
var group_id = "<?php echo $setval['id'] ?>";
var target_url = base_url.toString() + group_id.toString();
var dg_url = "<?php echo site_url('wl_oracle/dataguard?dg_group_id=') ?>" + group_id.toString();
var user_pwd = "<?php echo $userdata['password'] ?>" ;
var sta_version = "<?php echo $standby_db[0]['db_version'] ?>" ;
var sta_db_role = "<?php echo $standby_db[0]['database_role'] ?>" ;
var mrp_status = "<?php echo $standby_db[0]['s_mrp_status'] ?>" ;

var mylay = null;
var oTimer = null; 
var last_time = null;
var current_time = null;

var last_switchover = null;
var warningDiv = document.getElementById("mrp_warning");
var query_url="<?php echo site_url('wl_oracle/dg_progress') ?>";
var on_process="<?php echo $dg_group[0]['on_process'] ?>" ;
var on_switchover="<?php echo $dg_group[0]['on_switchover'] ?>" ;
var on_failover="<?php echo $dg_group[0]['on_failover'] ?>" ;
var on_startmrp="<?php echo $dg_group[0]['on_startmrp'] ?>" ;
var on_stopmrp="<?php echo $dg_group[0]['on_stopmrp'] ?>" ;
    
function checkUser(e){

		if(e.value == "MRPStart"){
			_message = "确认要开启MRP进程吗？";
		}
		else if(e.value == "MRPStop"){
			_message = "确认要停止MRP进程吗？";
		}
		else if(e.value == "Switchover"){
			_message = "确认要开始主备切换吗？";
		}
		else if(e.value == "Failover"){
			_message = "确认要开始灾难切换吗？";
		}
		else if(e.value == "SnapshotStart"){
			_message = "确认要进入快照状态吗？";
		}
		else if(e.value == "SnapshotStop"){
			_message = "确认要退出快照状态吗？";
		}
		else{
			_message = "";
		}



		

		
		
		if((e.value == "SnapshotStart" || e.value == "SnapshotStop")){
				var version = sta_version.substring(0, sta_version.indexOf('.'));
				if(version <=10){
						bootbox.alert({
				        		message: "数据库版本必须是11g以上才能支持此项功能!",
				        		buttons: {
									        ok: {
									            label: '确定',
									            className: 'btn-success'
									        }
									    }
				        	});
				        	
				    return false;
		  }
		  
		  if(sta_db_role=="SNAPSHOT STANDBY" && e.value == "SnapshotStart"){
					bootbox.alert({
			        		message: "数据库已经处于快照模式！",
			        		buttons: {
								        ok: {
								            label: '确定',
								            className: 'btn-success'
								        }
								    }
			        	});
			        	
			    return false;
		  }
		  
		  if(sta_db_role=="PHYSICAL STANDBY" && e.value == "SnapshotStop"){
					bootbox.alert({
			        		message: "数据库不在快照模式中，无法退出！",
			        		buttons: {
								        ok: {
								            label: '确定',
								            className: 'btn-success'
								        }
								    }
			        	});
			        	
			    return false;
		  }
		}
		else{
			if(sta_db_role=="SNAPSHOT STANDBY"){
					bootbox.alert({
			        		message: "数据库已经处于快照模式，无法进行主备切换及MRP起停等操作！",
			        		buttons: {
								        ok: {
								            label: '确定',
								            className: 'btn-success'
								        }
								    }
			        	});
			        	
			    return false;
		  }
		}
		
		//
		if((e.value == "MRPStart" || e.value == "MRPStop")){
		  if(mrp_status=="1" && e.value == "MRPStart"){
					bootbox.alert({
			        		message: "MRP进程已经是开启状态！",
			        		buttons: {
								        ok: {
								            label: '确定',
								            className: 'btn-success'
								        }
								    }
			        	});
			        	
			    return false;
		  }
		  
		  if(mrp_status=="0" && e.value == "MRPStop"){
					bootbox.alert({
			        		message: "MRP进程已经是停止状态！",
			        		buttons: {
								        ok: {
								            label: '确定',
								            className: 'btn-success'
								        }
								    }
			        	});
			        	
			    return false;
		  }
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
											                    data: "dg_action=" + e.value,
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
																	
																	oTimer = setInterval("queryHandle(query_url)",2000);
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
		if(sta_db_role=="SNAPSHOT STANDBY"){
			$("#lb_warning").html("The standby database is in Snapshot status.");
			warningDiv.style.display="block";
		}
		else if(mrp_status=="0"){
			$("#lb_warning").html("Warning: The MRP process is not running!!!");
			warningDiv.style.display="block";
		}
		
		
});  
  
function queryHandle(url){
    $.post(url, {group_id:group_id}, function(json){
    		sta_db_role = json.sta_role; 					//update value for sta_db_role
    		mrp_status = json.mrp_status; 			  //update value for mrp_status

        //var status = 1;
        last_switchover = json.on_switchover;
        
        if(json.mrp_status!='1' || json.sta_role=='SNAPSHOT STANDBY'){
						if(json.sta_role=='SNAPSHOT STANDBY'){
							$("#lb_warning").html("The standby database is in Snapshot status.");
						}
						else{
							$("#lb_warning").html("Warning: The MRP process is not running!!!");
						}
						warningDiv.style.display="block";
        }
        else{
						warningDiv.style.display="none";
        }
        
        if(json.on_process==='0'){ 
        		if(mylay!=null){
        			layer.close(mylay);
        			//setTimeout(function(){layer.close(mylay);}, 3000);
        			//window.clearInterval(oTimer);
        		}
            
        }else{

        	current_time = json.process_time;
        	if(current_time != last_time){
        			$("#div_layer").append("<p>" + json.process_time + ": " + json.process_desc + "</p>");
        	}
        	last_time = current_time;
        }  
    },'json');  
}  

</script>


