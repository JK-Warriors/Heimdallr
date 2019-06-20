<?php 
header('Content-type:text/json');


$arr['time'] = array();
$arr['threads_running'] = array();
$arr['threads_created'] = array();
$arr['threads_connected'] = array();
$arr['threads_cached'] = array();

$arr['qps'] = array();
$arr['tps'] = array();

$arr['total_select_persecond'] = array();
$arr['total_insert_persecond'] = array();
$arr['total_update_persecond'] = array();
$arr['total_delete_persecond'] = array();

$arr['queries_persecond'] = array();
$arr['questions_persecond'] = array();

$arr['commit_persecond'] = array();
$arr['rollback_persecond'] = array();

$arr['bytes_received'] = array();
$arr['bytes_sent'] = array();

$arr['aborted_clients'] = array();
$arr['aborted_connects'] = array();


$arr['max_connections'] = array();
$arr['connections_used'] = array();
$arr['connections_unused'] = array();
$arr['open_files_limit'] = array();
$arr['open_files_used'] = array();
$arr['open_files_unused'] = array();

$arr['table_open_cache'] = array();
$arr['open_tables_used'] = array();
$arr['open_tables_unused'] = array();

$arr['key_buffer_read_rate'] = array();
$arr['key_buffer_write_rate'] = array();
$arr['key_blocks_used_rate'] = array();

$arr['buffer_pool_reads_persecond'] = array();
$arr['buffer_pool_pages_flushed_persecond'] = array();

$arr['rows_read_persecond'] = array();
$arr['rows_inserted_persecond'] = array();
$arr['rows_updated_persecond'] = array();
$arr['rows_deleted_persecond'] = array();

$arr['connections_persecond'] = array();


$arr['delay'] = array();

$arr['table_size'] = array();



foreach ($chart_data as $item) {
	array_push($arr['time'], $item[time]);
	array_push($arr['threads_running'], $item[threads_running]);
	array_push($arr['threads_created'], $item[threads_created]);
	array_push($arr['threads_connected'], $item[threads_connected]);
	array_push($arr['threads_cached'], $item[threads_cached]);
	
	array_push($arr['qps'], $item[queries_persecond]);
	array_push($arr['tps'], $item[transaction_persecond]);
	
	array_push($arr['total_select_persecond'], $item[com_select_persecond]);
	array_push($arr['total_insert_persecond'], $item[com_insert_persecond]);
	array_push($arr['total_update_persecond'], $item[com_update_persecond]);
	array_push($arr['total_delete_persecond'], $item[com_delete_persecond]);
	
	array_push($arr['queries_persecond'], $item[com_update_persecond]);
	array_push($arr['questions_persecond'], $item[com_delete_persecond]);
	
	array_push($arr['commit_persecond'], $item[com_commit_persecond]);
	array_push($arr['rollback_persecond'], $item[com_rollback_persecond]);
	
	array_push($arr['bytes_received'], $item[bytes_received_persecond]);
	array_push($arr['bytes_sent'], $item[bytes_sent_persecond]);
	
	array_push($arr['aborted_clients'], $item[aborted_clients]);
	array_push($arr['aborted_connects'], $item[aborted_connects]);
	
	array_push($arr['max_connections'], $item[max_connections]);
	array_push($arr['connections_used'], $item[threads_connected]);
	array_push($arr['connections_unused'], $item[max_connections] - $item[threads_connected]);
	array_push($arr['open_files_limit'], $item[open_files_limit]);
	array_push($arr['open_files_used'], $item[open_files]);
	array_push($arr['open_files_unused'], $item[open_files_limit] - $item[open_files]);
	
	array_push($arr['table_open_cache'], $item[table_open_cache]);
	array_push($arr['open_tables_used'], $item[open_tables]);
	array_push($arr['open_tables_unused'], $item[table_open_cache] - $item[open_tables]);
	
	array_push($arr['key_buffer_read_rate'], $item[key_buffer_read_rate]);
	array_push($arr['key_buffer_write_rate'], $item[key_buffer_write_rate]);
	array_push($arr['key_blocks_used_rate'], $item[key_blocks_used_rate]);
	
	array_push($arr['buffer_pool_reads_persecond'], $item[innodb_buffer_pool_reads_persecond]);
	array_push($arr['buffer_pool_pages_flushed_persecond'], $item[innodb_buffer_pool_pages_flushed_persecond]);
	
	array_push($arr['rows_read_persecond'], $item[innodb_rows_read_persecond]);
	array_push($arr['rows_inserted_persecond'], $item[innodb_rows_inserted_persecond]);
	array_push($arr['rows_updated_persecond'], $item[innodb_rows_updated_persecond]);
	array_push($arr['rows_deleted_persecond'], $item[innodb_rows_deleted_persecond]);
	
	array_push($arr['connections_persecond'], $item[connections_persecond]);
	
	//from msql_replication_his
	array_push($arr['delay'], $item[delay]);
	
	//from msql_bigtable_his
	array_push($arr['table_size'], $item[table_size]);
	
}


echo json_encode($arr);
				  		 
?> 