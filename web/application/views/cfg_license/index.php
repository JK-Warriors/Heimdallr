<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
 
<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_License'); ?></li>
</ul>

 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/layer3/layer.js"></script>

<div class="container-fluid">
<div class="row-fluid">
 
<div class="btn-toolbar">
    <a id="m_code" onclick="get_m_code(this)" class="btn btn-primary "><?php echo $this->lang->line('get_m_code'); ?></a>

  <div class="btn-group"></div>
</div>

<div class="well">

<div class="ui-state-default ui-corner-all" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form_license" class="form-inline" method="get" action="" >
<table style="width:100%;border:0px;">
	<tr>
		<td style="width:150px;text-align:right;vertical-align: top;padding-right:30px;"><b><?php echo $this->lang->line('license_code'); ?>：</b></td>
		<td><textarea class="form-control" id="license_code" style="width: 400px;height: 150px"></textarea></td>
		</tr>
	<tr>
		<td></td>
		<td style="padding-top:15px;"><button id="license_active" onclick="licenseActive(this)" type="button" class="btn btn-primary"><?php echo $this->lang->line('license_active'); ?></button></td>
		</tr>
</table>
</form>                   
</div>
<style>
	.tempstyle01{padding-bottom:15px;}
	.tempstyle01 label,.tempstyle01 div{
		box-sizing:border-box;}
	</style>
<div class="ui-state-default ui-corner-all tempstyle01">
	<h3 style="padding: 0 15px;">授权信息</h3>
	
	<div style="width: 100%;">
		<label class="col-lg-4 control-label"><b>类型：</b><?php if($license_data['type']==1){echo "测试版";} ?></label>
		<label class="col-lg-4 control-label"><b>过期时间：</b><?php echo date("Y-m-d H:i:s",$license_data['expiration_time']); ?></label>
		<label class="col-lg-4 control-label"><b>状态：</b><?php if($license_data['status']==1){echo "启用";} ?></label>
	</div>
	
	<div style="width: 100%;">	
		<label class="col-lg-4 control-label"><b>Oracle容灾：</b><?php echo $license_data['config_info']['ora_recover']; ?> 个授权点</label>
		<label class="col-lg-4 control-label"><b>mysql容灾：</b><?php echo $license_data['config_info']['mysql_recover']; ?> 个授权点</label>
		<label class="col-lg-4 control-label"><b>SQLServer容灾：</b><?php echo $license_data['config_info']['mssql_recover']; ?> 个授权点</label>
	</div>
	
	<div style="width: 100%;">	
		<label class="col-lg-4 control-label"><b>Oracle监控：</b><?php echo $license_data['config_info']['ora_watch']; ?> 个授权点</label>
		<label class="col-lg-4 control-label"><b>mysql监控：</b><?php echo $license_data['config_info']['mysql_watch']; ?> 个授权点</label>
		<label class="col-lg-4 control-label"><b>SQLServer监控：</b><?php echo $license_data['config_info']['mssql_watch']; ?> 个授权点</label>
	</div>
	<div style="clear:both;"></div>
	

</div>     

</div>


<script type="text/javascript">
var m_code_url="<?php echo site_url('cfg_license/get_m_code') ?>";
var license_url="<?php echo site_url('cfg_license/license_active') ?>";
	
function get_m_code(e){
    $.ajax({
		        url: m_code_url,
		        data: $("#form_license").serializeArray(),
		        data: {},
		        type: "POST",
		        success: function (data) {
		  			//回调函数，判断提交返回的数据执行相应逻辑
		            if (data.Success) {
		            }
		            else {
		            }
		            
		            if (data.machine_code != ""){
		               bootbox.alert({
					        		message: "机器码：" + data.machine_code,
					        		buttons: {
										        ok: {
										            label: '确定',
										            className: 'btn-success'
										        }
										    },
									    callback: function () {
									        //window.location.reload();
									    }
					        	});
		            }
		            else{
		            }
		        }
					});


}

function licenseActive(e){
		var license_code = $("#license_code").val();
		
		if(license_code==""){
       bootbox.alert({
      		message: "您输入的License为空，请输入合法的License。",
      		buttons: {
				        ok: {
				            label: '确定',
				            className: 'btn-success'
				        }
				    },
			    callback: function () {
			    }
      	});
      	
       exit;
		}

    $.ajax({
            url: license_url,
            data: $("#form_license").serializeArray(),
            data: {"license_code": license_code},
            type: "POST",
            success: function (data) {
      			//回调函数，判断提交返回的数据执行相应逻辑
                if (data.Success) {
                }
                else {
                }
                
                if (data.active_result == "1"){
                   bootbox.alert({
					        		message: data.active_message,
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
                }
                else{
                   bootbox.alert({
					        		message: data.active_message,
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
                }
            }
					});


}
</script>
