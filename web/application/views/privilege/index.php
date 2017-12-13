<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

     
<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('permission_system'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('privilege'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">
 
<div class="btn-toolbar">
    <a class="btn btn-primary " href="<?php echo site_url('privilege/add') ?>" title="<?php echo $this->lang->line('edit'); ?>" ><i class="icon-plus"></i> <?php echo $this->lang->line('add'); ?></a>
  <div class="btn-group"></div>
</div>

<div class="well">
    <table class="table table-hover ">
      <thead>
        <tr>
          <th><?php echo $this->lang->line('privilege_title'); ?></th>
          <th><?php echo $this->lang->line('action'); ?></th>
          <th><?php echo $this->lang->line('menu'); ?></th>
          <th><?php echo $this->lang->line('display_order'); ?></th>
          <th style="width: 30px;"></th>
        </tr>
      </thead>
      <tbody>
 <?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
     <tr>
          <td><?php echo $item['privilege_title'] ?></td>
          <td><?php echo $item['action'] ?></td>
          <td><?php echo $item['menu_title'] ?></td>
          <td><?php echo $item['display_order'] ?></td>
          <td>
              <a href="<?php echo site_url('privilege/edit/'.$item['privilege_id']) ?>"><i class="icon-pencil"></i></a>&nbsp;
              <a href="<?php echo site_url('privilege/forever_delete/'.$item['privilege_id']) ?>" class="confirm_delete" title="<?php echo $this->lang->line('forever_delete'); ?>" ><i class="icon-remove"></i></a>
          </td>
     </tr>
 <?php endforeach;?>

 <?php }else{  ?>
<tr>
<td colspan="4">
<font color="red"><?php echo $this->lang->line('no_record'); ?> </font>
</td>
</tr>
<?php } ?>      
      </tbody>
    </table>
</div>



<script type="text/javascript">
	$(' .confirm_delete').click(function(){
		return confirm("<?php echo $this->lang->line('forever_delete_confirm'); ?>");	
	});
</script>