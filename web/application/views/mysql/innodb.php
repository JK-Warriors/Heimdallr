
        
        <ul class="breadcrumb">
<li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
<li class="active"><?php echo $this->lang->line('_MySQL Monitor'); ?></li><span class="divider">/</span></li>
<li class="active"><?php echo $this->lang->line('_InnoDB Monitor'); ?></li>
<span class="right"><?php echo $this->lang->line('the_latest_acquisition_time'); ?>:<?php if(!empty($datalist)){ echo $datalist[0]['create_time'];} else {echo $this->lang->line('the_monitoring_process_is_not_started');} ?></span>
</ul>

<div class="container-fluid">
<div class="row-fluid">

<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<link href="lib/bootstrap/css/bootstrap-switch.css" rel="stylesheet"/>
        
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form name="form" class="form-inline" method="get" action="" >
<input type="hidden" name="search" value="submit" />

<input type="text" id="host"  name="host" value="" placeholder="<?php echo $this->lang->line('please_input_host'); ?>" class="input-medium" >
<input type="text" id="tags"  name="tags" value="" placeholder="<?php echo $this->lang->line('please_input_tags'); ?>" class="input-medium" >

<select name="order" class="input-small" style="width: 180px;">
<option value=""><?php echo $this->lang->line('sort'); ?></option>
<option value="id" <?php if($setval['order']=='id') echo "selected"; ?> ><?php echo $this->lang->line('default'); ?></option>
<option value="host" <?php if($setval['order']=='host') echo "selected"; ?> ><?php echo $this->lang->line('host'); ?></option>
<option value="innodb_buffer_pool_instances" <?php if($setval['order']=='innodb_buffer_pool_instances') echo "selected"; ?> ><?php echo $this->lang->line('buffer_pool_instances'); ?></option>
<option value="innodb_buffer_pool_size" <?php if($setval['order']=='innodb_buffer_pool_size') echo "selected"; ?> ><?php echo $this->lang->line('buffer_pool_size'); ?></option>
<option value="innodb_buffer_pool_pages_total" <?php if($setval['order']=='innodb_buffer_pool_pages_total') echo "selected"; ?> ><?php echo $this->lang->line('pages_total'); ?></option>
<option value="innodb_buffer_pool_pages_data" <?php if($setval['order']=='innodb_buffer_pool_pages_data') echo "selected"; ?> ><?php echo $this->lang->line('pages_data'); ?></option>
<option value="innodb_buffer_pool_pages_dirty" <?php if($setval['order']=='innodb_buffer_pool_pages_dirty') echo "selected"; ?> ><?php echo $this->lang->line('pages_dirty'); ?></option>
<option value="innodb_buffer_pool_pages_flushed" <?php if($setval['order']=='innodb_buffer_pool_pages_flushed') echo "selected"; ?> ><?php echo $this->lang->line('pages_flushed'); ?></option>
<option value="innodb_buffer_pool_pages_free" <?php if($setval['order']=='innodb_buffer_pool_pages_free') echo "selected"; ?> ><?php echo $this->lang->line('pages_free'); ?></option>
<option value="innodb_io_capacity" <?php if($setval['order']=='innodb_io_capacity') echo "selected"; ?> ><?php echo $this->lang->line('io_capacity'); ?></option>
<option value="innodb_read_io_threads" <?php if($setval['order']=='innodb_read_io_threads') echo "selected"; ?> ><?php echo $this->lang->line('read_io_threads'); ?></option>
<option value="innodb_write_io_threads" <?php if($setval['order']=='innodb_write_io_threads') echo "selected"; ?> ><?php echo $this->lang->line('write_io_threads'); ?></option>
<option value="innodb_rows_read_persecond" <?php if($setval['order']=='innodb_rows_read_persecond') echo "selected"; ?> ><?php echo $this->lang->line('read'); ?></option>
<option value="innodb_rows_inserted_persecond" <?php if($setval['order']=='innodb_rows_inserted_persecond') echo "selected"; ?> ><?php echo $this->lang->line('inserted'); ?></option>
<option value="innodb_rows_updated_persecond" <?php if($setval['order']=='innodb_rows_updated_persecond') echo "selected"; ?> ><?php echo $this->lang->line('updated'); ?></option>
<option value="innodb_rows_deleted_persecond" <?php if($setval['order']=='innodb_rows_deleted_persecond') echo "selected"; ?> ><?php echo $this->lang->line('deleted'); ?></option>

</select>
<select name="order_type" class="input-small" style="width: 70px;">
<option value="asc" <?php if($setval['order_type']=='asc') echo "selected"; ?> ><?php echo $this->lang->line('asc'); ?></option>
<option value="desc" <?php if($setval['order_type']=='desc') echo "selected"; ?> ><?php echo $this->lang->line('desc'); ?></option>
</select>

<button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
<a href="<?php echo site_url('wl_mysql/innodb') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
<button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>
</form>                
</div>


<div class="well">
<table class="table table-hover table-condensed  table-bordered">
<thead>
<tr style="font-size: 12px;">
<th colspan="2"><center><?php echo $this->lang->line('servers'); ?></center></th>
<th colspan="2"><center><?php echo $this->lang->line('buffer_pool'); ?></center></th>
<th colspan="5"><center><?php echo $this->lang->line('pages'); ?></center></th>
<th colspan="3"><center><?php echo $this->lang->line('io'); ?></center></th>
<th colspan="4"><center><?php echo $this->lang->line('rows'); ?>(<?php echo $this->lang->line('persecond'); ?>)</center></th>
<th colspan="1"><center></center></th>
</tr>
<tr style="font-size: 12px;">
<th><center><?php echo $this->lang->line('host'); ?></center></th>
<th><center><?php echo $this->lang->line('tags'); ?></center></th> 
<th><center><?php echo $this->lang->line('buffer_pool_instances'); ?></center></th> 
<th><center><?php echo $this->lang->line('buffer_pool_size'); ?></center></th> 
<th><center><?php echo $this->lang->line('pages_total'); ?></center></th> 
<th><center><?php echo $this->lang->line('pages_data'); ?></center></th> 
<th><center><?php echo $this->lang->line('pages_dirty'); ?></center></th> 
<th><center><?php echo $this->lang->line('pages_flushed'); ?></center></th> 
<th><center><?php echo $this->lang->line('pages_free'); ?></center></th> 
<th><center><?php echo $this->lang->line('io_capacity'); ?></center></th> 
<th><center><?php echo $this->lang->line('read_io_threads'); ?></center></th> 
<th><center><?php echo $this->lang->line('write_io_threads'); ?></center></th> 
<th><center><?php echo $this->lang->line('read'); ?></center></th> 
<th><center><?php echo $this->lang->line('inserted'); ?></center></th> 
<th><center><?php echo $this->lang->line('updated'); ?></center></th> 
<th><center><?php echo $this->lang->line('deleted'); ?></center></th> 
<th><center><?php echo $this->lang->line('chart'); ?></th>
</tr>
</thead>
<tbody>
<?php if(!empty($datalist)) {?>
<?php foreach ($datalist  as $item):?>
<tr style="font-size: 12px;">
<td><?php echo $item['host'] ?>:<?php echo $item['port'] ?></td>
<td><?php echo $item['tags']; ?></td>
<td><?php echo $item['innodb_buffer_pool_instances']; ?></td>
<td><?php echo format_bytes($item['innodb_buffer_pool_size']); ?></td>
<td><?php echo $item['innodb_buffer_pool_pages_total']; ?></td>
<td><?php echo $item['innodb_buffer_pool_pages_data']; ?></td>
<td><?php echo $item['innodb_buffer_pool_pages_dirty']; ?></td>
<td><?php echo $item['innodb_buffer_pool_pages_flushed']; ?></td>
<td><?php echo $item['innodb_buffer_pool_pages_free']; ?></td>
<td><?php echo $item['innodb_io_capacity']; ?></td>
<td><?php echo $item['innodb_read_io_threads'] ?></td>
<td><?php echo $item['innodb_write_io_threads'] ?></td>
<td><?php echo $item['innodb_rows_read_persecond'] ?></td>
<td><?php echo $item['innodb_rows_inserted_persecond'] ?></td>
<td><?php echo $item['innodb_rows_updated_persecond'] ?></td>
<td><?php echo $item['innodb_rows_deleted_persecond'] ?></td>
<td><a href="<?php echo site_url('wl_mysql/innodb_chart/'.$item['server_id']) ?>"><i class="icon-bar-chart"></i><?php echo $this->lang->line('view_chart'); ?></a></td>
</tr>
<?php endforeach;?>
<?php }else{  ?>
<tr>
<td colspan="17">
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

