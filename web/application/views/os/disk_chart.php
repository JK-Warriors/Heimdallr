
<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_OS Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Chart'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">
<div class="btn-toolbar">
                <div class="btn-group">
                   <a class="btn btn-default <?php if($setval['begin_time']=='30') echo 'active'; ?>" href="<?php echo site_url('wl_os/disk_chart?host='). $setval['host'] . '&disk=' . $setval['disk']. '&begin_time=30';  ?>"><i class="fui-calendar-16"></i>&nbsp;30 <?php echo $this->lang->line('date_minutes'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='60') echo 'active'; ?>" href="<?php echo site_url('wl_os/disk_chart?host='). $setval['host'] . '&disk=' . $setval['disk']. '&begin_time=60';  ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='180') echo 'active'; ?>" href="<?php echo site_url('wl_os/disk_chart?host='). $setval['host'] . '&disk=' . $setval['disk']. '&begin_time=180';  ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='360') echo 'active'; ?>" href="<?php echo site_url('wl_os/disk_chart?host='). $setval['host'] . '&disk=' . $setval['disk']. '&begin_time=360';  ?>"><i class="fui-calendar-16"></i>&nbsp;6 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='720') echo 'active'; ?>" href="<?php echo site_url('wl_os/disk_chart?host='). $setval['host'] . '&disk=' . $setval['disk']. '&begin_time=720';  ?>"><i class="fui-calendar-16"></i>&nbsp;12 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='1440') echo 'active'; ?>" href="<?php echo site_url('wl_os/disk_chart?host='). $setval['host'] . '&disk=' . $setval['disk']. '&begin_time=1440';  ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='4320') echo 'active'; ?>" href="<?php echo site_url('wl_os/disk_chart?host='). $setval['host'] . '&disk=' . $setval['disk']. '&begin_time=4320';  ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='10080') echo 'active'; ?>" href="<?php echo site_url('wl_os/disk_chart?host='). $setval['host'] . '&disk=' . $setval['disk']. '&begin_time=10080';  ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_weeks'); ?></a>
                </div>
</div> <!-- /toolbar -->             
<hr/>
<div id="disk" style="margin-top:5px; margin-left:10px; width:96%; height:300px;"></div>


<script src="lib/echarts4/echarts.min.js"></script>
<script src="lib/echarts4/dark.js"></script>


<script type="text/javascript">

var url = "<?php echo site_url('wl_os/disk_data?host=') . $setval['host'] . '&disk=' . $setval['disk'] . '&begin_time=' . $setval['begin_time']; ?>";
var d_disk = document.getElementById("disk");
var c_disk = echarts.init(d_disk, 'infographic');
var option = null;

var colors = ['#5793f3', '#d14a61', '#675bba'];


$(document).ready(function(){  

		getChartSeriesData(url);
});


function getChartSeriesData(url){
    $.get(url, function(json){
    		//alert(json.server_id);
    		//alert(json.time);
    		//alert(json.delay);
        //var status = 1;

				option = {
				    title : {
				        text: "<?php echo $setval['host']; ?> <?php echo $setval['disk']; ?> <?php echo $this->lang->line('chart'); ?>",
				        x: 'center',
				        align: 'right'
				    },
				    color: colors,
				
				    toolbox: {
				        feature: {
				            restore: {},
				            saveAsImage: {}
				        }
				    },
				    tooltip: {
        				trigger: 'axis'
				    },
				    dataZoom: [
				        {
				            show: true,
				            realtime: true,
				            start: 80,
				            end: 100
				        },
				        {
				            type: 'inside',
				            realtime: true,
				            start: 80,
				            end: 100
				        }
				    ],
				    legend: {
				        data:['disk rate',''],
				        x: 'left'
				    },
				    grid: {
				        top: 70,
				        bottom: 50
				    },
				    xAxis: {
				        type: 'category',
				        axisTick: {
				            alignWithLabel: true
				        },
				        axisLine: {
				            onZero: false
				        },
				        axisPointer: {
				            label: {
				                formatter: function(params) {
				                    return params.value + ':';
				                }
				            }
				        },
				        data: json.time
				    },
				    yAxis: [{
				        type: 'value'
				    }],
				    series: [{
				        name: "disk rate",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.used_rate
				    }]
				};
				
				c_disk.setOption(option, true);
 },'json');  
    
}  


</script>
