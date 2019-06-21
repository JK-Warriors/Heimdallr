     
<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_SQLServer Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Health Monitor'); ?></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form" class="form-inline" method="get" action="" >


  <input type="text" id="host"  name="host" value="<?php echo $setval['host']; ?>" placeholder="<?php echo $this->lang->line('please_input_host'); ?>" class="input-medium" >
  <input type="text" id="tags"  name="tags" value="<?php echo $setval['tags']; ?>" placeholder="<?php echo $this->lang->line('please_input_tags'); ?>" class="input-medium" >
  

  <button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
  <a href="<?php echo site_url('wl_sqlserver/health') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
  <button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>
</form>                
</div>


<div class="well">
    <table class="table table-hover table-condensed ">
      <thead>
        <tr style="font-size: 12px;">
		<th colspan="2"><center><?php echo $this->lang->line('servers'); ?></center></th>
        <th colspan="3"><center><?php echo $this->lang->line('basic_info'); ?></center></th>
		<th colspan="4"><center><?php echo $this->lang->line('processes'); ?></center></th>
            <th colspan="3"><center><?php echo $this->lang->line('network'); ?></center></th>
        <th ></th>
	   </tr>
        <tr style="font-size: 12px;">
        <th><?php echo $this->lang->line('host'); ?></th> 
        <th><?php echo $this->lang->line('tags'); ?></th> 
		<th><?php echo $this->lang->line('connect'); ?></th>
        <th><?php echo $this->lang->line('uptime'); ?></th>
		<th><?php echo $this->lang->line('version'); ?></th>
            <th><?php echo $this->lang->line('max_connections'); ?></th>
         <th><?php echo $this->lang->line('processes'); ?></th>
		<th><?php echo $this->lang->line('processes_running'); ?></th>
        <th><?php echo $this->lang->line('processes_waits'); ?></th>
            <th><?php echo $this->lang->line('received'); ?></th>
            <th><?php echo $this->lang->line('sent'); ?></th>
            <th><?php echo $this->lang->line('errors'); ?></th>
        <th><?php echo $this->lang->line('chart'); ?></th>
	    </tr>
      </thead>
      <tbody>
 <?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
    <tr style="font-size: 12px;">
        <td><?php echo $item['host'] ?>:<?php echo $item['port'] ?></td>
		<td><?php echo $item['tags'] ?></td>
		<td><?php if($item['connect']=='1'){ ?> <span class="label label-success"><?php echo $this->lang->line('success'); ?></span> <?php }else{  ?><span class="label label-important"><?php echo $this->lang->line('failure'); ?></span> <?php } ?></td>
        <td><?php echo check_uptime($item['uptime']) ?></td>
        <td><?php echo check_value($item['version']) ?></td>
        <td><?php echo check_value($item['max_connections']) ?></td>
        <td><?php echo check_value($item['processes']) ?></td>
        <td><?php echo check_value($item['processes_running']) ?></td>
        <td><?php echo check_value($item['processes_waits']) ?></td>
        <td><?php echo check_value($item['pack_received_persecond']) ?></td>
        <td><?php echo check_value($item['pack_sent_persecond']) ?></td>
        <td><?php echo check_value($item['packet_errors_persecond']) ?></td>
     
        <td><?php if($item['connect']=='1'){ ?><a href="<?php echo site_url('wl_sqlserver/chart/'.$item['server_id']) ?>"><i class="icon-bar-chart"></i><?php echo $this->lang->line('view_chart'); ?></a> <?php }else{  ?>--- <?php } ?></td>
	</tr>
 <?php endforeach;?>
 <?php }else{  ?>
<tr>
<td colspan="13">
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

