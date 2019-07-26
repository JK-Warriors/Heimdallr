<?php 
header('Content-type:text/json');


$arr = array('connect' => $setval['connect'],
						 'error_code' => $error_code,
						 'error_message' => $error_message);


echo json_encode($arr);
				  		 
?> 