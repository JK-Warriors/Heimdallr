<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Wl_tool extends Front_Controller {

    function __construct(){
		parent::__construct();
        $this->load->model("tool_model","tool");
	}
    
   
	public function lock()
	{
        parent::check_privilege();
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["db_type"]=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        
        $data["datalist"]=$this->tool->get_db_list();
        
        
        $data["setval"]=$setval;
        
        $this->layout->view("tool/lock", $data);
	}
    
	public function lock_view()
	{
        parent::check_privilege();
        $data["datalist"]=$this->tool->get_db_list();
        
        $server_id=isset($_GET["server_id"]) ? $_GET["server_id"] : "";
        $db_type=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        $url_username=isset($_GET["username"]) ? $_GET["username"] : "";
        $url_machine=isset($_GET["machine"]) ? $_GET["machine"] : "";
        $url_program=isset($_GET["program"]) ? $_GET["program"] : "";
        $url_client_ip=isset($_GET["client_ip"]) ? $_GET["client_ip"] : "";
        $url_object_name=isset($_GET["object_name"]) ? $_GET["object_name"] : "";
        
        $data[server_id]=$server_id;
        $data[db_type]=$db_type;
        $data[username]=$url_username;
        $data[machine]=$url_machine;
        $data[program]=$url_program;
        $data[client_ip]=$url_client_ip;
        $data[object_name]=$url_object_name;
        
        if($db_type=='oracle'){
        	#step 1: get config
	 				$conn_str = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				
					try{
						$conn = oci_connect($username,$password,$conn_str);
						
	  				if (!$conn) {
	    				errorLog('wl_tool -> lock_view -> Error: Unable to connect to Oracle.' . oci_error());
						}else{
							#errorLog('wl_tool -> lock_view -> Connect Succ'); 
	        		$sql="select s.sid, s.serial#, s.status, s.username, s.machine, s.program, s.client_info, o.object_name, l.type, l.lmode, l.ctime, s.sql_id, to_char(a.sql_fulltext) sql_text
											from v\$session s, v\$lock l, v\$locked_object lo, dba_objects o, v\$sqlarea a
											where s.type != 'BACKGROUND' 
											and s.sid = l.sid
											and s.sid = lo.session_id(+)
											and lo.object_id = o.object_id(+)
											and l.block > 0
											and l.ctime > 0
											and l.type in ('TM', 'TX')
											and s.sql_id = a.sql_id(+)";
	        		if($url_username != ""){
	        			$sql = $sql . " AND s.username like '%" . $url_username . "%'";
	        		}
	        		if($url_machine != ""){
	        			$sql = $sql . " AND s.machine like '%" . $url_machine . "%'";
	        		}
	        		if($url_program != ""){
	        			$sql = $sql . " AND s.program like '%" . $url_program . "%'";
	        		}
	        		if($url_client_ip != ""){
	        			$sql = $sql . " AND s.client_info like '%" . $url_client_ip . "%'";
	        		}
	        		if($url_object_name != ""){
	        			$sql = $sql . " AND o.object_name like '%" . $url_object_name . "%'";
	        		}
	        		
	        		$stmt = oci_parse($conn, $sql);
	        		$result = oci_execute($stmt, OCI_DEFAULT);
	        		errorLog($result);
							#$rows =oci_fetch_all($stmt, $session_data);
							while (( $row  =  oci_fetch_array ( $stmt ,  OCI_NUM )) !=  false ) {
								$nrow['sid'] = $row[0];
								$nrow['serial#'] = $row[1];
								$nrow['status'] = $row[2];
								$nrow['username'] = $row[3];
								$nrow['machine'] = $row[4];
								$nrow['program'] = $row[5];
								$nrow['client_info'] = $row[6];
								$nrow['object_name'] = $row[7];
								$nrow['type'] = $row[8];
								$nrow['lmode'] = $row[9];
								$nrow['ctime'] = $row[10];
								$nrow['sql_id'] = $row[11];
								$nrow['sql_text'] = $row[12];
								$lock_list[]=$nrow;
							}
							$data["lock_list"]=$lock_list;
						}
						
					}
					catch(Exception $e){
	 					errorLog($e->getMessage());
					}finally {
						if($stmt){oci_free_statement($stmt);};
						if($conn){oci_close($conn);};
					}
					
        	$this->layout->view("tool/lock_oracle", $data);
        }
        elseif($db_type=='mysql'){
        	#step 1: get config
	 				$host = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				$port = $this->tool->get_port_by_id($server_id, $db_type);
        	
					try{
        		$conn=mysqli_connect($host,$username,$password, '', $port);
        		if (!$conn) {
	    				errorLog('wl_tool -> session -> Error: Unable to connect to MySQL.' . mysqli_connect_error());
						}else{
							#errorLog('wl_tool -> session -> Connect Succ'); 
	        		$sql="select id as sid, user, host, db, command, time, state, info, it2.trx_mysql_thread_id as blocked, l.lock_table
											from information_schema.processlist p, 
											information_schema.innodb_locks l,
											information_schema.innodb_lock_waits lw, 
											information_schema.innodb_trx it1, 
											information_schema.innodb_trx it2
											where lw.requesting_trx_id = it1.trx_id 
											and it1.trx_mysql_thread_id = p.id
											and lw.blocking_trx_id =it2.trx_id
											and lw.blocking_trx_id = l.lock_trx_id ";
	        		
	        		if($url_username != ""){
	        			$sql = $sql . " AND p.user like '%" . $url_username . "%'";
	        		}
	        		if($url_client_ip != ""){
	        			$sql = $sql . " AND p.host like '%" . $url_client_ip . "%'";
	        		}
	        		if($url_object_name != ""){
	        			$sql = $sql . " AND l.lock_table like '%" . $url_object_name . "%'";
	        		}
	        		#errorLog($sql);
	        		$result=mysqli_query($conn,$sql);
	        		
	        		## 获取数据
	        		$session_data = mysqli_fetch_all($result,MYSQLI_ASSOC);
	        		
							$data["lock_list"]=$session_data;
							
	        		## 释放结果集
	        		mysqli_free_result($result);

	        		mysqli_close($con);
							
						}
					}
					catch(Exception $e){
	 					errorLog($e->getMessage());
					}finally {
						if($conn){mysqli_close($conn);};
					}
        	
        	$this->layout->view("tool/lock_mysql", $data);
        }
        elseif($db_type=='sqlserver'){
	 				$conn_str = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				# example: dblib:host=192.168.100.10:1433
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				
        	try{
						$conn = new PDO($conn_str,$username,$password);
						
	  				if (!$conn) {
	    				errorLog('Error: Unable to connect to SQLServer.');
						}else{
							#errorLog('Succ'); 
	        		$sql="SELECT  [sid] = er.session_id ,
								            ecid ,
								            [dbname] = DB_NAME(sp.dbid) ,
								            [username] = es.login_name ,
								            [status] = er.status ,
								            [wait] = wait_type ,
								            [waittime] = sp.waittime ,
								            [blocked] = sp.blocked ,
								            er.sql_handle,
								            [sql_text] = SUBSTRING(qt.text,
								                                           er.statement_start_offset / 2,
								                                           ( CASE WHEN er.statement_end_offset = -1
								                                                  THEN LEN(CONVERT(NVARCHAR(MAX), qt.text))
								                                                       * 2
								                                                  ELSE er.statement_end_offset
								                                             END - er.statement_start_offset )
								                                           / 2) ,
								            [parent_sql_text] = qt.text ,
								            program = sp.program_name ,
								            hostname ,
								            [client_ip] = DC.CLIENT_NET_ADDRESS,
								            sp.nt_domain ,
								            start_time
								    FROM    sys.dm_exec_requests er
								            INNER JOIN sys.[dm_exec_sessions] es ON er.session_id = es.session_id
								            INNER JOIN sys.sysprocesses sp ON er.session_id = sp.spid
								            INNER JOIN SYS.DM_EXEC_CONNECTIONS AS DC ON SP.SPID = DC.SESSION_ID
								            CROSS APPLY sys.dm_exec_sql_text(er.sql_handle) AS qt
								    WHERE   es.is_user_process = 1  -- Ignore system spids.
								      AND   er.session_id NOT IN ( @@SPID )
									";
	        		if($url_username != ""){
	        			$sql = $sql . " AND es.login_name like '%" . $url_username . "%'";
	        		}
	        		if($url_machine != ""){
	        			$sql = $sql . " AND hostname like '%" . $url_machine . "%'";
	        		}
	        		if($url_program != ""){
	        			$sql = $sql . " AND sp.program_name like '%" . $url_program . "%'";
	        		}
	        		if($url_client_ip != ""){
	        			$sql = $sql . " AND dc.client_net_address like '%" . $url_client_ip . "%'";
	        		}
								
							foreach ($conn->query($sql) as $row) {
								$session_data[]=$row;
					    }
							$data["lock_list"]=$session_data;
	        		
						}
						
	        	
					}
					catch(PDOException $e){
	 					errorLog($e->getMessage());
					}finally {
						$conn=null;			#关闭连接
					}
					
        	$this->layout->view("tool/lock_sqlserver", $data);
        }
        
	}
	

	public function kill_session()
	{
        parent::check_privilege();
        
        $server_id=isset($_GET["server_id"]) ? $_GET["server_id"] : "";
        $db_type=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        
	      $data["result"] = -1;
	      
        if($db_type=='oracle'){
        	$sid=isset($_POST["sid"]) ? $_POST["sid"] : "";
        	$serial=isset($_POST["serial"]) ? $_POST["serial"] : "";
        	
        	#step 1: get config
	 				$conn_str = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				
					try{
						$conn = oci_connect($username,$password,$conn_str);
						
	  				if (!$conn) {
	    				errorLog('wl_tool -> kill_session -> Error: Unable to connect to Oracle.' . oci_error());
						}else{
							#errorLog('wl_tool -> kill_session -> Connect Succ'); 
	        		$sql="alter system kill session '" . $sid . "," . $serial . "' immediate";
							#errorLog($sql);
	        		$stmt = oci_parse($conn, $sql);
	        		$result = oci_execute($stmt, OCI_DEFAULT);
	        		#errorLog($result);
	        		if($result == 1){
	        			$data["result"] = 1;
	        		}
	        		else{
	        			$data["result"] = 0;
	        		}
						}
						
					}
					catch(Exception $e){
	 					errorLog($e->getMessage());
					}finally {
						if($stmt){oci_free_statement($stmt);};
						if($conn){oci_close($conn);};
					}
        }elseif($db_type=='mysql'){
        	$sid=isset($_POST["sid"]) ? $_POST["sid"] : "";
        	
	 				#step 1: get config
	 				$host = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				$port = $this->tool->get_port_by_id($server_id, $db_type);
        	
					try{
        		$conn=mysqli_connect($host,$username,$password, '', $port);
        		if (!$conn) {
	    				errorLog('wl_tool -> session -> Error: Unable to connect to MySQL.' . mysqli_connect_error());
						}else{
							#errorLog('wl_tool -> session -> Connect Succ'); 
	        		errorLog($sid);
	        		$result = mysqli_kill($conn,$sid);
	        		$error = mysqli_error($conn);
	        		if($result == 1){
	        			$data["result"] = 1;
	        		}
	        		else{
	        			$data["result"] = 0;
	        		}
						}
					}
					catch(Exception $e){
	 					errorLog($e->getMessage());
					}finally {
						if($conn){mysqli_close($conn);};
					}
        	
        }elseif($db_type=='sqlserver'){
        	$sid=isset($_POST["sid"]) ? $_POST["sid"] : "";
	 				$conn_str = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				# example: dblib:host=192.168.100.10:1433
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				
        	try{
						$conn = new PDO($conn_str,$username,$password);
						
	  				if (!$conn) {
	    				errorLog('wl_tool -> kill_session -> Error: Unable to connect to SQLServer.');
						}else{					
							#errorLog('wl_tool -> kill_session -> Connect Succ'); 
	        		$sql="kill " . $sid . "";
							#errorLog($sql);
	        		$conn->exec($sql);
	        		$result = $conn->errorCode();
	        		if($result == '00000'){
	        			$data["result"] = 1;
	        		}
	        		else{
	        			$data["result"] = 0;
	        			errorLog($conn->errorInfo());
	        		}
						}
						
	        	
					}
					catch(PDOException $e){
	 					errorLog($e->getMessage());
					}finally {
						$conn=null;			#关闭连接
					}
					
        }
        
				$this->layout->setLayout("layout_blank");
        $this->layout->view("tool/json_data", $data);
	}
	
		     
	public function session()
	{
        parent::check_privilege();
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["db_type"]=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        
        $data["datalist"]=$this->tool->get_db_list();
        
        
        $data["setval"]=$setval;
        
        $this->layout->view("tool/session", $data);
	}
	
	public function session_trace()
	{
        parent::check_privilege();
        $data["datalist"]=$this->tool->get_db_list();
        
        $server_id=isset($_GET["server_id"]) ? $_GET["server_id"] : "";
        $db_type=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        $url_username=isset($_GET["username"]) ? $_GET["username"] : "";
        $url_machine=isset($_GET["machine"]) ? $_GET["machine"] : "";
        $url_program=isset($_GET["program"]) ? $_GET["program"] : "";
        $url_client_ip=isset($_GET["client_ip"]) ? $_GET["client_ip"] : "";
        
        $data[server_id]=$server_id;
        $data[db_type]=$db_type;
        $data[username]=$url_username;
        $data[machine]=$url_machine;
        $data[program]=$url_program;
        $data[client_ip]=$url_client_ip;
        
        if($db_type=='oracle'){
        	#step 1: get config
	 				$conn_str = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				
					try{
						$conn = oci_connect($username,$password,$conn_str);
						
	  				if (!$conn) {
	    				errorLog('wl_tool -> session -> Error: Unable to connect to Oracle.' . oci_error());
						}else{
							#errorLog('wl_tool -> session -> Connect Succ'); 
	        		$sql="select s.sid, s.serial#, s.status, s.username, s.machine, s.program, s.client_info, s.event, s.sql_id, to_char(a.sql_fulltext) sql_text
					        		from v\$session s, v\$sqlarea a 
					        		where s.type != 'BACKGROUND' 
					        		and s.sql_id = a.sql_id(+)";
	        		if($url_username != ""){
	        			$sql = $sql . " AND s.username like '%" . $url_username . "%'";
	        		}
	        		if($url_machine != ""){
	        			$sql = $sql . " AND s.machine like '%" . $url_machine . "%'";
	        		}
	        		if($url_program != ""){
	        			$sql = $sql . " AND s.program like '%" . $url_program . "%'";
	        		}
	        		if($url_client_ip != ""){
	        			$sql = $sql . " AND s.client_info like '%" . $url_client_ip . "%'";
	        		}
	        		#errorLog($sql);
	        		$stmt = oci_parse($conn, $sql);
	        		
	        		oci_execute($stmt, OCI_DEFAULT);

							#$rows =oci_fetch_all($stmt, $session_data);
							while (( $row  =  oci_fetch_array ( $stmt ,  OCI_NUM )) !=  false ) {
								$nrow['sid'] = $row[0];
								$nrow['serial#'] = $row[1];
								$nrow['status'] = $row[2];
								$nrow['username'] = $row[3];
								$nrow['machine'] = $row[4];
								$nrow['program'] = $row[5];
								$nrow['client_info'] = $row[6];
								$nrow['event'] = $row[7];
								$nrow['sql_id'] = $row[8];
								$nrow['sql_text'] = $row[9];
								$session_data[]=$nrow;
							}
							$data["session_data"]=$session_data;
							#errorLog($rows);
						}
						
					}
					catch(Exception $e){
	 					errorLog($e->getMessage());
					}finally {
						if($stmt){oci_free_statement($stmt);};
						if($conn){oci_close($conn);};
					}
					
        	$this->layout->view("tool/session_oracle", $data);
        }
        elseif($db_type=='mysql'){
        	#step 1: get config
	 				$host = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				$port = $this->tool->get_port_by_id($server_id, $db_type);
        	
					try{
        		$conn=mysqli_connect($host,$username,$password, '', $port);
        		if (!$conn) {
	    				errorLog('wl_tool -> session -> Error: Unable to connect to MySQL.' . mysqli_connect_error());
						}else{
							#errorLog('wl_tool -> session -> Connect Succ'); 
	        		$sql="select id as sid, user, host, db, command, time, state, info from information_schema.processlist p where 1=1";
	        		
	        		if($url_username != ""){
	        			$sql = $sql . " AND p.user like '%" . $url_username . "%'";
	        		}
	        		if($url_client_ip != ""){
	        			$sql = $sql . " AND p.host like '%" . $url_client_ip . "%'";
	        		}
	        		#errorLog($sql);
	        		$result=mysqli_query($conn,$sql);
	        		
	        		## 获取数据
	        		$session_data = mysqli_fetch_all($result,MYSQLI_ASSOC);
	        		
							$data["session_data"]=$session_data;
							
	        		## 释放结果集
	        		mysqli_free_result($result);

	        		mysqli_close($con);
							
						}
        		$this->layout->view("tool/session_mysql", $data);
					}
					catch(Exception $e){
	 					errorLog($e->getMessage());
					}finally {
						if($conn){mysqli_close($conn);};
					}
        	
        	$this->layout->view("tool/session_mysql", $data);
        }elseif($db_type=='sqlserver'){
	 				$conn_str = $this->tool->get_conn_str_by_id($server_id, $db_type);
	 				# example: dblib:host=192.168.100.10:1433
	 				$username = $this->tool->get_username_by_id($server_id, $db_type);
	 				$password = $this->tool->get_passwd_by_id($server_id, $db_type);
	 				
        	try{
						$conn = new PDO($conn_str,$username,$password);
						
	  				if (!$conn) {
	    				errorLog('Error: Unable to connect to SQLServer.');
						}else{
							#errorLog('Succ'); 
	        		$sql="SELECT  [sid] = er.session_id ,
								            ecid ,
								            [dbname] = DB_NAME(sp.dbid) ,
								            [username] = es.login_name ,
								            [status] = er.status ,
								            [wait] = wait_type ,
								            er.sql_handle,
								            [sql_text] = SUBSTRING(qt.text,
								                                           er.statement_start_offset / 2,
								                                           ( CASE WHEN er.statement_end_offset = -1
								                                                  THEN LEN(CONVERT(NVARCHAR(MAX), qt.text))
								                                                       * 2
								                                                  ELSE er.statement_end_offset
								                                             END - er.statement_start_offset )
								                                           / 2) ,
								            [parent_sql_text] = qt.text ,
								            program = sp.program_name ,
								            hostname ,
								            [client_ip] = DC.CLIENT_NET_ADDRESS,
								            sp.nt_domain ,
								            start_time
								    FROM    sys.dm_exec_requests er
								            INNER JOIN sys.[dm_exec_sessions] es ON er.session_id = es.session_id
								            INNER JOIN sys.sysprocesses sp ON er.session_id = sp.spid
								            INNER JOIN SYS.DM_EXEC_CONNECTIONS AS DC ON SP.SPID = DC.SESSION_ID
								            CROSS APPLY sys.dm_exec_sql_text(er.sql_handle) AS qt
								    WHERE   es.is_user_process = 1  -- Ignore system spids.
								      AND   er.session_id NOT IN ( @@SPID ) 
									";
	        		if($url_username != ""){
	        			$sql = $sql . " AND es.login_name like '%" . $url_username . "%'";
	        		}
	        		if($url_machine != ""){
	        			$sql = $sql . " AND hostname like '%" . $url_machine . "%'";
	        		}
	        		if($url_program != ""){
	        			$sql = $sql . " AND sp.program_name like '%" . $url_program . "%'";
	        		}
	        		if($url_client_ip != ""){
	        			$sql = $sql . " AND dc.client_net_address like '%" . $url_client_ip . "%'";
	        		}
								
							foreach ($conn->query($sql) as $row) {
								$session_data[]=$row;
					    }
							$data["session_data"]=$session_data;
	        		
						}
						
	        	
					}
					catch(PDOException $e){
	 					errorLog($e->getMessage());
					}finally {
						$conn=null;			#关闭连接
					}
					
        	$this->layout->view("tool/session_sqlserver", $data);
        }
        
				
	}
	
	
	public function healthcheck()
	{
        parent::check_privilege();
        $setval["host"]=isset($_GET["host"]) ? $_GET["host"] : "";
        $setval["tags"]=isset($_GET["tags"]) ? $_GET["tags"] : "";
        $setval["db_type"]=isset($_GET["db_type"]) ? $_GET["db_type"] : "";
        
        $data["datalist"]=$this->tool->get_healthcheck_list();
        
        
        $data["setval"]=$setval;
        
        $this->layout->view("tool/dblist", $data);
	}
}	

/* End of file tool.php */
/* Location: ./application/controllers/tool.php */
