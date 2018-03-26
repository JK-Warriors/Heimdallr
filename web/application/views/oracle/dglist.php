<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_oracle/index'); ?>"><?php echo $this->lang->line('_Oracle Monitor'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DataGuard List'); ?></li><span class="divider"></span></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>: <?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form" class="form-inline" method="get" action="<?php echo site_url('wl_oracle/dglist') ?>" >
 
  <input type="text" id="host"  name="host" value="" placeholder="<?php echo $this->lang->line('please_input_ip'); ?>" class="input-medium" >
  <input type="text" id="dsn"  name="dsn" value="" placeholder="<?php echo $this->lang->line('please_input_inst_name'); ?>" class="input-medium" >
  

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
        <th colspan="4"><center><?php echo $this->lang->line('primary_db'); ?></center></th>
        <th colspan="4"><center><?php echo $this->lang->line('standby_db'); ?></center></th>
				<th colspan="3"><center><?php echo $this->lang->line('status'); ?></center></th>
        
        <th ></th>
	   		</tr>
        <tr style="font-size: 12px;">
        <th><?php echo $this->lang->line('group_name'); ?></th> 
        <th><?php echo $this->lang->line('primary_db'); echo $this->lang->line('ip'); ?></th> 
        <th><?php echo $this->lang->line('primary_db'); echo $this->lang->line('port'); ?></th> 
				<th><?php echo $this->lang->line('primary_db'); echo $this->lang->line('instance_name'); ?></th>
        <th><?php echo $this->lang->line('standby_db'); echo $this->lang->line('ip'); ?></th> 
        <th><?php echo $this->lang->line('standby_db'); echo $this->lang->line('port'); ?></th> 
				<th><?php echo $this->lang->line('standby_db'); echo $this->lang->line('instance_name'); ?></th>
				<th><?php echo $this->lang->line('status'); ?></th>
				<th><?php echo $this->lang->line('dg_delay'); ?></th>
				<th></th>
	    </tr>
      </thead>
      <tbody>
 <?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
    <tr style="font-size: 12px;">
        <td><?php echo $item['group_name'] ?></td>
        <td><?php echo $item['p_host'] ?></td>
        <td><?php echo $item['p_port'] ?></td>
        <td><?php echo $item['p_dsn'] ?></td>
        <td><?php echo $item['s_host'] ?></td>
        <td><?php echo $item['s_port'] ?></td>
        <td><?php echo $item['s_dsn'] ?></td>
        <td><?php echo $item['mrp_status'] ?></td>
        <td><?php echo $item['delay_mins'] ?></td>
				<td><a href="<?php echo site_url('wl_oracle/dataguard?dg_group_id='.$item['group_id']) ?>"><?php echo $this->lang->line('detail'); ?></a></td>

   
        <td></td>
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

