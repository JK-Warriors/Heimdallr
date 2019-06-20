
        
        <ul class="breadcrumb">
<li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
<li class="active"><?php echo $this->lang->line('_MySQL Monitor'); ?></li><span class="divider">/</span></li>
<li class="active"><?php echo $this->lang->line('_Key Cache Monitor'); ?></li>
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
<option value="key_buffer_size" <?php if($setval['order']=='key_buffer_size') echo "selected"; ?> ><?php echo $this->lang->line('key_buffer_size'); ?></option>
<option value="sort_buffer_size" <?php if($setval['order']=='sort_buffer_size') echo "selected"; ?> ><?php echo $this->lang->line('sort_buffer_size'); ?></option>
<option value="join_buffer_size" <?php if($setval['order']=='join_buffer_size') echo "selected"; ?> ><?php echo $this->lang->line('join_buffer_size'); ?></option>
<option value="key_blocks_unused" <?php if($setval['order']=='key_blocks_unused') echo "selected"; ?> ><?php echo $this->lang->line('key_blocks_unused'); ?></option>
<option value="key_blocks_used" <?php if($setval['order']=='key_blocks_used') echo "selected"; ?> ><?php echo $this->lang->line('key_blocks_used'); ?></option>
<option value="key_blocks_used_rate" <?php if($setval['order']=='key_blocks_used_rate') echo "selected"; ?> ><?php echo $this->lang->line('key_blocks_used_rate'); ?></option>
<option value="key_buffer_read_rate" <?php if($setval['order']=='key_buffer_read_rate') echo "selected"; ?> ><?php echo $this->lang->line('key_buffer_read_rate'); ?></option>
<option value="key_buffer_write_rate" <?php if($setval['order']=='key_buffer_write_rate') echo "selected"; ?> ><?php echo $this->lang->line('key_buffer_write_rate'); ?></option>

</select>
<select name="order_type" class="input-small" style="width: 70px;">
<option value="asc" <?php if($setval['order_type']=='asc') echo "selected"; ?> ><?php echo $this->lang->line('asc'); ?></option>
<option value="desc" <?php if($setval['order_type']=='desc') echo "selected"; ?> ><?php echo $this->lang->line('desc'); ?></option>
</select>

<button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
<a href="<?php echo site_url('wl_mysql/key_cache') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
<button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>
</form>                
</div>


<div class="well">
<table class="table table-hover table-condensed table-bordered">
<thead>
<tr style="font-size: 12px;">
<th colspan="2"><center><?php echo $this->lang->line('servers'); ?></th>
<th colspan="3"><center><?php echo $this->lang->line('key_buffer'); ?></center></th>
<th colspan="3"><center><?php echo $this->lang->line('key_blocks'); ?></center></th>
<th colspan="3"><center><?php echo $this->lang->line('rate'); ?></center></th>
<th colspan="1"></th>

</tr>
<tr style="font-size: 12px;">
<th><center><?php echo $this->lang->line('host'); ?></th> 
<th><center><?php echo $this->lang->line('tags'); ?></th>
<th><center><?php echo $this->lang->line('key_buffer_size'); ?></th>
<th><center><?php echo $this->lang->line('sort_buffer_size'); ?></th>
<th><center><?php echo $this->lang->line('join_buffer_size'); ?></th>
<th><center><?php echo $this->lang->line('key_blocks_unused'); ?></th>
<th><center><?php echo $this->lang->line('key_blocks_used'); ?></th>
<th><center><?php echo $this->lang->line('key_blocks_not_flushed'); ?></th>
<th><center><?php echo $this->lang->line('key_blocks_used_rate'); ?></th>
<th><center><?php echo $this->lang->line('key_buffer_read_rate'); ?></th>
<th><center><?php echo $this->lang->line('key_buffer_write_rate'); ?></th>
<th><center><?php echo $this->lang->line('chart'); ?></th>
</tr>
</thead>
<tbody>
<?php if(!empty($datalist)) {?>
<?php foreach ($datalist  as $item):?>
<tr style="font-size: 12px;">
<td><?php echo $item['host'] ?>:<?php echo $item['port'] ?></td>
<td><?php echo $item['tags'] ?></td>
<td><?php echo format_bytes($item['key_buffer_size']); ?></td>
<td><?php echo format_bytes($item['join_buffer_size']); ?></td>
<td><?php echo format_bytes($item['sort_buffer_size']); ?></td>
<td><?php echo $item['key_blocks_unused'] ?></td>
<td><?php echo $item['key_blocks_used'] ?></td>
<td><?php echo $item['key_blocks_not_flushed'] ?></td>
<td><?php echo format_rate($item['key_blocks_used_rate']); ?></td>
<td><?php echo format_rate($item['key_buffer_read_rate']); ?></td>
<td><?php echo format_rate($item['key_buffer_write_rate']); ?></td>
<td><a href="<?php echo site_url('wl_mysql/key_cache_chart/'.$item['server_id']) ?>"><i class="icon-bar-chart"></i><?php echo $this->lang->line('view_chart'); ?></a></td>
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

