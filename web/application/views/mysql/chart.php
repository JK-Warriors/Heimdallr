
        
		<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li><a href="<?php echo site_url('wl_mysql/index'); ?>"><?php echo $this->lang->line('_MySQL Monitor'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('chart'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">

<div class="btn-toolbar">
                <div class="btn-group">
                  <a class="btn btn-default <?php if($begin_time=='30') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/chart/'.$cur_server_id.'/30') ?>"><i class="fui-calendar-16"></i>&nbsp;30 <?php echo $this->lang->line('date_minutes'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='60') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/chart/'.$cur_server_id.'/60') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='180') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/chart/'.$cur_server_id.'/180') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='360') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/chart/'.$cur_server_id.'/360') ?>"><i class="fui-calendar-16"></i>&nbsp;6 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='720') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/chart/'.$cur_server_id.'/720') ?>"><i class="fui-calendar-16"></i>&nbsp;12 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='1440') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/chart/'.$cur_server_id.'/1440') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='4320') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/chart/'.$cur_server_id.'/4320') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='10080') echo 'active'; ?>" href="<?php echo site_url('wl_mysql/chart/'.$cur_server_id.'/10080') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_weeks'); ?></a>
                </div>
</div>
          
<hr/>
<div id="threads" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="qps_tps" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="dml" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="transaction" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="bytes" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="aborted" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>


<script src="lib/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="lib/echarts4/echarts.min.js"></script>
<script src="lib/echarts4/dark.js"></script>


<script type="text/javascript">
var url = "<?php echo site_url('wl_mysql/chart_data') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>";

var d_threads = document.getElementById("threads");
var c_threads = echarts.init(d_threads, 'infographic');

var d_qps_tps = document.getElementById("qps_tps");
var c_qps_tps = echarts.init(d_qps_tps, 'infographic');

var d_dml = document.getElementById("dml");
var c_dml = echarts.init(d_dml, 'infographic');

var d_transaction = document.getElementById("transaction");
var c_transaction = echarts.init(d_transaction, 'infographic');

var d_bytes = document.getElementById("bytes");
var c_bytes = echarts.init(d_bytes, 'infographic');

var d_aborted = document.getElementById("aborted");
var c_aborted = echarts.init(d_aborted, 'infographic');


var option = null;

var colors = ['#5793f3', '#d14a61', '#675bba', '#ffddaa'];


$(document).ready(function(){  

		getChartSeriesData(url);
});


function getChartSeriesData(url){
    $.get(url, function(json){
    		//alert(json.server_id);
    		//alert(json.time);
    		//alert(json.delay);

        //var status = 1;

				    
				//=========================threads=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> 线程图表",
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
				        data:['threads_running','threads_connected','threads_cached'],
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
				        name: "threads_running",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.threads_running
				    },{
				        name: "threads_connected",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.threads_connected
				    },{
				        name: "threads_cached",
				        type: 'line',
				        color: colors[2],
				        smooth: true,
				        data: json.threads_cached
				    }]
				};
				c_threads.setOption(option, true);

				//=========================QPS/TPS=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> QPS/TPS图表",
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
				        data:['QPS','TPS'],
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
				        name: "QPS",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.qps
				    },{
				        name: "TPS",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.tps
				    }]
				};
				c_qps_tps.setOption(option, true);


				//=========================DML=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> DML图表",
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
				        data:['total_select_persecond','total_insert_persecond','total_update_persecond','total_delete_persecond'],
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
				        name: "total_select_persecond",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.total_select_persecond
				    },{
				        name: "total_insert_persecond",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.total_insert_persecond
				    },{
				        name: "total_update_persecond",
				        type: 'line',
				        color: colors[2],
				        smooth: true,
				        data: json.total_update_persecond
				    },{
				        name: "total_delete_persecond",
				        type: 'line',
				        color: colors[3],
				        smooth: true,
				        data: json.total_delete_persecond
				    }]
				};
				c_dml.setOption(option, true);


				//=========================Transaction=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> 事务图表",
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
				        data:['commit_persecond','rollback_persecond'],
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
				        name: "commit_persecond",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.commit_persecond
				    },{
				        name: "rollback_persecond",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.rollback_persecond
				    }]
				};
				c_transaction.setOption(option, true);


				//=========================Network=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> 网络图表",
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
				        data:['bytes_received','bytes_sent'],
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
				        name: "bytes_received",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.bytes_received
				    },{
				        name: "bytes_sent",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.bytes_sent
				    }]
				};
				c_bytes.setOption(option, true);


				//=========================Aborted=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> Aborted图表",
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
				        data:['aborted_clients','aborted_connects'],
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
				        name: "aborted_clients",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.aborted_clients
				    },{
				        name: "aborted_connects",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.aborted_connects
				    }]
				};
				c_aborted.setOption(option, true);
				
				
				
    },'json');  
				
				
				
}  
</script>

