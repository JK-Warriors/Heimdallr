
        
		<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_MySQL Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_BigTable Analysis'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">
<div class="btn-toolbar">
                <div class="btn-group">
                  <a class="btn btn-default <?php if($begin_time=='30') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/bigtable_chart/'.$cur_server_id.'/'.$tab_name.'/30') ?>"><i class="fui-calendar-16"></i>&nbsp;30 <?php echo $this->lang->line('date_minutes'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='60') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/bigtable_chart/'.$cur_server_id.'/'.$tab_name.'/60') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='180') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/bigtable_chart/'.$cur_server_id.'/'.$tab_name.'/180') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='360') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/bigtable_chart/'.$cur_server_id.'/'.$tab_name.'/360') ?>"><i class="fui-calendar-16"></i>&nbsp;6 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='720') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/bigtable_chart/'.$cur_server_id.'/'.$tab_name.'/720') ?>"><i class="fui-calendar-16"></i>&nbsp;12 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='1440') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/bigtable_chart/'.$cur_server_id.'/'.$tab_name.'/1440') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='4320') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/bigtable_chart/'.$cur_server_id.'/'.$tab_name.'/4320') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='10080') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/bigtable_chart/'.$cur_server_id.'/'.$tab_name.'/10080') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_weeks'); ?></a>
                </div>
</div>
            
<hr/>
<div id="table_size" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>


<script type="text/javascript" src="./lib/jqplot/jquery.jqplot.min.js"></script>
<script src="lib/echarts4/echarts.min.js"></script>
<script src="lib/echarts4/dark.js"></script>


<script type="text/javascript">
var url = "<?php echo site_url('wl_mysql/bigtable_chart_data') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5); ?>";

var d_table_size = document.getElementById("table_size");
var c_table_size = echarts.init(d_table_size, 'infographic');


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
				        text: "<?php echo $cur_server; ?> 大表空间趋势 (MB)",
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
				        data:['table_size'],
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
				        name: "table_size",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.table_size
				    }]
				};
				c_table_size.setOption(option, true);
				
				
				
    },'json');  
				
				
				
}  
</script>