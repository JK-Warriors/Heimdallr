<?php 
header('Content-type:text/json');

$arr = array('on_process' => $dg_group[0]['on_process'], 
						 'on_switchover' => $dg_group[0]['on_switchover'], 
						 'on_failover' => $dg_group[0]['on_failover'], 
						 'on_startmrp' => $dg_group[0]['on_startmrp'], 
						 'on_stopmrp' => $dg_group[0]['on_stopmrp'],
						 
						 'group_id' => $dg_process[0]['group_id'],
						 'process_type' => $dg_process[0]['process_type'],
						 'process_desc' => $dg_process[0]['process_desc'],
						 'process_time' => $dg_process[0]['create_time'],
						 
						 'op_type' => $dg_opration[0]['op_type'],
						 'op_result' => $dg_opration[0]['result'],
						 'op_reason' => $dg_opration[0]['reason'],
						 
						 'rate' => $dg_process[0]['rate'],
						 'mrp_status' => $items['mrp_status'],
						 'sta_role' => $items['sta_role']);

$test = array('mrp_status' => $test['mrp_status']);
						  		 
echo json_encode($arr);
#echo json_encode($test);
?> 