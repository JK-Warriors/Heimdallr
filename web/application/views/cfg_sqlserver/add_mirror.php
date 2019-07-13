<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<ul class="breadcrumb">
           <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
           <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
           <li class="active"><?php echo $this->lang->line('_SQLServer'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">

<form name="form" class="form-horizontal" method="post" action="<?php if($group_id != ""){echo site_url('cfg_sqlserver/edit_mirror/') . "/" . $group_id ;}else{echo site_url('cfg_sqlserver/add_mirror');} ?>" >
<input type="hidden" id="submit" name="submit" value="dg_manage"/> 
<div class="btn-toolbar">
   <a class="btn btn " href="<?php echo site_url('cfg_sqlserver/index') ?>"><i class="icon-return"></i> <?php echo $this->lang->line('return'); ?></a>
 <div class="btn-group"></div>
</div>

<?php if ($error_code!==0) { ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">×</button>
<?php echo validation_errors(); ?>
</div>
<?php } ?>

<div class="well">
 <div class="control-group">
   <label class="control-label" for="">*<?php echo $this->lang->line('mirror_name'); ?></label>
   <div class="controls">
     <input type="text" id="mirror_name"  name="mirror_name" style="width: 200px;">
   </div>
  </div>

  <div class="control-group">
   <label class="control-label" for="">*<?php echo $this->lang->line('primary_db'); ?></label>
   <div class="controls">
     <select name="primary_db" id="primary_db" class="input-large"  >
       <option value=""><?php echo $this->lang->line('primary_db'); ?></option>
       <?php foreach ($datalist as $item):?>
       <option value="<?php echo $item['id'];?>" ><?php echo $item['host'];?>:<?php echo $item['port'];?>(<?php echo $item['tags'];?>)</option>
       <?php endforeach;?>
       </select>
       <span class="help-inline"></span>
   </div>
  </div>
  
  <div class="control-group">
   <label class="control-label" for="">*<?php echo $this->lang->line('standby_db'); ?></label>
   <div class="controls">
     <select name="standby_db" id="standby_db" class="input-large"  >
       <option value=""><?php echo $this->lang->line('standby_db'); ?></option>
       <?php foreach ($datalist as $item):?>
       <option value="<?php echo $item['id'];?>" ><?php echo $item['host'];?>:<?php echo $item['port'];?>(<?php echo $item['tags'];?>)</option>
       <?php endforeach;?>
       </select>
       <span class="help-inline"></span>
   </div>
  </div>



   <div class="control-group">
   <label class="control-label" for="">*数据库名</label>
   <div class="controls">
     <input type="text" id="db_name"  name="db_name" style="width: 200px;">
   </div>
  </div>


  <div class="controls">
   <button type="submit" id="btn_save" class="btn btn-primary"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>
  <div class="btn-group"></div>
  </div>
  
  
  <hr />
  
  <table class="table table-hover table-bordered">
     <thead>
       <th colspan="3"><center><?php echo $this->lang->line('mirror_name'); ?></center></th>
       <th colspan="3"><center><?php echo $this->lang->line('primary_db'); ?></center></th>
       <th colspan="3"><center><?php echo $this->lang->line('standby_db'); ?></center></th>
       <th colspan="1"></th>
       <tr style="font-size: 12px;">
       <th><?php echo $this->lang->line('mirror_id'); ?></th>
       <th><?php echo $this->lang->line('mirror_name'); ?></th>
       <th><?php echo $this->lang->line('db_name'); ?></th>
       <th><?php echo $this->lang->line('host'); ?></th>
       <th><?php echo $this->lang->line('port'); ?></th>
       <th><?php echo $this->lang->line('tags'); ?></th>
       <th><?php echo $this->lang->line('host'); ?></th>
       <th><?php echo $this->lang->line('port'); ?></th>
       <th><?php echo $this->lang->line('tags'); ?></th></th>
       <th></th>
 </tr>
     </thead>
     <tbody>
<?php if(!empty($mirror_list)) {?>
<?php foreach ($mirror_list  as $item):?>
   <tr style="font-size: 12px;">
       <td><?php echo $item['id'] ?></td>
       <td><?php echo $item['mirror_name'] ?></td>
       <td><?php echo $item['db_name'] ?></td>
       <td><?php echo $item['pri_host'] ?></td>
       <td><?php echo $item['pri_port'] ?></td>
       <td><?php echo $item['pri_tags'] ?></td>
       <td><?php echo $item['sta_host'] ?></td>
       <td><?php echo $item['sta_port'] ?></td>
       <td><?php echo $item['sta_tags'] ?></td></td>
       <td><a href="<?php echo site_url('cfg_sqlserver/edit_mirror/'.$item['id']) ?>"  title="<?php echo $this->lang->line('edit'); ?>" ><i class="icon-pencil"></i></a>&nbsp;
       <a href="<?php echo site_url('cfg_sqlserver/delete_mirror/'.$item['id']) ?>" class="confirm_delete" title="<?php echo $this->lang->line('delete'); ?>" ><i class="icon-remove"></i></a>
       </td>
 </tr>
<?php endforeach;?>
<tr>
<td colspan="13">
<font color="#000000"><?php echo $this->lang->line('total_record'); ?> <?php echo $dgcount; ?></font>
</td>
</tr>
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


</form>

<script type="text/javascript">
 var group_id = "<?php echo $group_id ?>";
 var mirror_name = "<?php echo $mirror[0]['mirror_name'] ?>";
 var primary_db = "<?php echo $mirror[0]['primary_db_id'] ?>";
 var standby_db = "<?php echo $mirror[0]['standby_db_id'] ?>";
 var db_name = "<?php echo $mirror[0]['db_name'] ?>";
 var error_code = "<?php echo $error_code ?>";
 
 $('.confirm_delete').click(function(){
   return confirm("<?php echo $this->lang->line('delete_confirm'); ?>");	
 });
 
 


 $(document).ready(function(){  
   
   if(group_id != ""){
     $("#mirror_name").val(mirror_name);
     $("#primary_db").val(primary_db);
     $("#standby_db").val(standby_db);
     $("#db_name").val(db_name);
   }
   

 });

 

</script>

