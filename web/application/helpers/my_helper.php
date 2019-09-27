<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * [errorLog 打印错误日志记录]
 * @param  [type] $message [打印日志记录]
 * @param  [type] $file    [日志文件名]
 * @return [type]          [description]
 */
function errorLog($message,$file='main.log')
{
   	//将日志文件放在根目录下/log/日期的文件夹名
   	$log_dir=$_SERVER['DOCUMENT_ROOT']."/application/logs/".date('Ymd')."/";
    //判断是否存在文件夹，没有则创建
    if(!is_dir($log_dir)){
        @mkdir($log_dir,0777,true);
    }
    //将错误日志记录写入文件中
    $file=$log_dir.$file;
    if(is_array($message)){
        $arr=explode(".",$file);
        if($arr[1]=='php'){
            error_log("<?php \n return ".var_export($message, true)."\n", 3,$file);
        }else{
             error_log(var_export($message, true)."\n", 3,$file);
        }        
    }else{
       error_log($message."\n\n", 3,$file); 
    }   
}


function get_client_ip(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
       $ip = getenv("REMOTE_ADDR");
   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
       $ip = $_SERVER['REMOTE_ADDR'];
   else
       $ip = "unknown";
   return($ip);
}

function substring($str, $start, $len) {
     $tmpstr = "";
     $strlen = $start + $len;
     for($i = 0; $i < $strlen; $i++) {
         if(ord(substr($str, $i, 1)) > 0xa0) {
             $tmpstr .= substr($str, $i, 2);
             $i++;
         } else
             $tmpstr .= substr($str, $i, 1);
     }
     return $tmpstr;
} 

function get_replication_tree($array,$host='---',$port='---',$level=0){
    $repeat='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $str_repeat = '';
    if($level) {
			for($j = 0; $j < $level; $j ++) {
				$str_repeat .= $repeat;
			}
		}
    if($level==0){
        $icon="<i class='icon-list'></i>";
    }
    else{
        $icon="<i class='icon-refresh'></i>";
    }
    $str_repeat = $str_repeat.$icon;
    $newarray = array ();
    $temparray = array ();
		
    foreach ( ( array ) $array as $v ) {
        if($v['master_server']==$host and $v['master_port']==$port)
        {
            $host_v=$v['host'];
            $port_v=$v['port'];
            $v['host']=$str_repeat.$v['host'];
            $v['level']=$level;
            $newarray[] = $v;
            $temparray = get_replication_tree($array,$host_v,$port_v,$level+1);
            
            if ($temparray) {
							$newarray = array_merge ( $newarray, $temparray );
						}elseif($level == 0){
							//如果顶层没有子项，则去除顶层
							$index = array_search($v, $newarray);
							unset($newarray[$index]);
						}
        }
    }
		
    return $newarray;
}


function get_mirror_tree($array,$host='---',$db_name='---',$level=0){
    $repeat='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $str_repeat = '';
    if($level) {
			for($j = 0; $j < $level; $j ++) {
				$str_repeat .= $repeat;
			}
		}
    if($level==0){
        $icon="<i class='icon-list'></i>";
    }
    else{
        $icon="<i class='icon-refresh'></i>";
    }
    $str_repeat = $str_repeat.$icon;
    $newarray = array ();
    $temparray = array ();
		
    foreach ( ( array ) $array as $v ) {
    	if($level==0){
        if($v['master_server']==$host)
        {
            $host_v=$v['host'];
            $v['host']=$str_repeat.$v['host'];
            $db_name_v=$v['db_name'];
            $v['level']=$level;
            $newarray[] = $v;
            $temparray = get_mirror_tree($array,$host_v,$db_name_v,$level+1);
            
            if ($temparray) {
							$newarray = array_merge ( $newarray, $temparray );
						}elseif($level == 0){
							//如果顶层没有子项，则去除顶层
							$index = array_search($v, $newarray);
							unset($newarray[$index]);
						}
        }
      }else{
        if($v['master_server']==$host and $v['db_name']==$db_name)
        {
            $host_v=$v['host'];
            $v['host']=$str_repeat.$v['host'];
            $db_name_v=$v['db_name'];
            $v['level']=$level;
            $newarray[] = $v;
            $temparray = get_mirror_tree($array,$host_v,$db_name_v,$level+1);
            
            if ($temparray) {
							$newarray = array_merge ( $newarray, $temparray );
						}elseif($level == 0){
							//如果顶层没有子项，则去除顶层
							$index = array_search($v, $newarray);
							unset($newarray[$index]);
						}
        }
      	
      }
    }
		
    return $newarray;
}


function get_redis_replication_tree($array,$server_id=0,$level=0){
    $repeat='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $str_repeat = '';
    if($level) {
        for($j = 0; $j < $level; $j ++) {
            $str_repeat .= $repeat;
        }
    }
    if($level==0){
        $icon="<i class='icon-list'></i>";
    }
    else{
        $icon="<i class='icon-refresh'></i>";
    }
    $str_repeat = $str_repeat.$icon;
    $newarray = array ();
    $temparray = array ();

    foreach ( ( array ) $array as $v ) {
        if($v['master_server_id']==$server_id)
        {
            $host_v=$v['host'];
            $port_v=$v['port'];
            $server_id_v=$v['server_id'];
            $v['host']=$str_repeat.$v['host'];
            $v['level']=$level;
            $newarray[] = $v;
            $temparray = get_redis_replication_tree($array,$server_id_v,$level+1);
   
            if ($temparray) {
                $newarray = array_merge ( $newarray, $temparray );
            }
        }
    }
    return $newarray;
}


function get_menu_record_tree($array,$parent_id=0,$level=1){
    $repeat='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$str_repeat = '';
    
	for($i = 0; $i < $level; $i++) {
	   $str_repeat .= $repeat;
	}
	$str_repeat = $str_repeat.'&rarr;';
	$newarray = array ();
	$temparray = array ();
    foreach ( ( array ) $array as $v ) {
        if(($v['parent_id']==$parent_id) and ($v['menu_level']==$level))
        {
           
            $v['str_repeat']=$str_repeat;
            $newarray[] = $v;
            
            $parent_id_v=$v['menu_id'];
            $level_v=$level+1;
            $temparray = get_menu_record_tree($array,$parent_id_v,$level_v);
   
            if ($temparray) {
				$newarray = array_merge ( $newarray, $temparray );
			}
        }
    }
    return $newarray;
}

/*
 * is_active
 */
function is_active_menu($current_action="index/index",$menu_actions = 'index/index')
{

   $menu_actions = explode('|',$menu_actions);
   //print_r($menu_actions);
   if(in_array($current_action,$menu_actions)){
        return true;
   }
   else{
        return false;
   }
}


function check_role($master,$slave){
    if($master == 1 && $slave==0){
        $data="master";
    }
    else if($master == 1 && $slave ==1){
        $data="master/slave";
    }
    else{
        $data="slave";
    }
    //$data = '<strong>'.$data.'</strong>';
    return $data;
}

function check_status($data){
    if($data == 1){
        return "<span class='badge badge-info'>Yes</span>";
    }
    else if($data == 0){
        return "<span class='badge'>No</span>";
    }
    else{
        return $data;
    }
}

function check_mail($data){
    if($data == 1){
        return "<span class='badge badge-success'>Yes</span>";
    }
    else if($data == 0){
        return "<span class='badge'>No</span>";
    }
    else{
        return $data;
    }
}



function check_on_off($data){
    if($data == 1){
        return "<span class='badge badge-info'>ON</span>";
    }
    else if($data == 0){
        return "<span class='badge'>OFF</span>";
    }
    else{
        return $data;
    }
}

function set_selected($data,$value){
    if($data == $value){
        return "selected";
    }
}
  
function check_value($data){
    if($data=='-1' || $data == null ){
        return "N/A";
    }
    else if($data=='master'){
        return "<span class='label label-info'>M</span>";
    }
    else if($data=='slave'){
        return "<span class='label label-warning'>S</span>";
    }
    else if($data=='alone'){
        return "<span class='label label-info'>Alone</span>";
    }
    else if($data=='Yes'){
        return "<span class='label label-success'>Run</span>";
    }
    else if($data=='No'){
        return "<span class='label label-important'>No</span>";
    }
    else if($data=='ON'){
        return "<span class='label label-info'>ON</span>";
    }
    else if($data=='OFF'){
        return "<span class='label '>OFF</span>";
    }
    else{
        return $data;
    }
    
}



function check_uptime($data){

    if($data ==-1){
        return "---";
    }
    else if($data < 60){
        return $data." 秒";
    }
    else if($data>=60 and $data <3600){
        return number_format(($data/60))." 分钟";
    }
    else if($data>=3600 and $data <86400){
        return number_format(($data/3600))." 小时";
    }
    else if($data>=86400){
       // $day = $data%86400
        $data=number_format(($data/86400),0)." 天";
        return $data;
    }
}

function check_memory($data)
{
    if($data=='-1'){
        return '---';
    }
    else if($data==0)
    {
        return 0;
    }
    else if($data>0 and $data<1024)
    {
        return $data."KB";
    }
    else if($data>=1024 and $data<1048576)
    {
        return number_format(($data/1024))."MB";
    }
    else
    {
        return number_format(($data/1024/1024))."GB";
    }
}

function check_cpu($data){
    if($data==-1)
    {
        return 'N/A';
    }
    if($data==0)
    {
        return "0%";
    }
    else if($data !=0 ){
        return $data.'%';
    }
    else{
        return '---';
    }
}

function check_binlog_space($data)
{
    if($data==0)
    {
        return '---';
    }
    else if($data>0 and $data<1048576)
    {
        return number_format(($data/1024))."KB";
    }
    else if($data>=1048576 and $data<1073741824)
    {
        return number_format(($data/1024/1024))."MB";
    }
    else
    {
        return number_format(($data/1024/1024/1024))."GB";
    }
}

function format_bytes($data)
{
    if($data==-1)
    {
        return '---';
    }
	else if($data>=0 and $data<1024)
    {
        return number_format(($data))."B";
    }
    else if($data>=1024 and $data<1048576)
    {
        return number_format(($data/1024))."KB";
    }
    else if($data>=1048576 and $data<1073741824)
    {
        return number_format(($data/1024/1024))."MB";
    }
    else
    {
        return number_format(($data/1024/1024/1024))."GB";
    }
}

function format_kbytes($data)
{
    if($data==-1)
    {
        return '---';
    }
    else if($data>=0 and $data<1024)
    {
        return number_format(($data))."KB";
    }
    else if($data>=1024 and $data<1048576)
    {
        return number_format(($data/1024))."MB";
    }
    else if($data>=1048576 and $data<1073741824)
    {
        return number_format(($data/1024/1024),1)."GB";
    }
    else
    {
        return number_format(($data/1024/1024/1024),1)."TB";
    }
}

function format_mbytes($data)
{
    if($data==-1)
    {
        return '---';
    }
    else if($data>0 and $data<1024)
    {
        return number_format(($data))."MB";
    }
    else if($data>=1024 and $data<1048576)
    {
        return number_format(($data/1024),1)."GB";
    }
    else
    {
        return number_format(($data/1024/1024),1)."TB";
    }
}

function format_rate($data){
    if($data==-1)
    {
        return '---';
    }
    if($data==0)
    {
        return "0%";
    }
    else if($data !=0 ){
        $result = $data*100;
        return $result.'%';
    }
    else{
        return '---';
    }
}

function check_connections($data){
    if($data > 1000){
        return "<span class='label label-warning'>".$data."</span>";
    }
    else{
        return $data;
    }
}

function check_active($data){
    if($data > 10){
        return "<span class='label label-warning'>".$data."</span>";
    }
    else{
        return $data;
    }
}

function check_10($data){
    if($data<10){
            $s='0'.$data;
        }
    else{
            $s=$data;
    }
    
    return $s;
    
}

function check_delay($data){
    
    $H='';
    $i='';
    $s='';
    if($data=='---' || $data == null ){
         $data= "---";
    }
    else if($data<60){
        $H='00';
        $i='00';
        $s=check_10($data);
        $data=$H.':'.$i.':'.$s;
        
    }
    else if($data>=60 and $data<3600){
        $H='00';
        $i=check_10(floor($data/60));
        $s=check_10($data%60);
        $data=$H.':'.$i.':'.$s;
        //return $data;exit;
    }
    else if($data>=3600 && $data<86400){
        $H=check_10(floor($data/3600));
        $i=check_10(floor($data%3600/60));
        $s=check_10($data%3600%60);
        $data=$H.':'.$i.':'.$s;
    }
    else{
        $data='1天以上'; 
    }
    
    if($data =="00:00:00"){
        return "<span class='label label-info'>".$data."</span>";
    }
    else if($data=='---' || $data == null ){
        return $data;   
    }
    else{
        return "<span class='label label-important'>".$data."</span>";
    }  
    
}

function check_mirror_role($data){  
    if($data =="1"){
        return "主体";
    }
    else if($data =="2"){
        return "镜像";
    }
    else{
        return "未知";
    }   
}

function check_mirror_state($data){  
    if($data =="0"){
        return "已挂起";
    }
    else if($data =="1"){
        return "与其他伙伴断开";
    }
    else if($data =="2"){
        return "正在同步";
    }
    else if($data =="3"){
        return "挂起故障转移";
    }
    else if($data =="4"){
        return "已同步";
    }
    else if($data =="5"){
        return "伙伴未同步";
    }
    else if($data =="6"){
        return "伙伴已同步";
    }
    else{
        return "不可访问或未镜像";
    }   
}

function check_mirror_safety_level($data){  
    if($data =="0"){
        return "未知状态";
    }
    else if($data =="1"){
        return "关闭[异步]";
    }
    else if($data =="2"){
        return "完全[同步]";
    }
    else{
        return "不可访问或未镜像";
    }   
}

function check_alarm_level($data){  
    if($data =="warning"){
        return "<span class='label label-warning'>警告</span>";
    }
    else if($data =="error"){
        return "<span class='label label-important'>紧急</span>";
    }
    else{
        return "<span class='label label-success'>".$data."</span>";
    }   
}

function check_alarm_level_2($data){  
    if($data =="warning"){
        return "bgcolor='#FF9900'";
    }
    else if($data =="critical"){
        return "bgcolor='#f13a16'";
    }
    else if($data =="ok"){
        return "bgcolor='#009900'";
    }   
}

function check_db_status_level($data,$data_tips="no_data"){
    if($data==-1){
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='no data'><img src='images/none.png' /></a>";
    }
    else if($data==1){
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='$data_tips'><img src='images/ok.png' /></a>";
    }
    else if($data==2){
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='$data_tips'><img src='images/warning.png' /></a>";
    }
    else if($data==3){
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='$data_tips'><img src='images/critical.png' /></a>";
    }
    else{
        $level_img="<img src='images/none.png' alt='none' title='no data' />";
    }
    return $level_img;
}


function check_db_status_level_new($data,$data_tips="no_data"){
    if($data==-1){
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-html='true' data-placement='top' title='no data'><i class='iconfont icon-icon2'></a>";
    }
    else if($data==1){
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='$data_tips'><i class='iconfont icon-ziyuan'></i></a>";
    }
    else if($data==2){
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='$data_tips'><i class='iconfont icon-jinggao1'></i></a>";
    }
    else if($data==3){
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='$data_tips'><i class='iconfont icon-jinggao1 iconred'></i></a>";
    }
    else{
        $level_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='no data'><i class='iconfont icon-icon2'></a>";
    }
    return $level_img;
}

function check_repl_color($normal_count, $waring_count, $critical_count){
    if($critical_count > 0){
        $repl_color=" cschedule_red";
    }
    else if($waring_count > 0){
        $repl_color=" cschedule_yellow";
    }
    else if($normal_count > 0){
        $repl_color=" cschedule_green";
    }
    else{
        $repl_color="";
    }
    
    return $repl_color;
}


function check_repl_rate($normal_count, $waring_count, $critical_count){
		$total = $normal_count + $waring_count + $critical_count;
		
    if($critical_count > 0){
        $repl_rate= round(($critical_count/$total) * 100) ;
    }
    else if($waring_count > 0){
        $repl_rate= round(($waring_count/$total) * 100) ;
    }
    else if($normal_count > 0){
        $repl_rate= round(($normal_count/$total) * 100) ;
    }
    else{
        $repl_rate="0";
    }
    
    return $repl_rate;
}


function check_db_status_role($data){
    if($data=='m'){
        $role_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='Master/Primary'><img src='images/master.png' /></a>";
    }
    else if($data=='s'){
        $role_img="<a href='javascript::(0)' data-toggle='tooltip' data-placement='top' title='Slave/Secondary'><img src='images/slave.png' /></a>";
    }
    else{
        $role_img="---";
    }
    return $role_img;
}

function check_send_mail_status($data){  
    if($data =="1"){
        return "<span class='label label-success'>成功</span>";
    }
    else{
        return "<span class='label'>失败</span>";
    }
    
}  

function check_hits($data){
    if($data){
        $result=$data*100;
        $result=$result."%";
    }
    return $result;
}

function format_percent_remove($data){
    return trim(str_replace("%","",$data));
}


function check_dbtype($data){  
    if($data =="mysql"){
        $db_type_img="<span class='textico i_mysql'>MySQL</span>";
    }
    else if($data =="mongodb"){
        $db_type_img="<img src='images/icon_mongo.png' />";
    }
    else if($data =="redis"){
        $db_type_img="<img src='images/icon_redis.png' />";
    }
    else if($data =="oracle"){
        $db_type_img="<span class='textico i_oracle'>Oracle</span>";
    }
    else if($data =="sqlserver"){
        $db_type_img="<span class='textico i_sqlserver'>SQL Server</span>";
    }
	return  $db_type_img;
}

function check_open_mode($data){  
    if($data =="READ WRITE"){
        return "读写";
    }
    else if($data =="READ ONLY WITH APPLY"){
        return "实时查询";
    }
    else if($data =="READ ONLY"){
        return "只读";
    }
    else if($data =="MOUNTED"){
        return "日志应用";
    }
    else{
        return "异常";
    }   
}
