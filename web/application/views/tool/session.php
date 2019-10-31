<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_tool/lock'); ?>"><?php echo $this->lang->line('_Tool Box'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_DB List'); ?></li><span class="divider"></span></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>: <?php if(!empty($datalist)){ echo $datalist[0]['uptime_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form" class="form-inline" method="get" action="<?php echo site_url('wl_tool/session') ?>" >
 
  <select name="db_type" class="input-small" style="width: 120px;">
  <option value="" <?php if($setval['db_type']=='') echo "selected"; ?> ><?php echo $this->lang->line('db_type'); ?></option>
  <option value="mysql" <?php if($setval['db_type']=='mysql') echo "selected"; ?> ><?php echo $this->lang->line('mysql'); ?></option>
  <option value="oracle" <?php if($setval['db_type']=='oracle') echo "selected"; ?> ><?php echo $this->lang->line('oracle'); ?></option>
  <option value="sqlserver" <?php if($setval['db_type']=='sqlserver') echo "selected"; ?> ><?php echo $this->lang->line('sqlserver'); ?></option>
  </select>
  
  <input type="text" id="host"  name="host" value="<?php echo $setval['host']; ?>" placeholder="<?php echo $this->lang->line('please_input_host'); ?>" class="input-medium" >
  <input type="text" id="tags"  name="tags" value="<?php echo $setval['tags']; ?>" placeholder="<?php echo $this->lang->line('please_input_tags'); ?>" class="input-medium" >
  

  <button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
  <a href="<?php echo site_url('wl_tool/session') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
  <button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>
</form>                
</div>


<div class="well">
    <table class="table table-hover table-condensed ">
      <thead>
        
        <tr style="font-size: 12px;">
        <th><center><?php echo $this->lang->line('db_type'); ?></th> 
        <th><center><?php echo $this->lang->line('host'); ?></th>
        <th><center><?php echo $this->lang->line('role'); ?></th> 
        <th><center><?php echo $this->lang->line('tags'); ?></th>
        <th><center><?php echo $this->lang->line('version'); ?></th>
        <th><center><?php echo $this->lang->line('connect'); ?></th>
				<th><center><?php echo $this->lang->line('opration'); ?></th>
	    </tr>
      </thead>
      <tbody>
 <?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
    <tr style="font-size: 12px;">
        <td><center><?php echo check_dbtype($item['db_type']) ?></td>
        <td><center><?php echo $item['host'] ?>:<?php echo $item['port'] ?></td>
        <td><center><?php echo check_db_status_role($item['role']) ?></td>
        <td><center><?php echo $item['tags'] ?></td>
        <td><center><?php echo check_value($item['version']) ?></td>
        <td><center><?php echo check_db_status_level($item['connect'],$item['connect_tips']) ?></td>
				<td><center><a href="<?php echo site_url('wl_tool/session_trace?server_id='.$item['server_id'].'&db_type='.$item['db_type']) ?>"><?php echo $this->lang->line('session_trace'); ?></a></td>
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
    $('#refresh').click(function(){
        document.location.reload(); 
    })
 </script>

