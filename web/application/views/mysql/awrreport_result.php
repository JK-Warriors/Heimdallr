<html lang="en">
<head>
<meta charset="utf-8">
<title>Mysql_AWR_Report</title>

<base href="<?php echo base_url().'application/views/static/'; ?>" />
<script src="lib/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="lib/echarts4/echarts.min.js"></script>
<script src="lib/echarts4/dark.js"></script>
<style type="text/css">
/* roScripts
Table Design by Mihalcea Romeo
www.roscripts.com
----------------------------------------------- */
table { border-collapse:collapse;
		background:#EFF4FB;
		border-left:1px solid #686868;
		border-right:1px solid #686868;
		font:0.8em/145% "Trebuchet MS",helvetica,arial,verdana;
		color: #333;}
td, th {padding:1px;}
caption {padding: 0 0 .5em 0;
		text-align: left;
		font-size: 1.4em;
		font-weight: bold;
		text-transform: uppercase;
		color: #333;
		background: transparent;}
/* =links----------------------------------------------- */
table a {color:#950000;	text-decoration:none;}
table a:link {}
table a:visited {font-weight:normal;color:#666;text-decoration: line-through;}
table a:hover {	border-bottom: 1px dashed #bbb;}
/* =head =foot----------------------------------------------- */
thead th, tfoot th, tfoot td {background:#333 ;color:#fff}
tfoot td {		text-align:right}
/* =body----------------------------------------------- */
tbody th, tbody td {border-bottom: dotted 1px #333;}
tbody th {white-space: nowrap;}
tbody th a {color:#333;}
.odd {}
tbody tr:hover {background:#fafafa}
a {color:#950000;	text-decoration:none;}
</style></head><body>
<h1 >
MySQL Online AWR Report
</h1>
<hr />
<a href="<?php echo site_url('wl_mysql/awrreport_create'); ?>#t_server">Server |</a> 
<a href="<?php echo site_url('wl_mysql/awrreport_create'); ?>#t_resource">Resource |</a> 
<a href="<?php echo site_url('wl_mysql/awrreport_create'); ?>#t_threads">Threads |</a>
<a href="<?php echo site_url('wl_mysql/awrreport_create'); ?>#t_aborted">Aborted |</a>
<a href="<?php echo site_url('wl_mysql/awrreport_create'); ?>#t_queries">Queries |</a> 
<a href="<?php echo site_url('wl_mysql/awrreport_create'); ?>#t_cpu">Cpu |</a>
<a href="<?php echo site_url('wl_mysql/awrreport_create'); ?>#t_slowsql">SlowSQL |</a>   
<hr />
<p id="t_server"><h3>Server</h3> </p>
<p>
<table border="1"  width="600">
<tr>
<th>tags</th>
<th>host</th>
<th>port</th>
<th>role</th>
<th>version</th>
<th>uptime</th>
</tr>
<tr>
<td align="right"><?php echo $mysql_info['tags']; ?> </td>
<td align="right"><?php echo $mysql_info['host']; ?> </td>
<td align="right"><?php echo $mysql_info['port']; ?> </td>
<td align="right"><?php echo $mysql_info['role']; ?> </td>
<td align="right"><?php echo $mysql_info['version']; ?> </td>
<td align="right"><?php echo check_uptime($mysql_info['uptime']); ?> </td>
</tr>
</table>
</p>

<hr />

<p id="t_resource"><h3>Resource</h3>   </p>
<div id="connections_usage" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div id="files_usage" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div id="tables_usage" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div style="clear:both;"></div>
<hr />

<p id="t_threads"><h3>Threads</h3>   </p>
<div id="threads_running" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div id="threads" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div id="connections" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div style="clear:both;"></div>
<hr />

<p id="t_aborted"><h3>Aborted</h3>   </p>
<div id="aborted_clients" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div id="aborted_connects" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div style="clear:both;"></div>
<hr />

<p id="t_queries"><h3>Queries</h3>   </p>
<div id="dml" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div id="queries" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div id="transaction" style="margin-top:15px; margin-left:20px; width:350px; height:250px; float:left;"></div>
<div style="clear:both;"></div>
<hr />
<!---
<p id="t_cpu"><h3>Host CPU</h3> </p>
<div id="cpu_load" style="margin-top:5px; margin-left:10px; width:350px; height:220px; float:left;"></div>
<div id="cpu_utilization" style="margin-top:5px; margin-left:10px; width:350px; height:220px;float:left;"></div>
<div id="process" style="margin-top:5px; margin-left:10px; width:350px; height:220px;float:left; "></div>
<div style="clear:both;"></div>
-->
<hr />

<p><h3>Top10 SlowQuery SQL</h3> </p>
<table border="1" width="1200" style="font-size: 12px;">
        <tr>
         <td>checksum</td>
        <td>fringerprint </td>
        <td>database</td>
        <td>user</td>
        <td>last_seen</td>
        <td>ts_cnt</td>
        <td>query_time_sum</td>
		<td>query_time_min</td>
        <td>query_time_max</td>
        <td>lock_time_sum</td>
        <td>lock_time_min</td>
		<td>lock_time_max</td>
	   </tr>
      <tbody>
 
  <?php if(!empty($top10_slowQuery)) {?>
 <?php foreach ($top10_slowQuery  as $item):?>
    <tr>
        <td><a href="<?php echo site_url("wl_mysql/awrreport_create#".$item['checksum']) ?>"   title="<?php echo $this->lang->line('view_detail'); ?>"><?php  echo $item['checksum'] ?></a></td>
        <td> <?php echo substring($item['fingerprint'],0,35); ?> </td>
        <td><?php echo $item['db_max'] ?></td>
        <td><?php echo $item['user_max'] ?></td>
        <td><?php echo $item['last_seen'] ?></td>
        <td><?php echo $item['ts_cnt'] ?></td>
        <td><?php echo $item['Query_time_sum'] ?></td>
        <td><?php echo $item['Query_time_min'] ?></td>
        <td><?php echo $item['Query_time_max'] ?></td>
        <td><?php echo $item['Lock_time_sum'] ?></td>
        <td><?php echo $item['Lock_time_min'] ?></td>
        <td><?php echo $item['Lock_time_max'] ?></td>
	</tr>
 <?php endforeach;?>
<?php } ?>
</tbody>
    </table>
<hr />

<p id="t_slowsql"><h3>Top10 SlowQuery SQL Detail</h3> </p>
 
  <?php if(!empty($top10_slowQuery)) {?>
 <?php foreach ($top10_slowQuery  as $record):?>
 <a name="<?php echo $record['checksum']; ?>"></a>
 <p>
    <table  border="1" width="1200" style="font-size: 12px;">
    <tr>
        <th>database</th>
        <td colspan="2"><?php echo $record['db_max']; ?></td>
        <th>user</th>	
        <td colspan="3"><?php echo $record['user_max']; ?></td>
	</tr>
    <tr>
        <th ><?php echo $this->lang->line('checksum'); ?></th>
        <td colspan="2"><?php echo $record['checksum']; ?></td>
        <th><?php echo $this->lang->line('ts_cnt'); ?></th>	
        <td colspan="3"><?php echo $record['ts_cnt']; ?></td>
	</tr>
    <tr>
        <th><?php echo $this->lang->line('first_seen'); ?></th>
        <td colspan="2"><?php echo $record['first_seen']; ?></td>
        <th><?php echo $this->lang->line('last_seen'); ?></th>
        <td colspan="3"><?php echo $record['last_seen']; ?></td>
	</tr>
    <tr>
        <th><?php echo $this->lang->line('fingerprint'); ?></th>
        <td colspan="6"><?php echo $record['fingerprint']; ?></td>	
	</tr>
    <tr>
        <th><?php echo $this->lang->line('sample'); ?></th>
        <td colspan="6"><?php echo $record['sample']; ?></td>
	</tr>
    <tr>
        <th rowspan="2"><?php echo $this->lang->line('query_time'); ?></th>
        <th>Query_time_sum</th>
        <th>Query_time_min</th>
        <th>Query_time_max</th>
        <th>Query_time_pct_95</th>
        <th>Query_time_stddev</th>
        <th>Query_time_median</th>
	</tr>
    <tr>
        <td><?php echo $record['Query_time_sum']; ?></td>
        <td><?php echo $record['Query_time_min']; ?></td>
        <td><?php echo $record['Query_time_max']; ?></td>
        <td><?php echo $record['Query_time_pct_95']; ?></td>
        <td><?php echo $record['Query_time_stddev']; ?></td>
        <td><?php echo $record['Query_time_median']; ?></td>
	</tr>
    <tr>
        <th rowspan="2"><?php echo $this->lang->line('lock_time'); ?></th>
        <th>Lock_time_sum</th>
        <th>Lock_time_min</th>
        <th>Lock_time_max</th>
        <th>Lock_time_pct_95</th>
        <th>Lock_time_stddev</th>
        <th>Lock_time_median</th>
	</tr>
    <tr>
        <td><?php echo $record['Lock_time_sum']; ?></td>
        <td><?php echo $record['Lock_time_min']; ?></td>
        <td><?php echo $record['Lock_time_max']; ?></td>
        <td><?php echo $record['Lock_time_pct_95']; ?></td>
        <td><?php echo $record['Lock_time_stddev']; ?></td>
        <td><?php echo $record['Lock_time_median']; ?></td>
	</tr>
    <tr>
        <th rowspan="2"><?php echo $this->lang->line('rows_sent'); ?></th>
        <th>Rows_sent_sum</th>
        <th>Rows_sent_min</th>
        <th>Rows_sent_max</th>
        <th>Rows_sent_pct_95</th>
        <th>Rows_sent_stddev</th>
        <th>Rows_sent_median</th>
	</tr>
    <tr>
        <td><?php echo $record['Rows_sent_sum']; ?></td>
        <td><?php echo $record['Rows_sent_min']; ?></td>
        <td><?php echo $record['Rows_sent_max']; ?></td>
        <td><?php echo $record['Rows_sent_pct_95']; ?></td>
        <td><?php echo $record['Rows_sent_stddev']; ?></td>
        <td><?php echo $record['Rows_sent_median']; ?></td>
	</tr>
    <tr>
        <th rowspan="2"><?php echo $this->lang->line('rows_examined'); ?></th>
        <th>Rows_examined_sum</th>
        <th>Rows_examined_min</th>
        <th>Rows_examined_max</th>
        <th>Rows_examined_pct_95</th>
        <th>Rows_examined_stddev</th>
        <th>Rows_examined_median</th>
	</tr>
    <tr>
        <td><?php echo $record['Rows_examined_sum']; ?></td>
        <td><?php echo $record['Rows_examined_min']; ?></td>
        <td><?php echo $record['Rows_examined_max']; ?></td>
        <td><?php echo $record['Rows_examined_pct_95']; ?></td>
        <td><?php echo $record['Rows_examined_stddev']; ?></td>
        <td><?php echo $record['Rows_examined_median']; ?></td>
	</tr>
	 
</table>
</p>
 <?php endforeach;?>
<?php } ?>

	



<script type="text/javascript">

var url = "<?php echo site_url('wl_mysql/awr_chart_data') . '/' . $server_id . '/' . $begin_timestamp . '/' . $end_timestamp; ?>";
//alert(url);

var d_conn_usage = document.getElementById("connections_usage");
var c_conn_usage = echarts.init(d_conn_usage, 'infographic');

var d_tables = document.getElementById("tables_usage");
var c_tables = echarts.init(d_tables, 'infographic');

var d_files = document.getElementById("files_usage");
var c_files = echarts.init(d_files, 'infographic');

var d_threads_running = document.getElementById("threads_running");
var c_threads_running = echarts.init(d_threads_running, 'infographic');

var d_threads = document.getElementById("threads");
var c_threads = echarts.init(d_threads, 'infographic');

var d_connections = document.getElementById("connections");
var c_connections = echarts.init(d_connections, 'infographic');

var d_aborted_clients = document.getElementById("aborted_clients");
var c_aborted_clients = echarts.init(d_aborted_clients, 'infographic');

var d_aborted_connects = document.getElementById("aborted_connects");
var c_aborted_connects = echarts.init(d_aborted_connects, 'infographic');

var d_dml = document.getElementById("dml");
var c_dml = echarts.init(d_dml, 'infographic');

var d_queries = document.getElementById("queries");
var c_queries = echarts.init(d_queries, 'infographic');

var d_transaction = document.getElementById("transaction");
var c_transaction = echarts.init(d_transaction, 'infographic');

/*
var d_cpu_load = document.getElementById("cpu_load");
var c_cpu_load = echarts.init(d_cpu_load, 'infographic');

var d_cpu_utilization = document.getElementById("cpu_utilization");
var c_cpu_utilization = echarts.init(d_cpu_utilization, 'infographic');

var d_process = document.getElementById("process");
var c_process = echarts.init(d_process, 'infographic');
*/

var option = null;

var colors = ['#5793f3', '#d14a61', '#675bba', '#ff5800'];
//[ "#ff5800", "#EAA228", "#4bb2c5", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc"]


$(document).ready(function(){  

		getChartSeriesData(url);
});


function getChartSeriesData(url){
    $.get(url, function(json){
    		//alert(json.server_id);
    		//alert(json.time);
    		//alert(json.delay);

        //var status = 1;

				    
				//=========================Connection Pool Usage=========================================//
    		option = {
				    title : {
				        text: "Connection Pool Usage",
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
				        data:['max_connections','threads_connected'],
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
				        name: "max_connections",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.max_connections
				    },{
				        name: "threads_connected",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.threads_connected
				    }]
				};
				c_conn_usage.setOption(option, true);
				
				
				//=========================files=========================================//
    		option = {
				    title : {
				        text: "File Limit Usage",
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
				        data:['open_files_limit','open_files_used'],
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
				        name: "open_files_limit",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.open_files_limit
				    },{
				        name: "open_files_used",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.open_files_used
				    }]
				};
				c_files.setOption(option, true);			
				
				//=========================tables=========================================//
    		option = {
				    title : {
				        text: "Table Cache Usage",
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
				        data:['table_open_cache','open_tables_used'],
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
				        name: "table_open_cache",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.table_open_cache
				    },{
				        name: "open_tables_used",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.open_tables_used
				    }]
				};
				c_tables.setOption(option, true);
					
				
				
		
				//Threads
				//=========================Threads Running=========================================//
    		option = {
				    title : {
				        text: "Threads Running",
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
				        data:['threads_running'],
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
				    }]
				};
				c_threads_running.setOption(option, true);
		
				//=========================Threads=========================================//
    		option = {
				    title : {
				        text: "Threads",
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
				        data:['threads_connected','threads_created','threads_cached'],
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
				        name: "threads_connected",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.threads_connected
				    },{
				        name: "threads_created",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.threads_created
				    },{
				        name: "threads_cached",
				        type: 'line',
				        color: colors[2],
				        smooth: true,
				        data: json.threads_cached
				    }]
				};
				c_threads.setOption(option, true);	
		
				
				//=========================Connection Persecond=========================================//
				option = {
				    title : {
				        text: "Connection Persecond",
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
				        data:['connections_persecond'],
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
				        name: "connections_persecond",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.connections_persecond
				    }]
				};
				c_connections.setOption(option, true);	
				

				//Aborted	
				//=========================aborted_clients=========================================//
    		option = {
				    title : {
				        text: "Aborted Clients",
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
				        data:['aborted_clients'],
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
				    }]
				};
				c_aborted_clients.setOption(option, true);
		
				//=========================aborted_connects=========================================//
    		option = {
				    title : {
				        text: "Aborted Connects",
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
				        data:['aborted_connects'],
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
				        name: "aborted_connects",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.aborted_connects
				    }]
				};
				c_aborted_connects.setOption(option, true);
				
				
				//Queries
				//=========================DML Persecond=========================================//	
    		option = {
				    title : {
				        text: "DML Persecond",
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
		
				//=========================Queries Persecond=========================================//
    		option = {
				    title : {
				        text: "Queries Persecond",
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
				        data:['queries_persecond','questions_persecond'],
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
				        name: "queries_persecond",
				        type: 'line',
				        color: colors[0],
				        smooth: true,
				        data: json.queries_persecond
				    },{
				        name: "questions_persecond",
				        type: 'line',
				        color: colors[1],
				        smooth: true,
				        data: json.questions_persecond
				    }]
				};
				c_queries.setOption(option, true);	
		
				
				//=========================Transaction Persecond=========================================//
				option = {
				    title : {
				        text: "Transaction Persecond",
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
					
		
		
				//Host CPU
				//=========================Host Load=========================================//	
    		
				//=========================CPU Usage=========================================//
    		
				//=========================CPU Process=========================================//
				
					
					
    },'json');  											
				
}  
</script>