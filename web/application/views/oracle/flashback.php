<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!-- <div class="header">
            
            <h1 class="page-title"><?php echo $this->lang->line('_Oracle'); ?> <?php echo $this->lang->line('_DataGuard Monitor'); ?></h1>
</div> -->
        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_oracle/index'); ?>"><?php echo $this->lang->line('_Oracle Monitor'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Flashback'); ?></li><span class="divider"></span></li>

            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<!-- <div class="container-fluid">
<div class="row-fluid"> -->
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>

<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/bootstrap/js/md5.js"></script>

<script src="lib/bootstrap/js/app.min.js"></script>
<link href="lib/bootstrap/css/app.css" rel="stylesheet"/>
<link href="lib/bootstrap/css/app.min.css" rel="stylesheet"/>
<link href="lib/bootstrap/css/font-awesome.min.css" rel="stylesheet">


                    
<div  class="ui-state-default ui-corner-all" style="height: 45px;" >
<p>
<span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 

<div class="control-group" >
<form name="form_flashback" class="form-inline">
    <label class="control-label" style="display:inline-block; width:80px">*数据库标签</label>
    
    <div class="controls" style="display:inline-block;" >
      <select id="server_name" onchange="server_change(this)" class="input-large">
        <?php foreach ($datalist as $item):?>
        <option value="<?php echo $item['server_id'];?>" <?php if ($item['server_id'] == $setval['id']) { ?>selected="selected"<?php } ?>><?php echo $item['tags'];?> (<?php echo $item['host'];?>)</option>
        <?php endforeach;?>
        </select>
    </div>
    
	


</form>
</div>
</div>


<div  style="padding:19px;height:200px; <?php if($setval['id']==""){echo "display:none;";} ?>">
    <div id="div_fb" class="controls">
  		<label class="control-label" style="display:inline-block;padding-left:15px; width:80px">*快照级别: </label>
  		
  		<div class="controls" style="display:inline-block;height:50px;">
  		<label class="control-label" style="display:inline-block;padding-left:0px; width:80px">数据库快照</label>
      <select id="flashback_type" onchange="fb_type_change(this)" class="input-large" style="display:none;">
		  <option value="1"> 数据库快照</option>
		  <!-- <option value="2"> 表空间闪回</option> -->
		  <!-- <option value="3"> 表格闪回</option> -->
      </select>
    	</div>
    </div>

    <div id="div_tbs" class="controls" >
    <label class="control-label" style="display:inline-block;padding-left:15px; width:80px">*表空间名</label>
    <div class="controls" style="display:inline-block;height:50px;" >
      <select id="restore_tbs" class="input-large">
        
      </select>
    </div>
    </div>

    <div id="div_user" class="controls" >
    <label class="control-label" style="display:inline-block;padding-left:15px; width:80px">*用户名</label>
    <div class="controls" style="display:inline-block;height:50px;" >
      <select id="restore_user" onchange="user_change(this)" class="input-large">
        
      </select>
    </div>
    </div>
    
    <div id="div_table" class="controls" style="display:inline-block;">
    <label class="control-label" style="display:inline-block;padding-left:15px; width:80px">*表名</label>
    <div class="controls" style="display:inline-block;height:50px;" >
      <select id="restore_tables" class="input-large">
        
      </select>
    </div>
    </div>
    

    <div>
		    <div class="controls" style="display:inline-block;">
				    <label class="control-label" style="display:inline-block;padding-left:15px; width:80px">*快照方式</label>
				    <div class="controls" style="display:inline-block;height:50px;" >
				      <select id="fb_method" onchange="fb_method_change(this)" class="input-large">
								  <option value="1"> 按快照点</option>
								  <option value="2"> 按颗粒时间</option>
				      </select>
				    </div>
		    </div>
		    
		    <div id="div_point" class="controls">
				    <label class="control-label" style="display:inline-block;padding-left:15px; width:80px">*快照点名称</label>
				    <div  class="controls" style="display:inline-block;height:50px;" >
				      <select id="fb_point" class="input-large">
				        <?php foreach ($restore_point as $item):?>
				        <option value="<?php echo $item['name'];?>" ><?php echo $item['name'];?> </option>
				        <?php endforeach;?>
				      </select>
				    </div>
		    </div>
		    
		    
		    <div id="div_time" class="controls">
				    <label class="control-label" style="display:inline-block;padding-left:15px; width:80px">*快照时间</label>
				    <input id="fb_time" type="datetime-local" style="width:195px"/>
		    </div>
    </div>
    
        
    <p>
    <div class="controls" style="padding-left:15px;">
    <button type="submit" class="btn btn-success" onclick="checkUser(this)" > 开始恢复</button>
  	</div>
</div>











<script type="text/javascript">
var server_id = "<?php echo $setval['id'] ?>";
var base_url="<?php echo site_url('wl_oracle/flashback') ?>";

function server_change(e){
		var target_url = base_url.toString() + '?server_id=' + e.value.toString();
    $(location).attr('href', target_url);
}

function fb_type_change(e){
		if(e.value == 2){
				$("#div_tbs").show();
				$("#div_user").hide();
				$("#div_table").hide();
		}
		else if(e.value == 3){
				$("#div_tbs").hide();
				$("#div_user").show();
				$("#div_table").show();
		}
		else{
				$("#div_tbs").hide();
				$("#div_user").hide();
				$("#div_table").hide();
		}
}


function fb_method_change(e){
		if(e.value == 1){
				$("#div_point").show();
				$("#div_time").hide();
		}
		else{
				$("#div_time").show();
				$("#div_point").hide();
		}
}


function user_change(e){
	  /*
		var tab_list= null;
		var tab_array=eval(tab_list);  
		
		
		$("#restore_tables").empty();
    for(i=0;i<tab_array.length;i++){
    		if(tab_array[i]['owner'] == e.value){
						$("#restore_tables").append("<option value='"+ tab_array[i]['table_name'] +"'>"+ tab_array[i]['table_name'] +"</option>");
    		}
    }*/ 
    
    $("#restore_tables").empty();
}



var target_url = base_url.toString() + '?server_id=' + server_id.toString();
var group_id = "<?php echo $setval['group_id'] ?>";
var dg_manage_url = "<?php echo site_url('wl_oracle/dg_switch') ?>" + '?dg_group_id=' + group_id.toString();

var user_pwd = "<?php echo $userdata['password'] ?>" ;
var test_order = "<?php echo $setval['order'] ?>";
var oTimer = null; 
var process_url = base_url.toString() + '_process?server_id=' + server_id.toString();

var fb_status = "<?php echo $setval['fb_status'] ?>";

function checkUser(e){
		var fb_type = $('#flashback_type option:selected').val();
		var fb_method = $('#fb_method option:selected').val();
		var fb_point = $('#fb_point option:selected').val();
		var fb_time = $('#fb_time').val();
		var restore_tbs = $('#restore_tbs option:selected').val();
		var restore_user = $('#restore_user option:selected').val();
		var restore_table = $('#restore_tables option:selected').val();
		
		//空值检查
		if(fb_method == "1" && typeof(fb_point) == "undefined"){
				bootbox.alert({
		        		message: "闪回点名称不能为空!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
		        	
		    return false;
		}
		
		//flashback 状态检查
		if(fb_status != "YES"){
				bootbox.alert({
		        		message: "该数据库没有开启闪回!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
		        	
		    return false;
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
							    message: "确定需要闪回吗？",
							    buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-danger',
													callback: function(){
		                            $.ajax({
											                    url: target_url,
											                    data: $("#form_flashback").serializeArray(),
											                    data: {"fb_type":fb_type,"fb_method":fb_method,"fb_point":fb_point,"fb_time":fb_time,"restore_tbs":restore_tbs,"restore_table":restore_table},
											                    type: "POST",
											                    success: function (data) {
											              			//回调函数，判断提交返回的数据执行相应逻辑
											                        if (data.Success) {
											                        }
											                        else {
											                        }
											                    }
		                										});
		                						$("#inner_frame").busyLoad("show", {background: "rgba(52, 52, 52, 0.5)", text: "闪回中 ...", color: "blue", fontawesome: "fa fa-spinner fa-pulse fa-5x fa-fw " });
		                						
		                						oTimer = setInterval("get_fb_process(process_url)",2000);
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


function get_fb_process(url){
    $.post(url, {server_id:server_id}, function(json){ 
        if(json.fb_process=='0'){
        		clearInterval(oTimer); 
        		$("#inner_frame").busyLoad("hide");
        		
        		if(json.fb_result=='1'){
        				fb_message = "闪回成功。";
        		
		        		bootbox.alert({
				        		message: fb_message,
				        		buttons: {
									        ok: {
									            label: '确定',
									            className: 'btn-success'
									        }
									    }
				        	});
        		}
        		else{
        				fb_message = "闪回失败。原因是：" + json.fb_reason + "<p>MRP进程已经停止，是否跳转到管理页面重新开启MRP？";
        				
								bootbox.dialog({
								    message: fb_message,
								    buttons: {
								        ok: {
								            label: '确定',
								            className: 'btn-danger',
														callback: function(){
																	//跳转到DG管理页面
																	window.location.href = dg_manage_url;
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
            
        }else if(json.fb_blocked=='1'){ 
        		clearInterval(oTimer); 
        		$("#inner_frame").busyLoad("hide");
        		
        		fb_message = "闪回失败，另外一个闪回进程正在运行中。闪回对象是：" + json.fb_object;
        		
        		bootbox.alert({
		        		message: fb_message,
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
        }  
    },'json');  
}  


$(document).ready(function(){
  $("#div_tbs").hide();
  $("#div_user").hide();
  $("#div_table").hide();
  
	$("#div_point").show();
	$("#div_time").hide();
  
  var now_time=new Date().Format("yyyy-MM-ddTHH:mm"); 
  $("#fb_time").val(now_time);
});


Date.prototype.Format = function (fmt) {    
    var o = {    
        "M+": this.getMonth() + 1, //月份     
        "d+": this.getDate(), //日     
        "H+": this.getHours(), //小时     
        "m+": this.getMinutes(), //分     
        "s+": this.getSeconds(), //秒     
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度     
        "S": this.getMilliseconds() //毫秒     
    };    
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));    
    for (var k in o)    
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));    
    return fmt;    
}    
</script>


<script type="text/javascript">
		//$("#inner_frame").busyLoad("show", {background: "rgba(52, 52, 52, 0.5)", text: "闪回中 ...", color: "blue", fontawesome: "fa fa-spinner fa-pulse fa-5x fa-fw " });
		//$("#inner_frame").busyLoad("hide");
		//$("#inner_frame").busyLoad("show", {background: "rgba(52, 52, 52, 0.5)", text: "闪回中 ...", color: "blue", fontawesome: "fa fa-spinner fa-pulse fa-5x fa-fw " });
		
</script>



