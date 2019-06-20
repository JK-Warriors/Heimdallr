
        
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_MySQL Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Key Cache Monitor'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">

<div class="btn-toolbar">
                <div class="btn-group">
                  <a class="btn btn-default <?php if($begin_time=='30') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/key_cache_chart/'.$cur_server_id.'/30') ?>"><i class="fui-calendar-16"></i>&nbsp;30 <?php echo $this->lang->line('date_minutes'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='60') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/key_cache_chart/'.$cur_server_id.'/60') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='180') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/key_cache_chart/'.$cur_server_id.'/180') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='360') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/key_cache_chart/'.$cur_server_id.'/360') ?>"><i class="fui-calendar-16"></i>&nbsp;6 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='720') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/key_cache_chart/'.$cur_server_id.'/720') ?>"><i class="fui-calendar-16"></i>&nbsp;12 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='1440') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/key_cache_chart/'.$cur_server_id.'/1440') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='4320') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/key_cache_chart/'.$cur_server_id.'/4320') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='10080') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/key_cache_chart/'.$cur_server_id.'/10080') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_weeks'); ?></a>
                </div>
</div>            
<hr/>
<div id="key_cache" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>



<script src="lib/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="lib/echarts4/echarts.min.js"></script>
<script src="lib/echarts4/dark.js"></script>

<script type="text/javascript">
var url = "<?php echo site_url('wl_mysql/chart_data') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>";

var d_key_cache = document.getElementById("key_cache");
var c_key_cache = echarts.init(d_key_cache, 'infographic');


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

				    
				//=========================key_cache=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> 键缓存图表",
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
				        data:['key_buffer_read_rate','key_buffer_write_rate','key_blocks_used_rate'],
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
				            onZero: false,
				            lineStyle: {
				            }
				        },
				        axisPointer: {
				            label: {
				                formatter: function(params) {
				                    return params.value;
				                }
				            }
				        },
				        data: json.time
				    },
				    yAxis: [{
				        type: 'value'
				    }],
				    series: [{
				        name: "key_buffer_read_rate",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.key_buffer_read_rate
				    },{
				        name: "key_buffer_write_rate",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.key_buffer_write_rate
				    },{
				        name: "key_blocks_used_rate",
				        type: 'line',
				        color: colors[2],
				        smooth: true,
				        data: json.key_blocks_used_rate
				    }]
				};
				c_key_cache.setOption(option, true);
				
				
				
    },'json');  
				
				
				
}  
</script>