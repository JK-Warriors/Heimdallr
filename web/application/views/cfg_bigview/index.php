<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>


<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<ul class="breadcrumb">
          <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
          <li class="active"><?php echo $this->lang->line('_Servers Configure'); ?></li><span class="divider">/</span></li>
          <li class="active"><?php echo $this->lang->line('_BigView'); ?></li>
</ul>


<form id="bigview_form" name="bigview_form" class="form-horizontal" method="post" action="<?php echo site_url('cfg_bigview/save') ?>" >
 
<div style="height: 20px;"></div>
<h2>配置在大屏上显示的数据库</h2>
<hr>
<div id="db_alert" class="alert alert-failed"><span>选择的数据库不能超过3个！</span></div>

<div class="control-group success">
<table>
	<tr>
		<td style="vertical-align: top;">请选择数据库(双击)：<br/>
			<select id="leftSelect" name="leftSelect[]" multiple="multiple" style="width:240px;height:180px;">
			 <?php foreach ($unselect_db as $item):?>
       <option value="<?php echo $item['id'];?>:<?php echo $item['db_type'];?>" ><?php echo $item['host'];?>(<?php echo $item['tags'];?>)</option>
       <?php endforeach;?>
			</select>
		</td>

		<td style="text-align:center;width:60px;">
			<input id="add" class="button1" style="width:40px;" type="button" value=">">
			<br/>
			<input id="add_all" class="button1" style="width:40px;" type="button" value=">>" >
			<br/>
			<input id="remove" class="button1" style="width:40px;" type="button" value="<" >
			<br/>
			<input id="remove_all" class="button1" style="width:40px;" type="button" value="<<" >
		</td>
		<td>已选择的数据库：<br/>
			<input type="hidden" id="signNames" name="signNames" value=""/>
			<select id="rightSelect" name="rightSelect[]" multiple="multiple" style="width: 240px;height:180px;">
			 <?php foreach ($select_db as $item):?>
       <option value="<?php echo $item['id'];?>:<?php echo $item['type'];?>" ><?php echo $item['host'];?>(<?php echo $item['tags'];?>)</option>
       <?php endforeach;?>
			</select>
		</td>
		
		</td>
	</tr>

</table>

</div>

<div style="height: 20px;"></div>
<h2>选择核心数据库</h2>
<hr>
  <div class="control-group">
   <label class="control-label" for="">*核心数据库</label>
   <div class="controls">
     <select name="core_db" id="core_db" class="input-large"  >
       <option value=""></option>
       <?php foreach ($total_db as $item):?>
       <option value="<?php echo $item['id'];?>:<?php echo $item['db_type'];?>"><?php echo $item['host'];?>:<?php echo $item['port'];?>(<?php echo $item['tags'];?>)</option>
       <?php endforeach;?>
       </select>
       <span class="help-inline"></span>
   </div>
  </div>

<div style="height: 20px;"></div>
<h2>选择核心主机</h2>
<hr>
  <div class="control-group">
   <label class="control-label" for="">*核心主机</label>
   <div class="controls">
     <select name="core_os" id="core_os" class="input-large"  >
       <option value=""></option>
       <?php foreach ($total_os as $item):?>
       <option value="<?php echo $item['id'];?>" ><?php echo $item['host'];?>(<?php echo $item['tags'];?>)</option>
       <?php endforeach;?>
       </select>
       <span class="help-inline"></span>
   </div>
  </div>

<!-- <hr > -->
<div class="btn-toolbar">
  <button type="button" class="btn btn-primary" onclick="form_submit();"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>
  <!---<button type="submit" class="btn btn-primary"><i class="icon-save"></i> <?php echo $this->lang->line('save'); ?></button>--->
<div class="btn-group"></div>
</div>
                                  
</form>



<script type="text/javascript">
$(function(){
    //移到右边
    $('#add').click(function() {
    //获取选中的选项，删除并追加给对方
        $('#leftSelect option:selected').appendTo('#rightSelect');  //appendTo（）方法可以用来移动元素，移动元素时首先从文档上删除此元素，然后将该元素插入得到文档中的指定节点
    });
    //移到左边
    $('#remove').click(function() {
        $('#rightSelect option:selected').appendTo('#leftSelect');
    });
    //全部移到右边
    $('#add_all').click(function() {
        //获取全部的选项,删除并追加给对方
        $('#leftSelect option').appendTo('#rightSelect');
    });
    //全部移到左边
    $('#remove_all').click(function() {
        $('#rightSelect option').appendTo('#leftSelect');
    });
    //双击选项
    $('#leftSelect').dblclick(function(){ //绑定双击事件
        //获取全部的选项,删除并追加给对方
        $("option:selected",this).appendTo('#rightSelect'); //追加给对方
    });
    //双击选项
    $('#rightSelect').dblclick(function(){
       $("option:selected",this).appendTo('#leftSelect');
    });
 
});
</script>


<script type="text/javascript">
	var target_url = "<?php echo site_url('cfg_bigview/save') ?>";
	
	function form_submit(){
		var core_db = $("#core_db").val();
		var core_os = $("#core_os").val();
		var sel = $('#rightSelect').val();
		var rightSelect = document.getElementById("rightSelect");
		//alert(rightSelect.options.length);
		var center_db = [];
		for(i = 0; i < rightSelect.options.length; i++){
			center_db[i] = rightSelect.options[i].value;
		}
		
		for(i = 0; i < 3; i++){
			if(typeof(center_db[i]) == "undefined"){
				center_db[i] = "";
			};
			//alert(center_db[i]);
		}
		
		if(rightSelect.options.length > 3)
		{
			$("#db_alert").show();
		}
		else
		{
			$("#db_alert").hide();
			
			$.ajax({url: target_url,
						  data: $("#bigview_form").serializeArray(),
						  data: {"center_db1":center_db[0],"center_db2":center_db[1],"center_db3":center_db[2],"core_db":core_db,"core_os":core_os},
						  type: "POST",
						  async: false,
						  success: function (data) {
							//回调函数，判断提交返回的数据执行相应逻辑
						      if (data.Success) {
						      	//alert(1);
						      	window.location.reload();
						      }
						      else {
						      	//alert(data.center_db1);
						      }
						  }
						});
		                										
		                										
		}
	}
	
	jQuery(document).ready(function(){
		var core_db = "<?php echo $core_db[0]['server_id'];?>:<?php echo $core_db[0]['type'];?>";
		var core_os = "<?php echo $core_os[0]['server_id'];?>";
		//alert(core_db);
		$("#db_alert").hide();
		//alert(return_code);
		$("#core_db").val(core_db);
		$("#core_os").val(core_os);
	});  
</script>



