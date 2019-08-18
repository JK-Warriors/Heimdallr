
        
        <ul class="breadcrumb">
            <li><a href=""><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_MySQL Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Replication Monitor'); ?></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">
                    
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>

<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span class="ui-icon ui-icon-search" style="float: left; margin-right: .3em;"></span>
                    
<form name="form" class="form-inline" method="get" action="<?php site_url('wl_mysql/replication') ?>" >
  <input type="hidden" name="search" value="submit" />
  
   <input type="text" id="host"  name="host" value="" placeholder="<?php echo $this->lang->line('please_input_host'); ?>" class="input-medium" >
  <input type="text" id="tags"  name="tags" value="" placeholder="<?php echo $this->lang->line('please_input_tags'); ?>" class="input-medium" >
  
  
  <button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
  <a href="<?php echo site_url('wl_mysql/replication') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
  <button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>

</form>               
</div>




<div class="well">
    <table class="table table-hover  table-condensed " style="font-size: 12px;">
		<thead>
		<tr>
		<th colspan="4"><center><?php echo $this->lang->line('servers'); ?></center></th>
		<th colspan="3"><center><?php echo $this->lang->line('thread'); ?></center></th>
		<th colspan="2"><center><?php echo $this->lang->line('binary_logs'); ?></center></th>
		<th colspan="3"><center><?php echo $this->lang->line('master_postion'); ?></center></th>
		<th colspan="1"></th>
		</tr>
		<tr>
        <th><?php echo $this->lang->line('host'); ?></th>
        <th><?php echo $this->lang->line('tags'); ?></th>
        <th><?php echo $this->lang->line('gtid_mode'); ?></th>
        <th><?php echo $this->lang->line('read_only'); ?></th>
        <th><?php echo $this->lang->line('io'); ?></th>
        <th><?php echo $this->lang->line('sql'); ?></th>
        <th><?php echo $this->lang->line('time_behind'); ?></th>
        <th><?php echo $this->lang->line('current_file'); ?></th>
        <th><?php echo $this->lang->line('postion'); ?></th>
        <th><?php echo $this->lang->line('binary_log'); ?></th>
        <th><?php echo $this->lang->line('postion'); ?></th>
        <th><?php echo $this->lang->line('space'); ?></th>
        <th><?php echo $this->lang->line('chart'); ?></th>
	   </tr>
	   </thead>
	   <tbody>
<?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
    <tr>
        <td><?php  echo $item['s_host'].':'. $item['s_port'] ?></td>
        <td><?php echo $item['s_tags'] ?></td>
        <td><?php echo $item['gtid_mode'] ?></td>
        <td><?php echo $item['read_only'] ?></td>
        <td><?php echo check_value($item['slave_io_run']) ?></td>
        <td><?php echo check_value($item['slave_sql_run']) ?></td>
        <td><?php echo check_delay($item['delay']) ?>  </td>
        <td><?php echo $item['current_binlog_file'] ?></td>
        <td><?php echo $item['current_binlog_pos'] ?></td>
        <td><?php echo $item['master_binlog_file'] ?></td>
        <td><?php echo $item['master_binlog_pos'] ?></td>
        <td><?php echo check_binlog_space($item['m_binlog_space']) ?></td>
				<td><center><a href="<?php echo site_url('wl_mysql/dr_switch?group_id='.$item['group_id']) ?>"><?php echo $this->lang->line('manage'); ?></a></td>
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
