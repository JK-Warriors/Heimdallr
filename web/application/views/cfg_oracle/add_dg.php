<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<ul class="breadcrumb">
           <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
           <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
           <li class="active"><?php echo $this->lang->line('_Oracle'); ?></li>
</ul>

<script src="lib/bootstrap/js/bootstrap-switch.js"></script>
<script src="lib/bootstrap/js/bootbox.js"></script>
<script src="lib/layer3/layer.js"></script>

<div class="container-fluid">
<div class="row-fluid">

<form id="form" name="form" class="form-horizontal" method="post" action="<?php if($group_id != ""){echo site_url('cfg_oracle/edit_dg/') . "/" . $group_id ;}else{echo site_url('cfg_oracle/add_dg');} ?>" >
<input type="hidden" id="submit" name="submit" value="dg_manage"/> 
<div class="btn-toolbar">
   <a class="btn btn " href="<?php echo site_url('cfg_oracle/index') ?>"><i class="icon-return"></i> <?php echo $this->lang->line('return'); ?></a>
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
   <label class="control-label" for="">*<?php echo $this->lang->line('primary_dest'); ?></label>
   <div class="controls">
     <select name="primary_dest" id="primary_dest" class="input-large"  >
       <?php for($x=1; $x<=31; $x++){?>
       <option value="<?php echo $x; ?>" ><?php echo $x; ?></option>
       <?php } ?>
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
   <label class="control-label" for="">*<?php echo $this->lang->line('standby_dest'); ?></label>
   <div class="controls">
     <select name="standby_dest" id="standby_dest" class="input-large"  >
       <?php for($x=1; $x<=31; $x++){?>
       <option value="<?php echo $x; ?>" ><?php echo $x; ?></option>
       <?php } ?>
       </select>
       <span class="help-inline"></span>
   </div>
  </div>
   <div class="control-group">
   <label class="control-label" for="">备库闪回保留天数：</label>
   <div class="controls">
     <input type="text" id="fb_retention"  name="fb_retention" style="width: 200px;">
   </div>
  </div>

  
  <div class="control-group">
   <label class="control-label" for="">切换时是否漂移IP：</label>
   <div class="controls">
     <input type="checkbox" id="shift_vip"  name="shift_vip" value="1">
   </div>
  </div>
  <div id="div_node_vips" class="control-group">
   <label class="control-label" for="">漂移IP：</label>
   <div class="controls">
     <input type="text" id="node_vips"  name="node_vips" style="width: 300px;">
   </div>
   <div class="controls">
   	<label>注：多个IP请使用逗号分割</label>
  	</div>
  </div>
  <div id="div_network_card_p" class="control-group">
   <label class="control-label" for="">主库网卡名称：</label>
   <div class="controls">
     <input type="text" id="network_card_p"  name="network_card_p" style="width: 300px;">
   </div>
  </div>
  <div id="div_network_card_s" class="control-group">
   <label class="control-label" for="">备库网卡名称：</label>
   <div class="controls">
     <input type="text" id="network_card_s"  name="network_card_s" style="width: 300px;">
   </div>
  </div>

  <div class="controls">
   <button type="button" id="btn_save" onclick="checkLicense(this)" class="btn btn-primary"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>
  <div class="btn-group"></div>
  </div>
  
  
  <hr />
  
  <table class="table table-hover table-bordered">
     <thead>
       <th colspan="2"><center><?php echo $this->lang->line('dg_group'); ?></center></th>
       <th colspan="4"><center><?php echo $this->lang->line('primary_db'); ?></center></th>
       <th colspan="5"><center><?php echo $this->lang->line('standby_db'); ?></center></th>
       <th colspan="1"></th>
       <tr style="font-size: 12px;">
       <th><?php echo $this->lang->line('group_id'); ?></th>
       <th><?php echo $this->lang->line('group_name'); ?></th>
       <th><?php echo $this->lang->line('host'); ?></th>
       <th><?php echo $this->lang->line('port'); ?></th>
       <th><?php echo $this->lang->line('dsn'); ?></th>
       <th><?php echo $this->lang->line('tags'); ?></th>
       <th><?php echo $this->lang->line('host'); ?></th>
       <th><?php echo $this->lang->line('port'); ?></th>
       <th><?php echo $this->lang->line('dsn'); ?></th>
       <th><?php echo $this->lang->line('tags'); ?></th>
       <th><?php echo $this->lang->line('fb_retention'); ?></th>
       <th></th>
 </tr>
     </thead>
     <tbody>
<?php if(!empty($dglist)) {?>
<?php foreach ($dglist  as $item):?>
   <tr style="font-size: 12px;">
       <td><?php echo $item['id'] ?></td>
       <td><?php echo $item['group_name'] ?></td>
       <td><?php echo $item['pri_host'] ?></td>
       <td><?php echo $item['pri_port'] ?></td>
       <td><?php echo $item['pri_dsn'] ?></td>
       <td><?php echo $item['pri_tags'] ?></td>
       <td><?php echo $item['sta_host'] ?></td>
       <td><?php echo $item['sta_port'] ?></td>
       <td><?php echo $item['sta_dsn'] ?></td>
       <td><?php echo $item['sta_tags'] ?></td>
       <td><?php echo $item['fb_retention'] ?></td>
       <td><a href="<?php echo site_url('cfg_oracle/edit_dg/'.$item['id']) ?>"  title="<?php echo $this->lang->line('edit'); ?>" ><i class="icon-pencil"></i></a>&nbsp;
       <a href="<?php echo site_url('cfg_oracle/delete_dg/'.$item['id']) ?>" class="confirm_delete" title="<?php echo $this->lang->line('delete'); ?>" ><i class="icon-remove"></i></a>
       </td>
 </tr>
<?php endforeach;?>
<tr>
<td colspan="13">
<font color="#000000"><?php echo $this->lang->line('total_record'); ?> <?php echo $dg_count; ?></font>
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
 var group_name = "<?php echo $dg[0]['group_name'] ?>";
 var primary_db = "<?php echo $dg[0]['primary_db_id'] ?>";
 var primary_dest = "<?php echo $dg[0]['primary_db_dest'] ?>";
 var standby_db = "<?php echo $dg[0]['standby_db_id'] ?>";
 var standby_dest = "<?php echo $dg[0]['standby_db_dest'] ?>";
 var fb_retention = "<?php echo $dg[0]['fb_retention'] ?>";
 var shift_vip = "<?php echo $dg[0]['shift_vip'] ?>";
 var node_vips = "<?php echo $dg[0]['node_vips'] ?>";
 var network_card_p = "<?php echo $dg[0]['network_card_p'] ?>";
 var network_card_s = "<?php echo $dg[0]['network_card_s'] ?>";
 var error_code = "<?php echo $error_code ?>";
 
 $(' .confirm_delete').click(function(){
   return confirm("<?php echo $this->lang->line('delete_confirm'); ?>");	
 });
 
 
 $("#fb_retention").keydown(function (e) {
   var code = parseInt(e.keyCode);
   if (code >= 96 && code <= 105 || code >= 48 && code <= 57 || code == 8) {
       return true;
   } else {
       return false;
   }
 });

 $(document).ready(function(){  
   
   if(group_id != ""){
     $("#group_name").val(group_name);
     $("#primary_db").val(primary_db);
     $("#primary_dest").val(primary_dest);
     $("#standby_db").val(standby_db);
     $("#standby_dest").val(standby_dest);
     $("#fb_retention").val(fb_retention);
     
     if(shift_vip == 1){
     		$("#shift_vip").attr("checked","checked");
     		
				$("#div_node_vips").show();
				$("#div_network_card_p").show();
				$("#div_network_card_s").show();
     		$("#node_vips").val(node_vips);
     		$("#network_card_p").val(network_card_p);
     		$("#network_card_s").val(network_card_s);
     }else{
				$("#div_node_vips").hide();
				$("#div_network_card_p").hide();
				$("#div_network_card_s").hide();
     }
     
   }
   else{
     $("#fb_retention").val("5");
		 $("#div_node_vips").hide();
		 $("#div_network_card_p").hide();
		 $("#div_network_card_s").hide();
     $("#primary_dest").val(2);
     $("#standby_dest").val(2);
   }
   

 });
 
 $("#shift_vip").change(function() { 
		//alert($("#is_vip_shift").is(':checked'));
		if($("#shift_vip").is(':checked') == true){
				$("#div_node_vips").show();
				$("#div_network_card_p").show();
				$("#div_network_card_s").show();
		}else{
				$("#div_node_vips").hide();
				$("#div_network_card_p").hide();
				$("#div_network_card_s").hide();
				$("#node_vips").val("");
				$("#network_card_p").val("");
				$("#network_card_s").val("");
		}
		
 });
 
 
 var target_url="<?php if($group_id==""){echo site_url('cfg_oracle/add_dg');}else{echo site_url('cfg_oracle/edit_dg');} ?>";
 var submit="<?php if($group_id==""){echo 'add_dg';}else{echo 'edit_dg';} ?>";
 
 function checkLicense(e){
 		var dg_count = parseInt("<?php echo $dg_count ?>");
 		var dg_license_quota = parseInt("<?php echo $dg_quota ?>");
 		var shift_vip = 0;
 		if($("#shift_vip").is(':checked') == true){
 				shift_vip = 1;
 		}
 		
 		
 		if($("#group_name").val() == ""){
				bootbox.alert({
		        		message: "请输入组名!",
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
				        	
		}else if(group_id=="" && dg_count >= dg_license_quota){
			bootbox.alert({
	        		message: "您已经超出了容灾授权限制，请删除后再添加!",
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
			        data: $("#form").serializeArray(),
			        data: {"submit":submit,"group_id":group_id,"group_name":$("#group_name").val(),"primary_db":$("#primary_db").val(),"primary_dest":$("#primary_dest").val(),"standby_db":$("#standby_db").val(),"standby_dest":$("#standby_dest").val(),"fb_retention":$("#fb_retention").val(),"shift_vip":shift_vip,"node_vips":$("#node_vips").val(),"network_card_p":$("#network_card_p").val(),"network_card_s":$("#network_card_s").val()},
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

