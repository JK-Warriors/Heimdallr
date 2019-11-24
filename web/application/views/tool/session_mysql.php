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
        <th><?php echo $this->lang->line('username'); ?></th>
        <th><?php echo $this->lang->line('client_info'); ?></th>
        <th><?php echo $this->lang->line('db_name'); ?></th>
        <th><?php echo $this->lang->line('command'); ?></th>
				<th style="width:200px"><?php echo $this->lang->line('status'); ?></th>
				<th style="width:280px"><?php echo $this->lang->line('sql_text'); ?></th>
				<th><?php echo $this->lang->line('exec_time'); ?></th>
	    </tr>
      </thead>
      <tbody>
 <?php if(!empty($session_data)) {?>
 <?php foreach ($session_data  as $item):?>
    <tr style="font-size: 12px;">
        <td><?php echo $item['sid'] ?></td>
        <td><?php echo $item['user'] ?></td>
        <td><?php echo $item['host'] ?></td>
        <td><?php echo $item['db'] ?></td>
        <td><?php echo $item['command'] ?></td>
        <td><?php echo $item['state'] ?></td>
        <td><?php echo $item['info'] ?></td>
        <td><?php echo $item['time'] ?></td>
				<td></td>
	</tr>
 <?php endforeach;?>
 <?php }else{  ?>
<tr>
<td colspan="5">
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
		var client_ip = $('#client_ip').val();
		var target_url = "<?php echo site_url('wl_tool/session_trace') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString() + "&username=" + username.toString() + "&client_ip=" + client_ip.toString();
		
		window.location.href=target_url;				
})

$('#reset').click(function(){
		var target_url = "<?php echo site_url('wl_tool/session_trace') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString();
		window.location.href=target_url;				
})

$('#refresh').click(function(){
		var username = $('#username').val();
		var client_ip = $('#client_ip').val();

		var target_url = "<?php echo site_url('wl_tool/session_trace') ?>" + "?server_id=" + server_id.toString() + "&db_type=" + db_type.toString() + "&username=" + username.toString() + "&client_ip=" + client_ip.toString();

		window.location.href=target_url;				
})
    


 </script>
 

