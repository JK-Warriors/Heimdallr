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
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/bootstrap/js/md5.js"></script>
<script src="lib/bootstrap/js/yprogressbar.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
<link href="lib/bootstrap/css/yprogressbar.css" rel="stylesheet"/>




<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 


<div>
<form id="form_switch" class="form-inline" method="post" action="" >
    <a class="btn btn " href="<?php echo site_url('lp_oracle/dataguard') ?>"><i class="icon-return"></i> <?php echo $this->lang->line('return'); ?></a>

    
    <button name="trans_type" type="button" value="Failover" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right:5px;"><?php echo $this->lang->line('failover'); ?></button>
    <button name="trans_type" type="button" value="Switchover" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"><?php echo $this->lang->line('switchover'); ?></button>

		<button name="mrp_action" type="button" value="MRPStop" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"><?php echo $this->lang->line('stop_mrp'); ?></button>
		<button name="mrp_action" type="button" value="MRPStart" onclick="checkUser(this)" <?php if($setval['id']==""){echo 'disabled="disabled"';} ?> class="btn btn-success" style="width:100px; float:right; margin-right:5px;"><?php echo $this->lang->line('start_mrp'); ?></button>
    

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
		<div id="mrp_warning" style="float:left; width:400px; height:30px; border:1px solid red; color:red; <?php if($standby_db[0]['s_mrp_status']==1){echo "display: none;";} ?>">
			<label name="sta_mrp" class="control-label" style="font-size:18px;color:red; padding: 5px 0px 0px 20px;"> Warning: The MRP process is not running!!!</label>
		</div>
		
</div>  

<label name="test1" class="control-label" style="display:none;">调试信息1：<?php echo $setval['python'] ?></label>
<label name="test2" class="control-label" style="display:none;">调试信息2：<?php echo $setval['test'] ?></label>


<script type="text/javascript">
var base_url = "<?php echo site_url('lp_oracle/dg_switch?dg_group_id=') ?>";
var group_id = "<?php echo $setval['id'] ?>";
var target_url = base_url.toString() + group_id.toString();
var dg_url = "<?php echo site_url('lp_oracle/dataguard?dg_group_id=') ?>" + group_id.toString();
var user_pwd = "<?php echo $userdata['password'] ?>" ;

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
		                							myBar = new YprogressBar({
																											    title: "后台正在处理中...",
																											    des: "{{y:progress}}",
																											    closeable: false,
																											    cancelCallback: function(rate, vars){
																											  	console.log(rate);
																											  	console.log(vars);
																											    }
																											  }); 
																	myBar.update(0,{progress: ""});  
																	myBar.show();
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


var oTimer = null; 
var myBar = null;
var last_switchover = null;
var warningDiv = document.getElementById("mrp_warning");
var query_url="<?php echo site_url('lp_oracle/dg_progress') ?>";
var on_process="<?php echo $dg_group[0]['on_process'] ?>" ;
var on_switchover="<?php echo $dg_group[0]['on_switchover'] ?>" ;
var on_failover="<?php echo $dg_group[0]['on_failover'] ?>" ;
var on_startmrp="<?php echo $dg_group[0]['on_startmrp'] ?>" ;
var on_stopmrp="<?php echo $dg_group[0]['on_stopmrp'] ?>" ;
  
jQuery(document).ready(function(){
		if(group_id != ""){
			oTimer = setInterval("queryHandle(query_url)",2000);
		} 
		
		if(on_process=='1'){
			myBar = new YprogressBar({
														    title: "后台正在处理中...",
														    des: "{{y:progress}}",
														    closeable: false,
														    cancelCallback: function(rate, vars){
														  	console.log(rate);
														  	console.log(vars);
														    }
														  }); 
			myBar.update(0,{progress: ""});  
			myBar.show();
			
		}
		    
});  
  
function queryHandle(url){
    $.post(url, {group_id:group_id}, function(json){ 
        //var status = 1;
        last_switchover = json.on_switchover;
        
        if(json.mrp_status=='1'){
						warningDiv.style.display="none";
        }
        else{
						warningDiv.style.display="block";
        }
        
        
        if(json.on_process==='0'){ 
        		if(myBar!=null){
        				myBar.destroy(); 
        				myBar=null;
        				//window.location.reload();
        				
        						alert(last_switchover);
        				if(last_switchover == 1){
        						window.location.href=dg_url;
        				}
        				else{
        						window.location.reload();
        				} 

        		}
            
        }else{ 
        		if(myBar==null){
  							myBar = new YprogressBar({
											    title: "后台正在处理中...",
											    des: "{{y:progress}}",
											    closeable: false,
											    cancelCallback: function(rate, vars){
											  	console.log(rate);
											  	console.log(vars);
											    }
											  }); 
								myBar.update(0,{progress: ""});  
								myBar.show();
        		}
        		else{
					if(json.process_desc==null){
						show_str="";
					}else{
						show_str=json.process_desc;
					}
					myBar.update(json.rate,{progress: show_str}); 
        				
        		}
            
        }  
    },'json');  
}  

</script>


