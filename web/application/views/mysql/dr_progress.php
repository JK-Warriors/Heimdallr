<?php 
header('Content-type:text/json');

$arr = array('on_process' => $mirror_group[0]['on_process'], 
						 'on_switchover' => $mirror_group[0]['on_switchover'], 
						 'on_failover' => $mirror_group[0]['on_failover'], 
						 
						 'group_id' => $op_process[0]['group_id'],
						 'process_type' => $op_process[0]['process_type'],
						 'process_desc' => $op_process[0]['process_desc'],
						 'rate' => $op_process[0]['rate'],
						 'process_time' => $op_process[0]['create_time'],
						 
						 'op_type' => $db_opration[0]['op_type'],
						 'op_result' => $db_opration[0]['result'],
						 'op_reason' => $db_opration[0]['reason']);
						 					  		 
echo json_encode($arr);
?> 