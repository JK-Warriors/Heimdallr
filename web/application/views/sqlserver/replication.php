
        
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
				<th colspan="2"><center></center></th>
        <th colspan="3"><center><?php echo $this->lang->line('primary_db'); ?></center></th>
        <th colspan="3"><center><?php echo $this->lang->line('standby_db'); ?></center></th>
				<th colspan="2"><center><?php echo $this->lang->line('status'); ?></center></th>
				<th colspan="1"></th>
        <th ></th>
	   </tr>
        <tr>
        <th><center><?php echo $this->lang->line('mirror_name'); ?></th> 
        <th><center><?php echo $this->lang->line('db_name'); ?></th> 
        <th><center><?php echo $this->lang->line('ip') ?></th> 
        <th><center><?php echo $this->lang->line('port') ?></th> 
        <th><center><?php echo $this->lang->line('tags'); ?></th> 
        <th><center><?php echo $this->lang->line('ip'); ?></th> 
        <th><center><?php echo $this->lang->line('port') ?></th> 
        <th><center><?php echo $this->lang->line('tags'); ?></th> 
				<th><center><?php echo $this->lang->line('mirror_state'); ?></th>
				<th><center><?php echo $this->lang->line('safety_level'); ?></th>
				<th></th>
	   </tr>
      </thead>
      <tbody>
<?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
        <td><center><?php echo $item['mirror_name'] ?></td>
        <td><center><?php echo $item['db_name'] ?></td>
        <td><center><?php echo $item['p_host'] ?></td>
        <td><center><?php echo $item['p_port'] ?></td>
        <td><center><?php echo $item['p_tags'] ?></td>
        <td><center><?php echo $item['s_host'] ?></td>
        <td><center><?php echo $item['s_port'] ?></td>
        <td><center><?php echo $item['s_tags'] ?></td>
        <td><center><?php if(!empty($sta_list)) { ?>
 										<?php foreach ($sta_list as $s_item):?>
 										<?php    if($s_item['server_id']==$item['s_id'] and $s_item['db_name']==$item['db_name']){  ?>
 									  <?php    		echo check_mirror_state($s_item['mirroring_state']);   ?>
 									  <?php    	} ?>
 										<?php endforeach; ?>
 										<?php } ?></td>
        <td><center><?php if(!empty($sta_list)) { ?>
 										<?php foreach ($sta_list as $s_item):?>
 										<?php    if($s_item['server_id']==$item['s_id'] and $s_item['db_name']==$item['db_name']){  ?>
 									  <?php    		echo check_mirror_safety_level($s_item['mirroring_safety_level']);   ?>
 									  <?php    	} ?>
 										<?php endforeach; ?>
 										<?php } ?></td>
				<td><center><a href="<?php echo site_url('wl_sqlserver/mirror_switch?group_id='.$item['group_id']) ?>"><?php echo $this->lang->line('manage'); ?></a></td>
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
