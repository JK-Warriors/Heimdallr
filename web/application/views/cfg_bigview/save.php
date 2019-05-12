<?php 
header('Content-type:text/json');

$arr = array('center_db1' => $items['center_db1'],
						 'center_db2' => $items['center_db1'],
						 'center_db3' => $items['center_db1'],
						 'core_db' => $items['center_db1'],
						 'core_os' => $items['center_db1'],
						 'Success' => true);
						  		 
echo json_encode($arr);
#echo json_encode($test);
?> 