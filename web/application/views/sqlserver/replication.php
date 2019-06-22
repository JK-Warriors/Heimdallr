
        
        <ul class="breadcrumb">
            <li><a href=""><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_SQLServer Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Mirror Monitor'); ?></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">
                    
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>

<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span class="ui-icon ui-icon-search" style="float: left; margin-right: .3em;"></span>
                    
<form name="form" class="form-inline" method="get" action="<?php site_url('wl_sqlserver/replication') ?>" >
  <input type="hidden" name="search" value="submit" />
  
   <input type="text" id="host"  name="host" value="<?php echo $setval['host']; ?>" placeholder="<?php echo $this->lang->line('please_input_host'); ?>" class="input-medium" >
  <input type="text" id="tags"  name="tags" value="<?php echo $setval['tags']; ?>" placeholder="<?php echo $this->lang->line('please_input_tags'); ?>" class="input-medium" >

  
  <button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
  <a href="<?php echo site_url('wl_sqlserver/replication') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
  <button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>

</form>               
</div>




<div class="well">
    <table class="table table-hover  table-condensed " style="font-size: 12px;">
      <thead>
        <tr>
		<th colspan="4"><center><?php echo $this->lang->line('servers'); ?></center></th>
		<th colspan="2"><center><?php echo $this->lang->line('status'); ?></center></th>
		<th colspan="2"><center><?php echo $this->lang->line('lsn'); ?></center></th>
        <th ></th>
	   </tr>
        <tr>
        <th><?php echo $this->lang->line('host'); ?></th>
        <th><?php echo $this->lang->line('tags'); ?></th>
        <th><?php echo $this->lang->line('db_name'); ?></th>
        <th><?php echo $this->lang->line('db_role'); ?></th>
        <th><?php echo $this->lang->line('state'); ?></th>
        <th><?php echo $this->lang->line('safety_level'); ?></th>
        <th><?php echo $this->lang->line('end_of_log_lsn'); ?></th>
        <th><?php echo $this->lang->line('replication_lsn'); ?></th>
	   </tr>
      </thead>
      <tbody>
<?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
    <tr <?php if($item['connect']==0){echo "bgcolor='#FF0000'";} ?>>
        <td><?php echo $item['host'].':'. $item['port'] ?></td>
        <td><?php echo $item['tags'] ?></td>
        <td><?php echo $item['db_name'] ?></td>
        <td><?php echo check_mirror_role($item['mirroring_role']) ?></td>
        <td><?php echo check_mirror_state($item['mirroring_state']) ?></td>
        <td><?php echo check_mirror_safety_level($item['mirroring_safety_level']) ?></td>
        <td><?php echo $item['mirroring_end_of_log_lsn'] ?></td>
        <td><?php echo $item['mirroring_replication_lsn'] ?></td>
        
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
