
        
		<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_OS Monitor'); ?></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('_Chart'); ?></li>
            
</ul>

<div class="container-fluid">
<div class="row-fluid">

<div class="btn-toolbar">
                <div class="btn-group">
                   <a class="btn btn-default <?php if($setval['begin_time']=='30') echo 'active'; ?>" href="<?php echo site_url('wl_os/chart?host='. $setval['host'] . '&begin_time=30') ?>"><i class="fui-calendar-16"></i>&nbsp;30 <?php echo $this->lang->line('date_minutes'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='60') echo 'active'; ?>" href="<?php echo site_url('wl_os/chart?host='.$setval['host'] . '&begin_time=60') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='180') echo 'active'; ?>" href="<?php echo site_url('wl_os/chart?host='.$setval['host'] . '&begin_time=180') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='360') echo 'active'; ?>" href="<?php echo site_url('wl_os/chart?host='.$setval['host'] . '&begin_time=360') ?>"><i class="fui-calendar-16"></i>&nbsp;6 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='720') echo 'active'; ?>" href="<?php echo site_url('wl_os/chart?host='.$setval['host'] . '&begin_time=720') ?>"><i class="fui-calendar-16"></i>&nbsp;12 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='1440') echo 'active'; ?>" href="<?php echo site_url('wl_os/chart?host='.$setval['host'] . '&begin_time=1440') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='4320') echo 'active'; ?>" href="<?php echo site_url('wl_os/chart?host='.$setval['host'] . '&begin_time=4320') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($setval['begin_time']=='10080') echo 'active'; ?>" href="<?php echo site_url('wl_os/chart?host='.$setval['host'] . '&begin_time=10080') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_weeks'); ?></a>
                </div>
</div> <!-- /toolbar -->             
<hr/>
<div id="cpu_load" style="margin-top:5px; margin-left:10px; width:96%; height:300px; <?php if(strpos($setval['kernel'],'Windows')!==false) echo 'display:none'; ?>" ></div>
<div id="cpu_utilization" style="margin-top:5px; margin-left:10px; width:96%; height:300px;"></div>
<div id="memory" style="margin-top:5px; margin-left:10px; width:96%; height:300px;"></div>
<div id="swap" style="margin-top:5px; margin-left:10px; width:96%; height:300px; <?php if(strpos($setval['kernel'],'Windows')!==false) echo 'display:none'; ?>"></div>
<div id="process" style="margin-top:5px; margin-left:10px; width:96%; height:300px; "></div>
<div id="network" style="margin-top:5px; margin-left:10px; width:96%; height:300px; "></div>
<div id="diskio" style="margin-top:5px; margin-left:10px; width:96%; height:300px; "></div>


<script src="lib/echarts4/echarts.min.js"></script>
<script src="lib/echarts4/dark.js"></script>


<script type="text/javascript">
var url = "<?php echo site_url('wl_os/chart_data?host=') . $setval['host'] . '&begin_time=' . $setval['begin_time']; ?>";
var kernel = "<?php echo $setval['kernel']; ?>";
var d_cpu_load = document.getElementById("cpu_load");
var c_cpu_load = echarts.init(d_cpu_load, 'infographic');

var d_cpu_utilization = document.getElementById("cpu_utilization");
var c_cpu_utilization = echarts.init(d_cpu_utilization, 'infographic');

var d_memory = document.getElementById("memory");
var c_memory = echarts.init(d_memory, 'infographic');

var d_swap = document.getElementById("swap");
var c_swap = echarts.init(d_swap, 'infographic');

var d_process = document.getElementById("process");
var c_process = echarts.init(d_process, 'infographic');

var d_network = document.getElementById("network");
var c_network = echarts.init(d_network, 'infographic');

var d_diskio = document.getElementById("diskio");
var c_diskio = echarts.init(d_diskio, 'infographic');

var option = null;
var option_1 = null;

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

				//=========================System Load=========================================//
				option = {
				    title : {
				        text: "<?php echo $setval['host']; ?> Load <?php echo $this->lang->line('chart'); ?>",
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
				        data:['load_1','load_5','load_15'],
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
				        name: "load_1",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.load_1
				    },{
				        name: "load_5",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.load_5
				    },{
				        name: "load_15",
				        type: 'line',
				        color: colors[2],
				        smooth: true,
				        data: json.load_15
				    }]
				};
				
				c_cpu_load.setOption(option, true);
				
				
				//=========================CPU =========================================//
				option = {
				    title : {
				        text: "<?php echo $setval['host']; ?> CPU <?php echo $this->lang->line('chart'); ?>",
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
				        data:['user time','system time','idle time'],
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
				        name: "user time",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.cpu_user_time
				    },{
				        name: "system time",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.cpu_system_time
				    },{
				        name: "idle time",
				        type: 'line',
				        color: colors[2],
				        smooth: true,
				        data: json.cpu_idle_time
				    }]
				};
				
				//=========================CPU windows=========================================//
				option_1 = {
				    title : {
				        text: "<?php echo $setval['host']; ?> CPU <?php echo $this->lang->line('chart'); ?>",
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
				        data:['idle time'],
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
				        name: "idle time",
				        type: 'line',
				        color: colors[2],
				        smooth: true,
				        data: json.cpu_idle_time
				    }]
				};
				
				//alert(kernel);
				if(kernel.indexOf("Windows") != -1){
						c_cpu_utilization.setOption(option_1, true);
				}
				else{
						c_cpu_utilization.setOption(option, true);
				}
				
				
				
				
				//========================= Memory =========================================//
				option = {
				    title : {
				        text: "<?php echo $setval['host']; ?> Memory <?php echo $this->lang->line('chart'); ?>",
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
				        data:['mem usage rate',''],
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
				        name: "mem usage rate",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.mem_usage_rate
				    }]
				};
				
				c_memory.setOption(option, true);
				
				
				
				
				
				//========================= Swap =========================================//
				option = {
				    title : {
				        text: "<?php echo $setval['host']; ?> Swap <?php echo $this->lang->line('chart'); ?>",
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
				        data:['swap avail rate',''],
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
				        name: "swap avail rate",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.swap_avail_rate
				    }]
				};
				
				c_swap.setOption(option, true);
				
				
				//========================= Process =========================================//
				option = {
				    title : {
				        text: "<?php echo $setval['host']; ?> Process <?php echo $this->lang->line('chart'); ?>",
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
				        data:['process',''],
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
				        name: "process",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.process
				    }]
				};
				
				c_process.setOption(option, true);
				
				
				
				//========================= Network =========================================//
				option = {
				    title : {
				        text: "<?php echo $setval['host']; ?> Network <?php echo $this->lang->line('chart'); ?>",
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
				        data:['net in bytes','net out bytes'],
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
				        name: "net in bytes",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.net_in_bytes
				    },{
				        name: "net out bytes",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.net_out_bytes
				    }]
				};
				
				c_network.setOption(option, true);
				
				
				
				//========================= Disk IO =========================================//
				option = {
				    title : {
				        text: "<?php echo $setval['host']; ?> Disk IO <?php echo $this->lang->line('chart'); ?>",
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
				        data:['io reads','io writes'],
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
				        name: "io reads",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.disk_io_reads
				    },{
				        name: "io writes",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.disk_io_writes
				    }]
				};
				
				c_diskio.setOption(option, true);
 },'json');  
    
}  



</script>


