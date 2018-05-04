<?php 
header('Content-type:text/json');

$arr['time'] = array();


foreach ($oracle_chart_server as $lines) {
	$name = "server_" . $lines['server_id'];
	$arr[$name] = array();
	
	$arr['time'] = array();													//清空time数组，只需要保留最后一组数据即可
	foreach ($oracle_yAxis as $y) {
		if($y['server_id'] == $lines['server_id']){
			array_push($arr['time'], $y['time']);
			array_push($arr[$name], $y['delay']);
		}
	}
	
}


echo json_encode($arr);
				  		 
?> 