<?php 
header('Content-type:text/json');


$arr['time'] = array();
$arr['delay'] = array();

foreach ($oracle_lines as $lines) {
	$arr['server_id'] = $lines['server_id'];
	
	foreach ($oracle_xAxis as $x) {
		if($x['server_id'] == $lines['server_id']){
			$time =  $x['time'];
			array_push($arr['time'], $time);
		
			foreach ($oracle_yAxis as $y) {
				if($y['server_id'] == $lines['server_id'] && $y['time'] == $time){
					$delay = $y['delay'];
					array_push($arr['delay'], $delay);
				}
			
			} 
		}
	}
	
}



echo json_encode($arr);
				  		 
?> 