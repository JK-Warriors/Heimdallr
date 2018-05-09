<?php 
header('Content-type:text/json');

$arr['time'] = array();

foreach ($oracle_xAxis as $lines) {
	array_push($arr['time'], $lines['time']);
}

foreach ($oracle_chart_server as $lines) {
	$server_id = $lines['server_id'];
	$name = "server_" . $server_id;
	
	$arr[$server_id] = array();
	$arr[$name] = array();
	
	foreach ($oracle_yAxis as $y) {
		if($y['server_id'] == $lines['server_id']){
			$key = $y["time"];
			$arr[$server_id][$key] = $y['delay'];
		}
	}
	
	#
	#print_r($arr[$server_id]);;
	
	for($n=0; $n < count($arr['time']); $n++){
		#echo $arr['time'][$n] . "\n";
		$time_key = $arr['time'][$n];
		if(array_key_exists($time_key, $arr[$server_id])){
				#echo $time_key . "\n";
				#echo $arr[$server_id][$time_key] . "\n";
				array_push($arr[$name], $arr[$server_id][$time_key]);
		}else{
				array_push($arr[$name], '');
		} 
	}
	
	#print_r($arr[$name]);
}


echo json_encode($arr);
				  		 
?> 