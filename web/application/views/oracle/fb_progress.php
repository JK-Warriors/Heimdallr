<?php 
header('Content-type:text/json');

$arr = array('fb_type' => $fb_process[0]['fb_type'], 
						 'fb_object' => $fb_process[0]['fb_object'], 
						 'fb_process' => $fb_process[0]['on_process'], 
						 'fb_result' => $fb_process[0]['result'], 
						 'fb_reason' => $fb_process[0]['reason'], 
						 'fb_blocked' => $fb_process[0]['blocked']);

						  		 
echo json_encode($arr);
?> 