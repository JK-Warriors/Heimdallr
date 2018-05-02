<?php 
header('Content-type:text/json');


$arr['time'] = array();
$arr['session_total'] = array();
$arr['session_actives'] = array();
$arr['session_waits'] = array();
$arr['logical_reads'] = array();
$arr['physical_reads'] = array();
$arr['physical_writes'] = array();
$arr['read_io'] = array();
$arr['write_io'] = array();
$arr['db_block_changes'] = array();
$arr['os_cpu_wait_time'] = array();
$arr['opened_cursors'] = array();
$arr['user_commits'] = array();
$arr['user_rollbacks'] = array();

foreach ($chart_data as $item) {
	array_push($arr['time'], $item[time]);
	array_push($arr['session_total'], $item[session_total]);
	array_push($arr['session_actives'], $item[session_actives]);
	array_push($arr['session_waits'], $item[session_waits]);
	array_push($arr['logical_reads'], $item[session_logical_reads_persecond]);
	array_push($arr['physical_reads'], $item[physical_reads_persecond]);
	array_push($arr['physical_writes'], $item[physical_writes_persecond]);
	array_push($arr['read_io'], $item[physical_read_io_requests_persecond]);
	array_push($arr['write_io'], $item[physical_write_io_requests_persecond]);
	array_push($arr['db_block_changes'], $item[db_block_changes_persecond]);
	array_push($arr['os_cpu_wait_time'], $item[os_cpu_wait_time]);
	array_push($arr['opened_cursors'], $item[opened_cursors_current]);
	array_push($arr['user_commits'], $item[user_commits_persecond]);
	array_push($arr['user_rollbacks'], $item[user_rollbacks_persecond]);
}


echo json_encode($arr);
				  		 
?> 