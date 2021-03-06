
<ul class="breadcrumb">
<li class="active"><a href="<?php echo site_url('wl_oracle/index'); ?>"><?php echo $this->lang->line('_Oracle Monitor'); ?></a></li><span class="divider">/</span></li>
<li class="active"><?php echo $this->lang->line('_DiskGroup Monitor'); ?></li>
<span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">

<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
        
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form" class="form-inline" method="get" action="<?php site_url('wl_oracle/diskgroup') ?>" >

<input type="text" id="host"  name="host" value="<?php echo $setval['host']; ?>" placeholder="<?php echo $this->lang->line('please_input_host'); ?>" class="input-medium" >
<input type="text" id="tags"  name="tags" value="<?php echo $setval['tags']; ?>" placeholder="<?php echo $this->lang->line('please_input_tags'); ?>" class="input-medium" >


<button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
<a href="<?php echo site_url('wl_oracle/diskgroup') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
<button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>
</form>                
</div>


<div class="well">
<table class="table table-hover table-condensed ">
<thead>
<tr style="font-size: 12px;">
<th><?php echo $this->lang->line('host'); ?></th> 
<th><?php echo $this->lang->line('tags'); ?></th> 
<th><?php echo $this->lang->line('diskgroup_name'); ?></th>
    <th><?php echo $this->lang->line('status'); ?></th>
    <th><?php echo $this->lang->line('type'); ?></th>
    <th><?php echo $this->lang->line('total_size'); ?></th>
		<th><?php echo $this->lang->line('free_size'); ?></th>
    <th><?php echo $this->lang->line('used_rate'); ?></th>

</tr>
</thead>
<tbody>
<?php if(!empty($datalist)) {?>
<?php foreach ($datalist  as $item):?>
<tr style="font-size: 12px;">
<td><?php echo $item['host'] ?></td>
<td><?php echo $item['tags'] ?></td>
<td><?php echo $item['diskgroup_name'] ?></td>
<td><?php echo $item['state'] ?></td>
<td><?php echo $item['type'] ?></td>
<td><?php echo $item['total_mb'] ?>MB</td>
<td><?php echo $item['free_mb'] ?>MB</td>
<td><?php echo $item['used_rate'] ?>%</td>

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

