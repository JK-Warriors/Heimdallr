
        
		<ul class="breadcrumb">
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('home'); ?></a> <span class="divider">/</span></li>
            <li><a href="<?php echo site_url(); ?>"><?php echo $this->lang->line('_Oracle'); ?> <?php echo $this->lang->line('_Health Monitor'); ?></a></li><span class="divider">/</span></li>
            <li class="active"><?php echo $this->lang->line('chart'); ?></li>
</ul>

<div class="container-fluid">
<div class="row-fluid">

<div class="btn-toolbar">
                <div class="btn-group">
                  <a class="btn btn-default <?php if($begin_time=='30') echo 'active'; ?>" href="<?php echo site_url('wl_oracle/chart/'.$cur_server_id.'/30') ?>"><i class="fui-calendar-16"></i>&nbsp;30 <?php echo $this->lang->line('date_minutes'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='60') echo 'active'; ?>" href="<?php echo site_url('wl_oracle/chart/'.$cur_server_id.'/60') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='180') echo 'active'; ?>" href="<?php echo site_url('wl_oracle/chart/'.$cur_server_id.'/180') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='360') echo 'active'; ?>" href="<?php echo site_url('wl_oracle/chart/'.$cur_server_id.'/360') ?>"><i class="fui-calendar-16"></i>&nbsp;6 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='720') echo 'active'; ?>" href="<?php echo site_url('wl_oracle/chart/'.$cur_server_id.'/720') ?>"><i class="fui-calendar-16"></i>&nbsp;12 <?php echo $this->lang->line('date_hours'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='1440') echo 'active'; ?>" href="<?php echo site_url('wl_oracle/chart/'.$cur_server_id.'/1440') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='4320') echo 'active'; ?>" href="<?php echo site_url('wl_oracle/chart/'.$cur_server_id.'/4320') ?>"><i class="fui-calendar-16"></i>&nbsp;3 <?php echo $this->lang->line('date_days'); ?></a>
                  <a class="btn btn-default <?php if($begin_time=='10080') echo 'active'; ?>" href="<?php echo site_url('wl_oracle/chart/'.$cur_server_id.'/10080') ?>"><i class="fui-calendar-16"></i>&nbsp;1 <?php echo $this->lang->line('date_weeks'); ?></a>
                </div>
</div> <!-- /toolbar -->             
<hr/>
<div id="sessions" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="actives" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="logical_reads" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="physical_wr" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="io_requests" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="db_block_changes" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="os_cpu_wait_time" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="opened_cursors" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>
<div id="transactions" style="margin-top:10px; margin-left:0px; width:96%; height:300px;"></div>

<script src="lib/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="lib/echarts4/echarts.min.js"></script>
<script src="lib/echarts4/dark.js"></script>



<script type="text/javascript">
var url = "<?php echo site_url('wl_oracle/chart_data') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>";

var d_sessions = document.getElementById("sessions");
var c_sessions = echarts.init(d_sessions, 'infographic');

var d_actives = document.getElementById("actives");
var c_actives = echarts.init(d_actives, 'infographic');

var d_logical_reads = document.getElementById("logical_reads");
var c_logical_reads = echarts.init(d_logical_reads, 'infographic');

var d_physical_wr = document.getElementById("physical_wr");
var c_physical_wr = echarts.init(d_physical_wr, 'infographic');

var d_io_requests = document.getElementById("io_requests");
var c_io_requests = echarts.init(d_io_requests, 'infographic');

var d_db_block_changes = document.getElementById("db_block_changes");
var c_db_block_changes = echarts.init(d_db_block_changes, 'infographic');

var d_os_cpu_wait_time = document.getElementById("os_cpu_wait_time");
var c_os_cpu_wait_time = echarts.init(d_os_cpu_wait_time, 'infographic');

var d_opened_cursors = document.getElementById("opened_cursors");
var c_opened_cursors = echarts.init(d_opened_cursors, 'infographic');

var d_transactions = document.getElementById("transactions");
var c_transactions = echarts.init(d_transactions, 'infographic');


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
				        text: "<?php echo $cur_server; ?> session_total <?php echo $this->lang->line('chart'); ?>",
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
				        data:['sessions',''],
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
				        name: "sessions",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.session_total
				    }]
				};
				
				c_sessions.setOption(option, true);
    
    
				//=========================Actives/Waits=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> actives/waits <?php echo $this->lang->line('chart'); ?>",
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
				        data:['actives','waits'],
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
				        name: "actives",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.session_actives
				    },{
				        name: "waits",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.session_waits
				    }]
				};
				c_actives.setOption(option, true);
				
				
				//=========================session_logical_reads=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> logical reads <?php echo $this->lang->line('chart'); ?>",
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
				        data:['logical reads',''],
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
				        name: "logical reads",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.logical_reads
				    }]
				};
				c_logical_reads.setOption(option, true);
				
				
				//=========================physical_wr=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> physical reads/writes <?php echo $this->lang->line('chart'); ?>",
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
				        data:['physical reads','physical writes'],
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
				        name: "physical reads",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.physical_reads
				    },{
				        name: "physical writes",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.physical_writes
				    }]
				};
				c_physical_wr.setOption(option, true);
				
				
				//=========================physical_io_requests=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> physical io requests <?php echo $this->lang->line('chart'); ?>",
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
				        data:['read io requests','write io requests'],
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
				        name: "read io requests",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.read_io
				    },{
				        name: "write io requests",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.write_io
				    }]
				};
				c_io_requests.setOption(option, true);
				
				
				//=========================db_block_changes=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> db block changes <?php echo $this->lang->line('chart'); ?>",
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
				        data:['db block changes',''],
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
				        name: "db block changes",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.db_block_changes
				    }]
				};
				c_db_block_changes.setOption(option, true);
				
				
				//=========================os_cpu_wait_time=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> os cpu wait time <?php echo $this->lang->line('chart'); ?>",
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
				        data:['os cpu wait time',''],
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
				        name: "os cpu wait time",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.os_cpu_wait_time
				    }]
				};
				c_os_cpu_wait_time.setOption(option, true);
				
				
				//=========================opened_cursors=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> opened cursors <?php echo $this->lang->line('chart'); ?>",
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
				        data:['opened cursors',''],
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
				        name: "opened cursors",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.opened_cursors
				    }]
				};
				c_opened_cursors.setOption(option, true);
				
				
				//=========================transactions=========================================//
    		option = {
				    title : {
				        text: "<?php echo $cur_server; ?> transactions <?php echo $this->lang->line('chart'); ?>",
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
				        data:['user commits','user rollbacks'],
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
				        name: "user commits",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.user_commits
				    },{
				        name: "user rollbacks",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.user_rollbacks
				    }]
				};
				c_transactions.setOption(option, true);
				
    },'json');  
    
    
    
    
}  
</script>


