<?php 
header('Content-type:text/json');


$arr['time'] = array();
$arr['process'] = array();
$arr['load_1'] = array();
$arr['load_5'] = array();
$arr['load_15'] = array();
$arr['cpu_user_time'] = array();
$arr['cpu_system_time'] = array();
$arr['cpu_idle_time'] = array();
$arr['mem_usage_rate'] = array();
$arr['swap_avail_rate'] = array();
$arr['disk_io_reads'] = array();
$arr['disk_io_writes'] = array();
$arr['net_in_bytes'] = array();
$arr['net_out_bytes'] = array();

foreach ($chart_data as $item) {
	array_push($arr['time'], $item[time]);
	array_push($arr['process'], $item[process]);
	array_push($arr['load_1'], $item[load_1]);
	array_push($arr['load_5'], $item[load_5]);
	array_push($arr['load_15'], $item[load_15]);
	array_push($arr['cpu_user_time'], $item[cpu_user_time]);
	array_push($arr['cpu_system_time'], $item[cpu_system_time]);
	array_push($arr['cpu_idle_time'], $item[cpu_idle_time]);
	array_push($arr['mem_usage_rate'], $item[mem_usage_rate]);
	array_push($arr['swap_avail_rate'], $item[swap_avail_rate]);
	array_push($arr['disk_io_reads'], $item[disk_io_reads_total]);
	array_push($arr['disk_io_writes'], $item[disk_io_writes_total]);
	array_push($arr['net_in_bytes'], $item[net_in_bytes_total]);
	array_push($arr['net_out_bytes'], $item[net_out_bytes_total]);
}


echo json_encode($arr);
				  		 
?> 