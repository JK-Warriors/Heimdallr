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

<div class="ui-state-default ui-corner-all" style="height: 220px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form_license" class="form-inline" method="get" action="" >

<div class="form-group">
		<label class="col-lg-2 control-label"><?php echo $this->lang->line('license_code'); ?>：</label>
		<div class="col-lg-4" style="float:left;" >
         <textarea class="form-control" id="license_code" style="width: 100%;height: 150px"></textarea>
    </div>
</div>         
<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
				<button id="license_active" onclick="licenseActive(this)" type="button" class="btn btn-primary"><?php echo $this->lang->line('license_active'); ?></button>
		</div>
</div>

</form>                   
</div>

<div class="ui-state-default ui-corner-all" style="height: 140px;" >
<p><span style="margin-right: .3em;" class="ui-icon ui-icon-search"></span> 
	
	<div style="width: 100%;">
		<label class="col-lg-2 control-label">授权信息：</label>
	</div>
	
	<div style="width: 100%;">
		<label class="col-lg-2 control-label">类型：<?php if($license_data['type']==1){echo "测试版";} ?></label>
		<label class="col-lg-2 control-label">过期时间：<?php echo date("Y/m/d H:i:s",$license_data['expiration_time']); ?></label>
		<label class="col-lg-2 control-label">状态：<?php if($license_data['status']==1){echo "启用";} ?></label>
	</div>
	
	<div style="width: 100%;">	
		<label class="col-lg-2 control-label">Oracle容灾：<?php echo $license_data['config_info']['ora_recover']; ?> 个授权点</label>
		<label class="col-lg-2 control-label">mysql容灾：<?php echo $license_data['config_info']['mysql_recover']; ?> 个授权点</label>
		<label class="col-lg-2 control-label">SQLServer容灾：<?php echo $license_data['config_info']['mssql_recover']; ?> 个授权点</label>
	</div>
	
	<div style="width: 100%;">	
		<label class="col-lg-2 control-label">Oracle监控：<?php echo $license_data['config_info']['ora_watch']; ?> 个授权点</label>
		<label class="col-lg-2 control-label">mysql监控：<?php echo $license_data['config_info']['mysql_watch']; ?> 个授权点</label>
		<label class="col-lg-2 control-label">SQLServer监控：<?php echo $license_data['config_info']['mssql_watch']; ?> 个授权点</label>
	</div>
	
	

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
