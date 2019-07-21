<?php 
header('Content-type:text/json');


$arr = array('machine_code' => $setval['machine_code'],
						 'active_result' => $setval['active_result'],
						 'active_message' => $setval['active_message']);


echo json_encode($arr);
				  		 
?> 