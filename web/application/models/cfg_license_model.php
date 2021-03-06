<?php 
class cfg_license_model extends CI_Model{
	  private static $PRIVATE_KEY = '';
    private static $PUBLIC_KEY = '-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAu4JxauiYZWM6vWRitoUX
taDwA7aabma4poOtweRWPGYgDFhXoUpZxho8fcR6q9kaSnOh+bu/Q/Wc8nqNq15f
wSj6I9GE5FtV57AM7Tcx0+fg2emhzqZ4YKa/yxheii7OAdJTsA4vk8bkCq2LSqli
GgK5oBRtdM5srKALymvEUnZb6jxp/V/HzB08BuPHfQcvW/BHB/FTjcy6uHH9uQZE
qIG0yrYorOC5DJ3gHOH45GQ4nWXkya61BxDCwW++bOqzhSzY0XYhZJduXShs50Iu
90lcNy9I6FiHZPwnENvj4k9RcJ34GcVwA69+y3NkEJbAdbRATDXSCogYiFFc/Blj
cniUezgoUTs+wacRouH1MnXA8vKH+xrOJV2SJAV5aGtt6x8xtnABsYERL02vn15v
CVXAv1JcTli9Hi0uIgTRlLYWGnW85fFKRkHxSTSPoWTyE0JqTzG+J+s1tJRTlIUP
RNFSB8hkRwlhDUdE9G06XCeKeSZXogYc1frSRtTTPyVN/Qqh/30TK56o8nDZMlHD
M/hi2RO38xHb1KCYIJmqaR0qxZBiKVELuvrlICKzMOWyT2gh28LPGXXzu3DvnB1k
gkQAK6rpSmr5RYhB5BqquLZWkWu91U7cJxWsl/8TtF69eeZAAMeMqXarCELCFV0q
hbaMIjgsqOhJUwoQb0ymNVMCAwEAAQ==
-----END PUBLIC KEY-----';   
  
   		
		#获取License到期时间
		function get_exprie_date()
		{
	    $license_data = $this->get_license();  
			if($license_data){
				return $license_data['expiration_time'];
			}else{
				return null;
			}
		} 

		#获取License 配额
		function get_license_quota($quota_type){
	    $license_data = $this->get_license();  
			if($license_data){
					if($quota_type == 'ora_watch')
					{
						return $license_data['config_info']['ora_watch'];
					}elseif($quota_type == 'ora_recover'){
						return $license_data['config_info']['ora_recover'];
					}elseif($quota_type == 'mysql_watch'){
						return $license_data['config_info']['mysql_watch'];
					}elseif($quota_type == 'mysql_recover'){
						return $license_data['config_info']['mysql_recover'];
					}elseif($quota_type == 'mssql_watch'){
						return $license_data['config_info']['mssql_watch'];
					}elseif($quota_type == 'mssql_recover'){
						return $license_data['config_info']['mssql_recover'];
					}
					else{
						return 0;
					}
					
			}else{
				return 0;
			}
		}
		

		function set_license($license_code)
		{
			
			if(!$license_code){
				return false;
			}
			
      $base_path=$_SERVER['DOCUMENT_ROOT'];
		  $license_path = $base_path . '/application/license/license';
		  
			$license_data = @file_put_contents($license_path, $license_code);
			
			if($license_data){
				return true;
			}else{
				return false;
			}
		}


		function get_license()
		{
      $base_path=$_SERVER['DOCUMENT_ROOT'];
		  $license_path = $base_path . '/application/license/license';
		  
			$license_contents = @file_get_contents($license_path);
			
			#$license_data = json_decode($this->licensecrypto($license_contents),true);
			#改为RSA加密
			$license_data = json_decode($this->publicDecrypt($license_contents),true);
			
			return $license_data;
		}
		

		
		
    public function getMacAddr($os_type)   
    {   
    		$return_array = array(); 
    		
        switch ( strtolower($os_type) )   
        {   
                case "linux":   
                        $return_array = $this->forLinux();   
                        break;   
                case "solaris":   
                        break;   
                case "unix":   
                        break;   
                case "aix":   
                        break;   
                default:   
                        $return_array = $this->forWindows();   
                        break;   
        }
        
        $mac_addr='';   
        $temp_array = array();   
        foreach ( $return_array as $value )   
        {   
                if ( preg_match( "/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i", $value, $temp_array ) )   
                {   
                        $mac_addr = $temp_array[0];   
                        break;   
                }   
        }   
        unset($temp_array);   
        return md5($mac_addr);    
        
    }  
        
    function forLinux() { 
    		$return_array = array(); // 返回带有MAC地址的字串数组    
        @exec ( "ifconfig -a", $return_array );  
        return $return_array;  
    }        
         
    function forWindows()   
    {   
    		$return_array = array(); // 返回带有MAC地址的字串数组  
        @exec("ipconfig /all", $return_array);   
        if ( $return_array )   
                return $return_array;   
        else{   
                $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe";   
                if ( is_file($ipconfig) )   
                        @exec($ipconfig." /all", $return_array);   
                else  
                        @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $return_array);   
                return $return_array;   
        }   
    }   
   
    //字符串解密加密
		function licensecrypto($string, $operation = 'DECODE', $key = '') {
			$ckey_length = 4; // 随机密钥长度 取值 0-32;
			// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
			// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
			// 当此值为 0 时，则不产生随机密钥
			$key    = md5($key ? $key : 'ywy_drm');
		
			$keya   = md5(substr($key, 0, 16));
			$keyb   = md5(substr($key, 16, 16));
			$keyc   = ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(time()), -$ckey_length));
		
			$cryptkey   = $keya . md5($keya . $keyc);
			$key_length = strlen($cryptkey);
		
			$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', 0) . substr(md5($string . $keyb), 0, 16) . $string;
		
			$string_length = strlen($string);
			$result        = '';
			$box           = range(0, 255);
			$rndkey        = array();
		
			for ($i = 0; $i <= 255; $i++) {
				$rndkey[$i] = ord($cryptkey[$i % $key_length]);
			}
		
			for ($j = $i = 0; $i < 256; $i++) {
				$j       = ($j + $box[$i] + $rndkey[$i]) % 256;
				$tmp     = $box[$i];
				$box[$i] = $box[$j];
				$box[$j] = $tmp;
			}
		
			for ($a = $j = $i = 0; $i < $string_length; $i++) {
				$a       = ($a + 1) % 256;
				$j       = ($j + $box[$a]) % 256;
				$tmp     = $box[$a];
				$box[$a] = $box[$j];
				$box[$j] = $tmp;
				$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
			}
		
			if ($operation == 'DECODE') {
				if ((substr($result, 0, 10) == 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
					return substr($result, 26);
				} else {
					return '';
				}
			} else {
				return $keyc . str_replace('=', '', base64_encode($result));
			}
		}
		

    /**     
     * 获取私钥     
     * @return bool|resource     
     */    
    private static function getPrivateKey() 
    {        
        $privKey = self::$PRIVATE_KEY;        
        return openssl_pkey_get_private($privKey);    
    }    
 
    /**     
     * 获取公钥     
     * @return bool|resource     
     */    
    private static function getPublicKey()
    {        
        $publicKey = self::$PUBLIC_KEY;        
        return openssl_pkey_get_public($publicKey);    
    }    
 
    /**     
     * 私钥加密     
     * @param string $data     
     * @return null|string     
     */    
    public static function privEncrypt($data = '')    
    {        
        if (!is_string($data)) {            
            return null;       
        }
				
        return openssl_private_encrypt($data,$encrypted,self::getPrivateKey()) ? base64_encode($encrypted) : null;    
    }    
 
    /**     
     * 公钥加密     
     * @param string $data     
     * @return null|string     
     */    
    public static function publicEncrypt($data = '')   
    {        
        if (!is_string($data)) {            
            return null;        
        }
        
        return openssl_public_encrypt($data,$encrypted,self::getPublicKey()) ? base64_encode($encrypted) : null;    
    }    
 
    /**     
     * 私钥解密     
     * @param string $encrypted     
     * @return null     
     */    
    public static function privDecrypt($encrypted = '')    
    {        
        if (!is_string($encrypted)) {            
            return null;        
        }        
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, self::getPrivateKey())) ? $decrypted : null;    
    }    
 
    /**     
     * 公钥解密     
     * @param string $encrypted     
     * @return null     
     */    
    public static function publicDecrypt($encrypted = '')    
    {        
        if (!is_string($encrypted)) {            
            return null;        
        }        
    return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, self::getPublicKey())) ? $decrypted : null;    
    }

}

/* End of file cfg_license_model.php */
/* Location: ./application/models/cfg_license_model.php */