<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<ul class="breadcrumb">
           <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
           <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
           <li class="active"><?php echo $this->lang->line('_MySQL'); ?></li>
</ul>

<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/layer3/layer.js"></script>

<div class="container-fluid">
<div class="row-fluid">

<form id="form_dr" class="form-horizontal" method="post" action="<?php if($group_id != ""){echo site_url('cfg_mysql/edit_dr/') . "/" . $group_id ;}else{echo site_url('cfg_mysql/add_dr');} ?>" >
<input type="hidden" id="submit" name="submit" value="dg_manage"/> 
<div class="btn-toolbar">
   <a class="btn btn " href="<?php echo site_url('cfg_mysql/index') ?>"><i class="icon-return"></i> <?php echo $this->lang->line('return'); ?></a>
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
   <label class="control-label" for="">*<?php echo $this->lang->line('group_name'); ?></label>
   <div class="controls">
     <input type="text" id="group_name"  name="group_name" style="width: 200px;">
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




  <div class="controls">
   <button type="button" id="btn_save" onclick="checkLicense(this)" class="btn btn-primary"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>
  <div class="btn-group"></div>
  </div>
  
  
  <hr />
  
  <table class="table table-hover table-bordered">
     <thead>
       <th colspan="2"><center><?php echo $this->lang->line('dr_group'); ?></center></th>
       <th colspan="3"><center><?php echo $this->lang->line('primary_db'); ?></center></th>
       <th colspan="3"><center><?php echo $this->lang->line('standby_db'); ?></center></th>
       <th colspan="1"></th>
       <tr style="font-size: 12px;">
       <th><?php echo $this->lang->line('group_id'); ?></th>
       <th><?php echo $this->lang->line('group_name'); ?></th>
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
<?php if(!empty($dr_list)) {?>
<?php foreach ($dr_list  as $item):?>
   <tr style="font-size: 12px;">
       <td><?php echo $item['id'] ?></td>
       <td><?php echo $item['group_name'] ?></td>
       <td><?php echo $item['pri_host'] ?></td>
       <td><?php echo $item['pri_port'] ?></td>
       <td><?php echo $item['pri_tags'] ?></td>
       <td><?php echo $item['sta_host'] ?></td>
       <td><?php echo $item['sta_port'] ?></td>
       <td><?php echo $item['sta_tags'] ?></td></td>
       <td><a href="<?php echo site_url('cfg_mysql/edit_dr/'.$item['id']) ?>"  title="<?php echo $this->lang->line('edit'); ?>" ><i class="icon-pencil"></i></a>&nbsp;
       <a href="<?php echo site_url('cfg_mysql/delete_dr/'.$item['id']) ?>" class="confirm_delete" title="<?php echo $this->lang->line('delete'); ?>" ><i class="icon-remove"></i></a>
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
 var group_name = "<?php echo $dr[0]['group_name'] ?>";
 var primary_db = "<?php echo $dr[0]['primary_db_id'] ?>";
 var standby_db = "<?php echo $dr[0]['standby_db_id'] ?>";
 var error_code = "<?php echo $error_code ?>";
 
 $('.confirm_delete').click(function(){
   return confirm("<?php echo $this->lang->line('delete_confirm'); ?>");	
 });
 
 


 $(document).ready(function(){  
   
   if(group_id != ""){
     $("#group_name").val(group_name);
     $("#primary_db").val(primary_db);
     $("#standby_db").val(standby_db);
   }
   

 });
 
 var target_url="<?php if($group_id==""){echo site_url('cfg_mysql/add_dr');}else{echo site_url('cfg_mysql/edit_dr');} ?>";
 var submit="<?php if($group_id==""){echo 'add_dr';}else{echo 'edit_dr';} ?>";
 
 function checkLicense(e){
 
   	
 		var dr_count = parseInt("<?php echo $dr_count ?>");
 		var dr_license_quota = parseInt("<?php echo $dr_quota ?>");
 		
 		if($("#group_name").val() == ""){
				bootbox.alert({
		        		message: "请输入容灾组名!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
				        	
		}else if($("#primary_db").val() == ""){
				bootbox.alert({
		        		message: "请选择主库!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
				        	
		}else if($("#standby_db").val() == ""){
				bootbox.alert({
		        		message: "请选择备库!",
		        		buttons: {
							        ok: {
							            label: '确定',
							            className: 'btn-success'
							        }
							    }
		        	});
				        	
		}else if(group_id=="" &&  dr_count >= dr_license_quota){
			bootbox.alert({
	        		message: "您已经超出了容灾组授权限制，请删除后再添加!",
	        		buttons: {
						        ok: {
						            label: '确定',
						            className: 'btn-success'
						        }
						    }
	        	});
 			
 		}else{
	    $.ajax({
			        url: target_url,
			        data: $("#form_dr").serializeArray(),
			        data: {"submit":submit,"group_id":group_id,"group_name":$("#group_name").val(),"primary_db":$("#primary_db").val(),"standby_db":$("#standby_db").val()},
			        type: "POST",
			        success: function (data) {
			  			//回调函数，判断提交返回的数据执行相应逻辑
			            if (data.Success) {
			            }
			            else {
			            }
			            
			            if(data.error_code==-1){
		  									bootbox.alert({
								        		message: data.error_message,
								        		buttons: {
													        ok: {
													            label: '确定',
													            className: 'btn-success'
													        }
													    }
								        	});
		          		}else{
											window.location.reload();
		          		}
		          			
			        }
						});
 		}
 		
 }

</script>

