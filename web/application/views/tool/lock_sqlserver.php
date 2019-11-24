<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_tool/lock'); ?>"><?php echo $this->lang->line('_Tool Box'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><a href="<?php echo site_url('wl_tool/lock'); ?>"><?php echo $this->lang->line('_DB List'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('lock_list'); ?></li><span class="divider"></span></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>: <?php if(!empty($datalist)){ echo $datalist[0]['uptime_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/layer3/layer.js"></script>
<script src="lib/bootstrap/js/md5.js"></script>
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form" class="form-inline">	
	<input type="text" id="username"  name="username" value="<?php echo $username; ?>" placeholder="<?php echo $this->lang->line('please_input_username'); ?>" class="input-medium" >
  <input type="text" id="machine"  name="machine" value="<?php echo $machine; ?>" placeholder="<?php echo $this->lang->line('please_input_machine'); ?>" class="input-medium" >
  <input type="text" id="program"  name="program" value="<?php echo $program; ?>" placeholder="<?php echo $this->lang->line('please_input_program'); ?>" class="input-medium" >
  <input type="text" id="client_ip"  name="client_ip" value="<?php echo $client_ip; ?>" placeholder="<?php echo $this->lang->line('please_input_client_ip'); ?>" class="input-medium" >
  
  <button type="button" id="search" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
  <button type="button" id="reset" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></button>
  <button type="button" id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>

</form>                
</div>


<div class="well">
    <table class="table table-hover table-condensed ">
      <thead>
        
        <tr style="font-size: 12px;">
        <th><?php echo $this->lang->line('sid'); ?></th> 
				<th><?php echo $this->lang->line('status'); ?></th>
        <th><?php echo $this->lang->line('username'); ?></th>
        <th><?php echo $this->lang->line('machine'); ?></th>
        <th><?php echo $this->lang->line('program'); ?></th>
        <th><?php echo $this->lang->line('client_info'); ?></th>
        <th><?php echo $this->lang->line('block_sid'); ?></th>
        <th><?php echo $this->lang->line('hold_time'); ?></th>
				<th><?php echo $this->lang->line('opration'); ?></th>
	    </tr>
      </thead>
      <tbody>
 <?php if(!empty($lock_list)) {?>
 <?php foreach ($lock_list  as $item):?>
    <tr style="font-size: 12px;">
        <td><?php echo $item['sid'] ?></td>
        <td><?php echo $item['status'] ?></td>
        <td><?php echo $item['username'] ?></td>
        <td><?php echo $item['hostname'] ?></td>
        <td><?php echo $item['program'] ?></td>
        <td><?php echo $item['client_ip'] ?></td>
        <td><?php echo $item['blocked'] ?></td>
        <td><?php echo $item['waittime'] ?></td>
        <td><a href="javascript:void(0);" sid="<?php echo $item['blocked']?>" onclick="kill_session(this)"><?php echo $this->lang->line('kill_session'); ?></a></td>
				<td></td>
	</tr>
 <?php endforeach;?>
 <?php }else{  ?>
<tr>
<td colspan="15">
<font color="red"><?php echo $this->lang->line('no_record'); ?></font>
</td>
</tr>
<?php } ?>      
      </tbody>
    </table>
</div>

<script type="text/javascript">
var server_id = "<?php echo $server_id ?>";
var db_type = "<?php echo $db_type ?>";
var base_url="<?php echo site_url('wl_tool/lock_view') ?>";

$('#search').click(function(){
		var username = $('#username').val();
		var machine = $('#machine').val();
		var program = $('#program').val();
		var client_ip = $('#client_ip').val();
		var target_url = "<?php echo site_url('wl_tool/lock_view') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString() + "&username=" + username.toString() + "&machine=" + machine.toString() + "&program=" + program.toString() + "&client_ip=" + client_ip.toString();

		window.location.href=target_url;				
})

$('#reset').click(function(){
		var target_url = "<?php echo site_url('wl_tool/lock_view') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString();
		window.location.href=target_url;				
})

$('#refresh').click(function(){
		var username = $('#username').val();
		var machine = $('#machine').val();
		var program = $('#program').val();
		var client_ip = $('#client_ip').val();

		var target_url = "<?php echo site_url('wl_tool/lock_view') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString() + "&username=" + username.toString() + "&machine=" + machine.toString() + "&program=" + program.toString() + "&client_ip=" + client_ip.toString();

		window.location.href=target_url;				
})
    
    

function kill_session(e){
		var target_url = "<?php echo site_url('wl_tool/kill_session') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString();
		var sid = $(e).attr("sid");
		
		if(sid < 51){
			bootbox.alert({
	        		title: "",
	        		message: "阻塞会话为系统会话，无法清除！",
	        		buttons: {
						        ok: {
						            label: '确定',
						            className: 'btn-success'
						        }
						    }
	        	});
			
		}else{
		bootbox.dialog({
							    message: "确定要杀掉这个会话吗？",
							    buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-danger',
													callback: function(){
		                            $.ajax({
											                    url: target_url,
											                    data: $("#form").serializeArray(),
											                    data: {"sid":sid},
											                    type: "POST",
											                    success: function (data) {
											              			//回调函数，判断提交返回的数据执行相应逻辑
											                        if (data.result == 1) {
																		        		bootbox.alert({
																				        		message: "会话已成功被清除.",
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
											                        else {
																		        		bootbox.alert({
																				        		message: "会话清除失败，请查看相关日志排查原因.",
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
											                    }
		                										});
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
}
	
	
	
 </script>

