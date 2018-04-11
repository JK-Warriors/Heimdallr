<?php 
header('Content-type:text/json');


$arr['delay'] = array();

foreach ($oracle_lines as $lines) {
	$arr['server_id'] = $lines['server_id'];
	
	foreach ($oracle_yAxis as $y) {
		if($lines['server_id'] == $lines['server_id']){
			
			array_push($arr['delay'], [$y[time],$y[delay]]);
		
		}
	}
	
}


echo json_encode($arr);
				  		 
?> 