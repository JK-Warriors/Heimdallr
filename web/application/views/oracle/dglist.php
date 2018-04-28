<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_oracle/index'); ?>"><?php echo $this->lang->line('_Oracle Monitor'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DataGuard List'); ?></li><span class="divider"></span></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>: <?php if(!empty($sta_list)){ echo $sta_list[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form" class="form-inline" method="get" action="<?php echo site_url('wl_oracle/dglist') ?>" >
 
  <input type="text" id="host"  name="host" value="<?php echo $setval['host']; ?>" placeholder="<?php echo $this->lang->line('please_input_ip'); ?>" class="input-medium" >
  <input type="text" id="dsn"  name="dsn" value="<?php echo $setval['dsn']; ?>" placeholder="<?php echo $this->lang->line('please_input_inst_name'); ?>" class="input-medium" >
  

  <button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
  <a href="<?php echo site_url('wl_oracle/dglist') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
  <button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>
</form>                
</div>


<div class="well">
    <table class="table table-hover table-condensed ">
      <thead>
        <tr style="font-size: 12px;">
				<th colspan="1"><center></center></th>
        <th colspan="3"><center><?php echo $this->lang->line('primary_db'); ?></center></th>
        <th colspan="3"><center><?php echo $this->lang->line('standby_db'); ?></center></th>
				<th colspan="2"><center><?php echo $this->lang->line('status'); ?></center></th>
				<th colspan="1"></th>
	   		</tr>
        <tr style="font-size: 12px;">
        <th><center><?php echo $this->lang->line('group_name'); ?></th> 
        <th><center><?php echo $this->lang->line('primary_db'); echo $this->lang->line('ip'); ?></th> 
        <th><center><?php echo $this->lang->line('primary_db'); echo $this->lang->line('port'); ?></th> 
				<th><center><?php echo $this->lang->line('primary_db'); echo $this->lang->line('instance_name'); ?></th>
        <th><center><?php echo $this->lang->line('standby_db'); echo $this->lang->line('ip'); ?></th> 
        <th><center><?php echo $this->lang->line('standby_db'); echo $this->lang->line('port'); ?></th> 
				<th><center><?php echo $this->lang->line('standby_db'); echo $this->lang->line('instance_name'); ?></th>
				<th><center><?php echo $this->lang->line('mrp_status'); ?></th>
				<th><center><?php echo $this->lang->line('dg_delay'); ?></th>
				<th></th>
	    </tr>
      </thead>
      <tbody>
 <?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
    <tr style="font-size: 12px;">
        <td><center><?php echo $item['group_name'] ?></td>
        <td><center><?php echo $item['p_host'] ?></td>
        <td><center><?php echo $item['p_port'] ?></td>
        <td><center><?php echo $item['p_dsn'] ?></td>
        <td><center><?php echo $item['s_host'] ?></td>
        <td><center><?php echo $item['s_port'] ?></td>
        <td><center><?php echo $item['s_dsn'] ?></td>
        <td><center><?php if(!empty($sta_list)) { ?>
 										<?php foreach ($sta_list as $s_item):?>
 										<?php    if($s_item['server_id'] == $item['s_id']){  ?>
 									  <?php    		if($s_item['mrp_status'] == '1'){echo "<img src='images/ok.png' />"; echo $this->lang->line('mrp_on');   ?>
 									  <?php    			}else{ ?>
 									  <?php        	echo "<img src='images/critical.png' />"; echo $this->lang->line('mrp_stop'); ?>
 									  <?php         } ?>
 									  <?php    	} ?>
 										<?php endforeach; ?>
 										<?php } ?></td>
 										
        <td><center><?php if(!empty($sta_list)) { ?>
 										<?php foreach ($sta_list as $s_item):?>
 										<?php    if($s_item['server_id'] == $item['s_id']){  ?>
 									  <?php    		echo $s_item['delay_mins'];   ?>
 									  <?php    	} ?>
 										<?php endforeach; ?>
 										<?php } ?></td>
				<td><center><a href="<?php echo site_url('wl_oracle/dataguard?dg_group_id='.$item['group_id']) ?>"><?php echo $this->lang->line('detail'); ?></a></td>
	</tr>
 <?php endforeach;?>
 <?php }else{  ?>
<tr>
<td colspan="12">
<font color="red"><?php echo $this->lang->line('no_record'); ?></font>
</td>
</tr>
<?php } ?>      
      </tbody>
    </table>
</div>

 <script type="text/javascript">
    $('#refresh').click(function(){
        document.location.reload(); 
    })
 </script>

