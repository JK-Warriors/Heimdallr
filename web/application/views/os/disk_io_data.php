<?php 
header('Content-type:text/json');


$arr['time'] = array();
$arr['disk_io_reads'] = array();
$arr['disk_io_writes'] = array();

foreach ($disk_io_data as $item) {
	array_push($arr['time'], $item[time]);
	array_push($arr['disk_io_reads'], $item[disk_io_reads]);
	array_push($arr['disk_io_writes'], $item[disk_io_writes]);
}


echo json_encode($arr);
				  		 
?> 