<?php 
header('Content-type:text/json');


$arr = array('result' => $result,
						 'error_code' => $error_code,
						 'error_message' => $error_message);


echo json_encode($arr);
				  		 
?> 