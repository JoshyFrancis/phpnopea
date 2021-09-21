<?php
function set_cookie($name, $value = null, $expiryTime = 0, $path = '/', $domain = null, $secureOnly = false, $httpOnly = true, $sameSiteRestriction = 'Strict') {
	 // PHP-Cookie (https://github.com/delight-im/PHP-Cookie)
	 // Copyright (c) delight.im (https://www.delight.im/)
	 // Licensed under the MIT License (https://opensource.org/licenses/MIT)
	if($domain == null){
		$domain =  $_SERVER['HTTP_HOST']  ;
	}
	// make sure that the domain is a string
	$domain = (string) $domain;
	// if the cookie should be valid for the current host only
	if ($domain === '') {
		// no need for further normalization
		return false;
	}
	// if the provided domain is actually an IP address
	if (filter_var($domain, FILTER_VALIDATE_IP) !== false) {
		// let the cookie be valid for the current host
		return false;
			// for local hostnames (which either have no dot at all or a leading dot only)
		if (strpos($domain, '.') === false || strrpos($domain, '.') === 0) {
			// let the cookie be valid for the current host while ensuring maximum compatibility
			return false;
		}
			// unless the domain already starts with a dot
		if ($domain[0] !== '.') {
			// prepend a dot for maximum compatibility (e.g. with RFC 2109)
			$domain = '.' . $domain;
		}
	}
	$secure=false;
//	if ( (isset($_SERVER['HTTPS']) && in_array(strtolower($_SERVER['HTTPS']), array('on','1' ,'ssl')) ) || intval($_SERVER['SERVER_PORT'])==443 || $_SERVER['REQUEST_SCHEME'] == "https" )		{
	if((isset($_SERVER['HTTPS']) && strpos('on,1,ssl', strtolower($_SERVER['HTTPS']) )!==false ) || intval($_SERVER['SERVER_PORT'])==443 || (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == "https") ){
		$secure=true;
	}
	$secureOnly=$secure;
	$name = (string) $name;
	// The name of a cookie must not be empty on PHP 7+ (https://bugs.php.net/bug.php?id=69523).
	if ($name !== '' || PHP_VERSION_ID < 70000) {
		if (preg_match('/[=,; \\t\\r\\n\\013\\014]/', $name)) {
			return false;
		}
	}
	$forceShowExpiry = false;
	if (empty($value)  ) {
		$value = 'deleted';
		$expiryTime = 0;
		$forceShowExpiry = true;
	}
	if ($expiryTime > 0 || $forceShowExpiry) {
		if ($expiryTime === 0) {
			$maxAgeStr=(string) 0;
		}else {
			$maxAge = $expiryTime - time();

			// The value of the `Max-Age` property must not be negative on PHP 7.0.19+ (< 7.1) and
			// PHP 7.1.5+ (https://bugs.php.net/bug.php?id=72071).
			if ((PHP_VERSION_ID >= 70019 && PHP_VERSION_ID < 70100) || PHP_VERSION_ID >= 70105) {
				if ($maxAge < 0) {
					$maxAge = 0;
				}
			}
			$maxAgeStr=(string) $maxAge;
		}
	}else {
		$maxAgeStr =null;
	}
	if ($expiryTime > 0 || $forceShowExpiry) {
		if ($forceShowExpiry) {
			$expiryTime = 1;
		}
		$expiryTimeStr = gmdate('D, d-M-Y H:i:s T', $expiryTime);
	}else {
		$expiryTimeStr = null;
	}
	$headerStr = 'Set-Cookie: ' . $name . '=' . urlencode($value);
	if (!is_null($expiryTimeStr)) {
		$headerStr .= '; expires=' . $expiryTimeStr;
	}
	// The `Max-Age` property is supported on PHP 5.5+ only (https://bugs.php.net/bug.php?id=23955).
	if (PHP_VERSION_ID >= 50500) {
		if (!is_null($maxAgeStr)) {
			$headerStr .= '; Max-Age=' . $maxAgeStr;
		}
	}
	if (!empty($path) || $path === 0) {
		$headerStr .= '; path=' . $path;
	}
	if ($secureOnly) {
		if (!empty($domain) || $domain === 0) {
			if(strpos($domain,':')!==false){
				$domain=explode(':',$domain)[0];
			}
			$headerStr .= '; domain=' . $domain;
		}
	}
	if ($secureOnly) {
		$headerStr .= '; secure';
	}
	if ($httpOnly) {
		$headerStr .= '; httponly';
	}
	if ($sameSiteRestriction === 'Lax') {
		$headerStr .= '; SameSite=Lax';
	}
	elseif ($sameSiteRestriction === 'Strict') {
		$headerStr .= strtolower( '; SameSite=Strict');
	}
	if (!headers_sent()) {
		if (!empty($headerStr)) {
			header($headerStr, false,200);
			return true;
		}
	}
		return false;
}

function decrypt_coookies(){
	$app_key=App::$app_key;
	$cookie_vars=[];
	foreach($_COOKIE as $key=>&$val){
		$value=decrypt($val,$app_key);
		//if($value!==$val){
			$val=$value;
			$cookie_vars[$key]=$value;
		//}
	}
	return $cookie_vars;
}
function cookie_exists($name){
		$found=false;
	foreach(headers_list() as $header){
		if(stripos($header,'set-cookie')!==false){				
				$parts=explode(';',$header);
			foreach($parts as &$part){
				if(stripos($part,'set-cookie')!==false){
					$keypair=explode('=',$part);
					if( $keypair[0] ===$name){
						$found=true;
						break;
					}
				}
			}
		}
		if($found==true){
			break;
		}
	}
	return $found;
}
function remove_cookie($name){
			$cookies=[];
	set_cookie($name ,'' ,-1);
	unset($_COOKIE[$name]);
		foreach(headers_list() as $header){
			if(stripos($header,'set-cookie')!==false){
				$cookies[]=$header;
			}
		}
			header_remove('Set-Cookie');
		foreach($cookies as $cookie){
			$add=true;
			$parts=explode(';',$cookie);
			foreach($parts as &$part){
				if(stripos($part,'set-cookie')!==false){
					$keypair=explode('=',$part);
					if( $keypair[0] ===$name){
						$add=false;
						break;
					}	 
				}
			}
			if($add==true){
				header($cookie, false );
			}
		}
	Route::$request->set_cookies($_COOKIE);
}
function encrypt_coookies(){
	$app_key=App::$app_key;
	if(!isset(App::$session_name)){
		return;
	}
	$session_name=App::$session_name; 
			$cookies=[];
	$date_found=false;
	$Content_Type='';
		foreach(headers_list() as $header){
			if (strpos($header, 'X-Powered-By:')!==false) {
				header_remove('X-Powered-By');
			}
			if(stripos($header,'set-cookie')!==false){
				$cookies[]=$header;
			}
			if (strpos($header, 'Date:')!==false) {
				$date_found=true;
			}
			if (strpos($header, 'Content-Type:')!==false) {
				//$Content_Type=trim( explode(':',$header)[0]);
				$Content_Type=$header;
			}
		}
		/*
			// prevent clickjacking
			header('X-Frame-Options: sameorigin');	//SAMEORIGIN		
		if(strpos($Content_Type ,'text/html')!==false){
			// prevent content sniffing (MIME sniffing)
			header('X-Content-Type-Options: nosniff');// when content-type is image IE will reject with this header
		}
			// disable caching of potentially sensitive data
			header('Cache-Control: no-store, no-cache, must-revalidate',true);
			header('Expires: Thu, 19 Nov 1981 00:00:00 GMT',true);
			header('Pragma: no-cache',true);	
		*/
			if($date_found==false){
				$now = DateTime::createFromFormat('U',time());
				$now->setTimezone(new \DateTimeZone('UTC'));
				header('Date: '.$now->format('D, d M Y H:i:s').' GMT',true);
			}
		header_remove('Set-Cookie');			 
		//$options=strtolower( 'expires,Max-Age,path,domain,secure,httponly,SameSite');
		foreach($cookies as $cookie){
			$parts=explode(';',$cookie);
			foreach($parts as &$part){
				if(stripos($part,'set-cookie')!==false){
					$keypair=explode('=',$part);
					if(is_encrypted($keypair[1])===false && strpos($keypair[0],$session_name)!==false){
						$keypair[1]=encrypt($keypair[1],$app_key) ;
					}
					$part=implode('=',$keypair);
					break;
				}
			}
			$cookie=implode(';',$parts);
			//if (!headers_sent()) {
				header($cookie,false);
			//}
		}
}
$result = header_register_callback('encrypt_coookies');
