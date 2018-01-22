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
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>




<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 


<div>
<form id="form_switch" name="form" class="form-inline" method="post" action="<?php echo site_url('lp_oracle/dg_switch') ?>" >
    <a class="btn btn " href="<?php echo site_url('lp_oracle/dataguard') ?>"><i class="icon-return"></i> <?php echo $this->lang->line('return'); ?></a>

    
    <input name="trans_type" type="submit" value="Failover" onclick="return checkUser()" class="btn btn-success" style="width:100px; float:right; margin-right:5px;"></button>
    <input name="trans_type" type="submit" value="Switchover"  class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"></button>

		<input name="mrp_action" type="submit" value="MRPStop"  class="btn btn-success" style="width:100px; float:right; margin-right: 5px;"></button>
		<input name="mrp_action" type="submit" value="MRPStart"  class="btn btn-success" style="width:100px; float:right; margin-right:5px;"></button>
    

</form>
</div>
</div>






<div style="padding: 19px;" >
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
        <img src="./images/left_arrow.png"/>
        <img src="
        <?php
        $second_dif=floor((strtotime($primary_db[0]['p_db_time'])-strtotime($standby_db[0]['s_db_time']))%86400%60);
        if($second_dif > 3600 ){echo "./images/trans_alarm.png";}   #时间差超过1小时，显示trans_error图片
        elseif($primary_db[0]['open_mode']==-1 or $standby_db[0]['open_mode']==-1){echo "./images/trans_error.png";}
        else{echo "./images/health_status.png";}  ?> 
        "/>
        <img src="./images/right_arrow.png"/>
        </div> 
        
        <!-- <div style="float:left;"><img src="./images/standby_db.png"/></div>  -->
        
        <div style="float:left;"><img src="<?php if($standby_db[0]['open_mode']==-1){echo "./images/connect_error.png";} else{echo "./images/standby_db.png";}  ?> "/></div> 
    </div>


		<div style="float:left; width:265px; height:30px; border:0px solid red;">
		</div>
		<div style="float:left; width:400px; height:30px; border:1px solid red; color:red; <?php if($standby_db[0]['s_mrp_status']==1){echo "display: none;";} ?>">
			<label name="sta_mrp" class="control-label" style="font-size:18px;color:red; padding: 5px 0px 0px 20px;"> Warning: The MRP process is not running!!!</label>
		</div>
		
</div>  

<label name="test1" class="control-label" >调试信息1：<?php echo $setval['python'] ?></label>
<label name="test2" class="control-label" style="display:none;">调试信息2：<?php echo $setval['test'] ?></label>


<script type="text/javascript">
function checkUser(){
var base_url = "<?php echo site_url('lp_oracle/dg_switch?dg_group_id=') ?>";
var group_id = "<?php echo $setval['id'] ?>";
var target_url = base_url.toString() + group_id.toString();

var user_pwd = "<?php echo $userdata['password'] ?>" ;


bootbox.prompt({
    title: "请输入管理员密码!",
    inputType: 'password',
    callback: function (result) {
    	alert(result.length);
    	if (result == ''){
    			bootbox.alert({
        		message: "您输入的密码为空!",
        		buttons: {
					        ok: {
					            label: '确定',
					            className: 'btn-success'
					        }
					    }
        	});
    		}
    	else if(result)
    	{ alert('111');
    		
        if (md5(result) == user_pwd)
        {
					bootbox.confirm({
					    message: "确认要开始切换吗？",
					    buttons: {
					        confirm: {
					            label: '是',
					            className: 'btn-success'
					        },
					        cancel: {
					            label: '否',
					            className: 'btn-danger'
					        }
					    },
					    callback: function (result) {
					        if(result)
					        { 
					        	//window.location.href=target_url;
					        	//document.getElementById("form_switch").submit();
					        	//alert(result);
					        	return true;
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
alert("xxxxx");
return false;
}
</script>