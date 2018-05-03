<?php 
header('Content-type:text/json');


$arr['time'] = array();
$arr['used_rate'] = array();

foreach ($disk_data as $item) {
	array_push($arr['time'], $item[time]);
	array_push($arr['used_rate'], $item[used_rate]);
}


echo json_encode($arr);
				  		 
?> 