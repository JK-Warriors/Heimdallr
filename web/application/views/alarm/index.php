<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

     
<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Alarm Panel'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Alarm List'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">
 
<script src="lib/bootstrap/js/bootbox.js"></script>
<script language="javascript" src="./lib/DatePicker/WdatePicker.js"></script>
                    
<div class="ui-state-default ui-corner-all" style="height: 45px;" >
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-search"></span>                 
<form id="form_alert" name="form_alert" class="form-inline" method="get" action="" >
  <input type="text" id="host"  name="host" value="<?php echo $setval['host']; ?>" placeholder="<?php echo $this->lang->line('please_input_host'); ?>" class="input-medium" >
 	<input type="text" id="tags"  name="tags" value="<?php echo $setval['tags']; ?>" placeholder="<?php echo $this->lang->line('please_input_tags'); ?>" class="input-medium" >
  <select name="db_type" class="input-small" style="width: 120px;">
  <option value="" <?php if($setval['db_type']=='') echo "selected"; ?> ><?php echo $this->lang->line('type'); ?></option>
  <option value="oracle" <?php if($setval['db_type']=='oracle') echo "selected"; ?> ><?php echo $this->lang->line('oracle'); ?></option>
  <option value="mysql" <?php if($setval['db_type']=='mysql') echo "selected"; ?> ><?php echo $this->lang->line('mysql'); ?></option>
  <option value="sqlserver" <?php if($setval['db_type']=='sqlserver') echo "selected"; ?> ><?php echo $this->lang->line('sqlserver'); ?></option>
  <option value="sqlserver" <?php if($setval['db_type']=='os') echo "selected"; ?> ><?php echo $this->lang->line('os'); ?></option>
  </select>
  
  <select name="level" class="input-small" style="width: 100px;" >
  <option value=""><?php echo $this->lang->line('level'); ?></option>
  <option value="warning" <?php if($setval['level']=='warning') echo "selected"; ?> ><?php echo $this->lang->line('warning'); ?></option>
  <option value="critical" <?php if($setval['level']=='critical') echo "selected"; ?> ><?php echo $this->lang->line('critical'); ?></option>
  <option value="ok" <?php if($setval['level']=='ok') echo "selected"; ?> ><?php echo $this->lang->line('ok'); ?></option>
  </select>

  <button type="submit" class="btn btn-success"><i class="icon-search"></i> <?php echo $this->lang->line('search'); ?></button>
  <a href="<?php echo site_url('alarm/index') ?>" class="btn btn-warning"><i class="icon-repeat"></i> <?php echo $this->lang->line('reset'); ?></a>
  <button id="refresh" class="btn btn-info"><i class="icon-refresh"></i> <?php echo $this->lang->line('refresh'); ?></button>
  
</form>                
</div>


<div class="well">
    <table class="table table-hover table-condensed" style="font-size: 12px;">
      <thead>
      	<th><input type='checkbox' id='chkb_all' /></th>
        <th><?php echo $this->lang->line('host'); ?></th>
        <th><?php echo $this->lang->line('tags'); ?></th>
        <th><?php echo $this->lang->line('type'); ?></th>
        <th><?php echo $this->lang->line('item'); ?></th>
        <th><?php echo $this->lang->line('level'); ?></th>
        <th><?php echo $this->lang->line('message'); ?></th>
        <th><?php echo $this->lang->line('value'); ?></th>
        <th><?php echo $this->lang->line('monitor_time'); ?></th>
        <th><?php echo $this->lang->line('mail'); ?></th>
        <th><?php echo $this->lang->line('sms'); ?></th>
      </thead>
      <tbody>
<?php if(!empty($datalist)) {?>
 <?php foreach ($datalist  as $item):?>
    <tr class="warning">
      	<td><input type='checkbox' name='chkb' value='<?php echo $item['id'] ?>' /></td>
				<td><?php echo $item['host'].":".$item['port'] ?></td>
        <td><?php echo $item['tags'] ?></td>
        <td><?php echo $item['db_type'] ?></td>
        <td><?php echo $item['alert_item'] ?></td>
        <td><?php if($item['level']=='critical'){ ?> <span class="label label-important"><?php echo $this->lang->line('critical'); ?></span> <?php }else if($item['level']=='warning'){  ?><span class="label label-warning"><?php echo $this->lang->line('warning'); ?></span> <?php }else{?> <span class="label label-success"><?php echo $this->lang->line('ok'); ?></span>  <?php } ?></td>
        <td><?php echo $item['message'] ?></td>
        <td><span class="label label-info"><?php echo $item['alert_value']  ?></span></td>
        <td><?php echo $item['create_time'] ?></td>
        <td><?php echo check_mail($item['send_mail_status']) ?></td>
        <td><?php echo check_mail($item['send_sms_status']) ?></td>
 
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

<div class="" style="margin-top: 8px;padding: 8px;">
<center><?php echo $this->pagination->create_links(); ?></center>
</div>


<div class="controls" style="padding-left:15px;">
<button type="submit" class="btn btn-success" onclick="confirm_alert(this)" ><?php echo $this->lang->line('confirm_alert'); ?></button>
</div>

 <script type="text/javascript">
    $('#refresh').click(function(){
        document.location.reload(); 
    })
    
    
		$("#chkb_all").click(function(){
			if(this.checked){
			    $("[name=chkb]").prop("checked",true);}
			else{ 
					$("[name=chkb]").prop("checked",false);}
			});
			
			
		function confirm_alert(e){
				var alert_ids = ""
				$.each($('input:checkbox[name=chkb]:checked'),function(){
                alert_ids = alert_ids + $(this).val() + ",";
         });
         
        alert_ids=alert_ids.substring(0, alert_ids.lastIndexOf(','));  
        alert(alert_ids);
        var direct_url = "<?php echo site_url('alarm/index') ?>";

        bootbox.dialog({
			    message: "确定确认这些告警吗？",
			    buttons: {
			        ok: {
			            label: '确定',
			            className: 'btn-danger',
									callback: function(){
										$.ajax({
				                    url: direct_url,
				                    data: $("#form_alert").serializeArray(),
				                    data: {"alert_ids":alert_ids},
				                    type: "POST",
				                    async:false, 
				                    success: function (data) {
				              			//回调函数，判断提交返回的数据执行相应逻辑
                        				window.location.href = direct_url;
				                    }
      										});
                    }
			        },
			        cancel: {
			            label: '取消',
			            className: 'btn-default',
			            callback: function () {
                  }
			        }
			    }
				});
		}
 </script>