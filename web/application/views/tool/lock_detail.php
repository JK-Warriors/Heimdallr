<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

        
<ul class="breadcrumb">
            <li class="active"><a href="<?php echo site_url('wl_lock/index'); ?>"><?php echo $this->lang->line('_Tool Box'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Lock List'); ?></li><span class="divider"></span></li>
            <span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>: <?php if(!empty($sta_list)){ echo $sta_list[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">
 
<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form" class="form-inline" method="get" action="<?php echo site_url('wl_lock/lock_detail') ?>" >
  <button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>
</form>                
</div>


<div class="well">
    <table class="table table-hover table-condensed ">
      <thead>
        
        <tr style="font-size: 12px;">
        <th><center><?php echo $this->lang->line('db_type'); ?></th> 
        <th><center><?php echo $this->lang->line('instance_name');?></th> 
				<th><center><?php echo $this->lang->line('host_type'); ?></th>
        <th><center><?php echo $this->lang->line('ip'); ?></th>
				<th><center><?php echo $this->lang->line('opration'); ?></th>
	    </tr>
      </thead>
      <tbody>
 <?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
    <tr style="font-size: 12px;">
        <td><center><?php echo $item['db_type'] ?></td>
        <td><center><?php echo $item['dsn'] ?></td>
        <td><center><?php echo $item['host_type'] ?></td>
        <td><center><?php echo $item['host'] ?></td>
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
    $('#refresh').click(function(){
        document.location.reload(); 
    })
 </script>

