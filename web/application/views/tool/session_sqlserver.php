<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_tool/lock'); ?>"><?php echo $this->lang->line('_Tool Box'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><a href="<?php echo site_url('wl_tool/session'); ?>"><?php echo $this->lang->line('_DB List'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('session_list'); ?></li><span class="divider"></span></li>
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
<form name="form" class="form-inline" >
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
        <th><?php echo $this->lang->line('db_name');?></th> 
				<th><?php echo $this->lang->line('status'); ?></th>
        <th><?php echo $this->lang->line('username'); ?></th>
        <th><?php echo $this->lang->line('machine'); ?></th>
        <th><?php echo $this->lang->line('program'); ?></th>
        <th><?php echo $this->lang->line('client_info'); ?></th>
        <th><?php echo $this->lang->line('event'); ?></th>
				<th><?php echo $this->lang->line(''); ?></th>
	    </tr>
      </thead>
      <tbody>
 <?php if(!empty($session_data)) {?>
 <?php foreach ($session_data  as $item):?>
    <tr style="font-size: 12px;">
        <td><?php echo $item['sid'] ?></td>
        <td><?php echo $item['dbname'] ?></td>
        <td><?php echo $item['status'] ?></td>
        <td><?php echo $item['username'] ?></td>
        <td><?php echo $item['hostname'] ?></td>
        <td><?php echo $item['program'] ?></td>
        <td><?php echo $item['client_ip'] ?></td>
        <td><?php echo $item['wait'] ?></td>
        <td><a href="javascript:void(0);" sql_text="<?php echo $item['parent_sql_text'] ?>" onclick="show_sql_detail(this)">SQL文本</a></td>
				<td></td>
	</tr>
 <?php endforeach;?>
 <?php }else{  ?>
<tr>
<td colspan="9">
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
var base_url="<?php echo site_url('wl_tool/session_trace') ?>";

$('#search').click(function(){
		var username = $('#username').val();
		var machine = $('#machine').val();
		var program = $('#program').val();
		var client_ip = $('#client_ip').val();
		var target_url = "<?php echo site_url('wl_tool/session_trace') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString() + "&username=" + username.toString() + "&machine=" + machine.toString() + "&program=" + program.toString() + "&client_ip=" + client_ip.toString();
		
		window.location.href=target_url;				
})

$('#reset').click(function(){
		var target_url = "<?php echo site_url('wl_tool/session_trace') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString();
		window.location.href=target_url;				
})

$('#refresh').click(function(){
		var username = $('#username').val();
		var machine = $('#machine').val();
		var program = $('#program').val();
		var client_ip = $('#client_ip').val();

		var target_url = "<?php echo site_url('wl_tool/session_trace') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString() + "&username=" + username.toString() + "&machine=" + machine.toString() + "&program=" + program.toString() + "&client_ip=" + client_ip.toString();

		window.location.href=target_url;				
})
    
	function show_sql_detail(e){
		bootbox.alert({
        		title: "SQL文本",
        		message: $(e).attr("sql_text"),
        		buttons: {
					        ok: {
					            label: '确定',
					            className: 'btn-success'
					        }
					    }
        	});
	}

 </script>
 

