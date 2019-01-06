<?php 
header('Content-type:text/json');


$arr['time'] = array();
$arr['processes'] = array();
$arr['connections'] = array();
$arr['running'] = array();
$arr['waits'] = array();
$arr['received'] = array();
$arr['sent'] = array();
$arr['errors'] = array();

foreach ($chart_data as $item) {
	array_push($arr['time'], $item[time]);
	array_push($arr['processes'], $item[processes]);
	array_push($arr['connections'], $item[connections_persecond]);
	array_push($arr['running'], $item[processes_running]);
	array_push($arr['waits'], $item[processes_waits]);
	array_push($arr['received'], $item[pack_received_persecond]);
	array_push($arr['sent'], $item[pack_sent_persecond]);
	array_push($arr['errors'], $item[packet_errors_persecond]);
}


echo json_encode($arr);
				  		 
?> 