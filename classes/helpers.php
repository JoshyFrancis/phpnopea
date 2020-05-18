<?php
/* 			
Jesus Christ is the only savior, who will redeem you.
യേശുക്രിസ്തു മാത്രമാണ് രക്ഷകൻ , അവൻ നിങ്ങളെ വീണ്ടെടുക്കും.
ישוע המשיח היחיד שיגאל אותך.
	
 All honor and glory and thanks and praise and worship belong to him, my Lord Jesus Christ.
സകല മഹത്വവും ബഹുമാനവും സ്തോത്രവും സ്തുതിയും ആരാധനയും എന്റെ കർത്താവായ യേശുക്രിസ്തുവിന് എന്നേക്കും ഉണ്ടായിരിക്കട്ടെ.
כל הכבוד והתהילה והשבחים והשבחים שייכים לו, אדוננו ישוע המשיח.


*/
	function json_encode_fast($arr){
		$data='{' ;
		$i=0;
		foreach ($arr as $key => $val) {
			if($i>0){
				$data.=',';
			}
				$data.="\"$key\":";
			switch (gettype ($val)){
				case 'boolean':
					$data.=$val?'true':'false';
				break;
				case 'integer':case 'double':
					$data.=$val ;
				break;
				case 'string':
					$data.="\"$val\"" ;
				break;
				case 'array':
					$data.= json_encode_fast($val)  ;
				break;
				default:
					$data.="\"".gettype($val)."\"" ;
				break;
						
			}
			$i+=1;
		}
		$data.='}';
		return $data;
	}
	function serialize_fast($arr){
		$data='a:'.count($arr).':{' ;
		foreach ($arr as $key => $val) {
				$data.='s:'.strlen($key).":\"$key\";";
			switch (gettype ($val)){
				case 'boolean':
					$data.=	'b:' . ($val?'1':'0').';';
				break;
				case 'integer': 
					$data.='i:' . $val.';';
				break;
				case 'double':
					$data.='d:' . $val.';';
				break;
				case 'string':
					$data.='s:'.strlen($val).":\"$val\";" ;
				break;
				case 'array':
					$data.= serialize_fast($val)  ;
				break;
				case 'object':
					$data.= serialize_fast($val)  ;
				break;
				default:
					var_dump(gettype ($val));
					var_dump($val);
					$data.='s:'.strlen($val).":\"$val\";" ;
				break;		
			}
		}
		$data.='}';
		return $data;
	}
if (!function_exists('getallheaders')){
    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return string[string] The HTTP header key/value pairs.
     */
    function getallheaders()    {
        $headers = array();
        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }
        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
        return $headers;
    }
}
function parse_ini_file_quotes_safe($f){
    $r=$null;
    $sec=$null;
    $f=@file($f);
    for($i=0;$i<@count($f);$i++){
        $newsec=0;
        $w=@trim($f[$i]);
        if($w){
         if((!$r) or ($sec)){
            if((@substr($w,0,1)=="[") and (@substr($w,-1,1))=="]") {$sec=@substr($w,1,@strlen($w)-2);$newsec=1;}
         }
         if(!$newsec){
            $w=@explode("=",$w);$k=@trim($w[0]);unset($w[0]); $v=@trim(@implode("=",$w));
            if((@substr($v,0,1)=="\"") and (@substr($v,-1,1)=="\"")) {$v=@substr($v,1,@strlen($v)-2);}
            if($sec) {$r[$sec][$k]=$v;} else {$r[$k]=$v;}
         }
        }
    }
    return $r;
}
function storage_link($target, $link){
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$mode = is_dir($target) ? 'J' : 'H';
		return exec("mklink /{$mode} \"{$link}\" \"{$target}\"");
	}
	return symlink($target, $link);
}
function storage_path($path='') {
	return App::$public_path. '/../storage/'.$path;
}
function public_path($path='') {
	return App::$public_path .($path!=''? '/'. $path:'') ;
}
function base_path($path='') {
	return App::$public_path. '/..'.($path!=''? '/'. $path:'');
}
function app_path($path='') {
	return App::$public_path .'/../app' .($path!=''? '/'. $path:'') ;
}
function array_insert_assoc (&$array, $position, $insert_array) { 
  $first_array = array_splice ($array, 0, $position); 
  //$array = array_merge ($first_array, $insert_array, $array); 
  $array = $first_array + $insert_array + $array;//better speed
}
function parse_ini_file_ext($file, $sections = false,$scanner_mode=INI_SCANNER_RAW) {
    ob_start();
    include $file;
    $str = ob_get_contents();
    ob_end_clean();
    return parse_ini_string($str, $sections,$scanner_mode);
}
function env($key,$def=null){
		$env=App::$env_data;	 
	return isset($env[$key])?$env[$key]:$def;
}
function isValidUrl($path){
	if (! preg_match('~^(#|//|https?://|mailto:|tel:)~', $path)) {
		return filter_var($path, FILTER_VALIDATE_URL) !== false;
	}
	return true;
}
function url($route=null){
	/*
	if($route!==null  ){
		//if(isset($routes['get'][$route])){
		//	$route= $routes['get'][$route][0];
		//}
		$url=Route::$request->getBaseUri();
		//if(stripos( $url,'index.php/')!==false){
		//	$url=str_replace('index.php/','',$url);
		//}
		
		
		if(stripos( $_SERVER['REQUEST_URI'],'index.php')!==false && stripos( $url,'/index.php')===false ){
			 if(substr($url,-1,1)!=='/'){
				$url.='/';
			} 
			$url=$url.'index.php';
		}
			 
		if(stripos($route.'/',$url)!==false){
			//if(substr($route,-1,1)!=='/'){
			//	$route.='/';
			//}
			 $route=trim($route);
			return $route;
		}
			
		if( substr($url,-1,1)!=='/'){
			$url.='/';
		}
		
		$route=trim($route);
		//if(substr($route,0,1)==='/' && $route!=='/'){
		//	$route=substr($route,1);
		//}
		$url.= $route ;
		
		if(substr($url,-1,1)==='/'){//&& $route!=='' && $route!=='/'
			$url=substr($url,0,strrpos($url,'/'));
		}
		
		return $url;
	}
	return Route::$request;
	*/
	if($route===null  ){
		return Route::$request;
	}
	if (isValidUrl($route)) {
		return $route;
	}
	$root = Route::$request->root();
	$route = '/'.trim($route, '/');
	
	return trim($root.$route, '/');
	 
}
function asset($path){
	$public_path=App::$public_path;
	$file=$public_path.'/'.$path;	
	
	/*

	$url=url('/');
		//if(stripos( $url,'index.php')!==false){
		//	$url=str_replace('index.php','',$url);
		//}
		if(stripos( $_SERVER['REQUEST_URI'],'index.php')!==false && stripos( $url,'/index.php')===false ){
			 if(substr($url,-1,1)!=='/'){
				$url.='/';
			} 
			$url=$url.'index.php';
		}
	return  $url  . trim( $path,'/') . '?t=' . filemtime($file) ;
	*/
	$root = Route::$request->root();
	
	
	$base_path=Route::$request->getBasePath();
	 
	$http_path=str_replace('\\','/',$public_path);
	 
	if(!empty($base_path)){
		$p=strpos($root,$base_path);
		if($p!==false){
			$root =substr($root,0,$p).$base_path;
		}

	}else{
		$base_path=Route::$request->getHost();
	}
		$p=strpos($http_path,$base_path);
		if($p!==false){
			$root .='/' .trim( substr($http_path,$p+strlen($base_path)),'/') ;
		}	
	
		/* Begin 04-Mar-2020 */
		if(stripos( $root,'index.php')!==false){
			$root=str_replace('index.php','',$root);
		}
		/* End 04-Mar-2020 */
	
	//return $base_path.'<br>'.$root.'<br>'.$path.'<br>'.$file;	
	return rtrim( $root,'/')  .'/' . trim( $path,'/') . (file_exists($file)? '?t=' . filemtime($file):'') ;
}
function request($name=null,$default=null){
	if($name!==null){
		return Route::$request->input($name,$default);
	}
	return Route::$request;
}
function encrypt($data,$key,$cipher='AES-256-CBC'){
	
	//laravel 5.4
	if (function_exists('random_bytes')) {
		$iv =random_bytes(16) ;
	}
	if (function_exists('openssl_random_pseudo_bytes')) {
		$iv =openssl_random_pseudo_bytes(16) ;
	}
	
	/*
	//laravel 5.7
	$iv=random_bytes(openssl_cipher_iv_length($cipher));
	*/
	$value=openssl_encrypt($data,$cipher,$key,0,$iv);
	$iv=base64_encode($iv);
	$mac=hash_hmac('sha256',$iv.$value,$key);
	$json=base64_encode( json_encode(['iv'=>$iv,'value'=>$value,'mac'=>$mac]));
	return rawurlencode( $json) ;
}
function decrypt($json_data,$key,$cipher='AES-256-CBC'){
	$json= json_decode( base64_decode( rawurldecode( $json_data)));
	if($json===null){
		return $json_data;
	}
	$data=openssl_decrypt( $json->value,$cipher,$key,0,base64_decode($json->iv));
	return $data;
}
function is_encrypted($json_data){
	//ctype_xdigit( $decrypted)===false
	$json= json_decode( base64_decode(  $json_data ));
	return $json!==null;
}
function csrf_token(){
		//Route::$request->session->save();
	return Route::$request->session->token();
}
function csrf_field(){
	return  '<input type="hidden" name="_token" value="'.csrf_token().'">' ;
}
function method_field($method){
	return  '<input type="hidden" name="_method" value="'.$method.'">' ;
}
function old($key, $default = ''){
	return Route::$request->input($key);
}
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}
function formatBytes($bytes, $precision = 2) {
    $units = array("b", "kb", "mb", "gb", "tb");

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . " " . $units[$pow];
}
function bytes_formatted($size){
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
function create_nonce($optional_salt=''){//https://stackoverflow.com/a/20039787/6417678
		$salt=App::$app_key;
    return hash_hmac('sha256', Route::$request->session->getId().$optional_salt, date('YmdG').$salt.$_SERVER['REMOTE_ADDR']);
}
function check_nonce($nonce, $optional_salt='',$hour=1){
		$salt=App::$app_key;
    $lasthour = date('G')-$hour<0 ? date('Ymd').(24-$hour) : date('YmdG')-$hour;   
    if (hash_hmac('sha256', Route::$request->session->getId().$optional_salt, date('YmdG').$salt.$_SERVER['REMOTE_ADDR']) == $nonce || 
        hash_hmac('sha256', Route::$request->session->getId().$optional_salt, $lasthour.$salt.$_SERVER['REMOTE_ADDR']) == $nonce){
        return true;
    } else {
        return false;
    }
}
function ajax_csrf_token( ){
		Route::$request->session->put('_current_page',$_SERVER['SCRIPT_NAME']);
		//Route::$request->session->save();
	return create_nonce($_SERVER['SCRIPT_NAME'] );
}
function check_ajax_csrf_token($nonce,$hour=1){
	return check_nonce($nonce, Route::$request->session->get('_current_page'),$hour); 
}
function load_middleware_class($public_path,$class){
	$middleware_file=$public_path.'/../'. str_replace('\\','/', $class).'.php';			 
	include  $middleware_file;					 
	return new $class() ;
}
function assoc_array_merge_diff($array1, $array2){ 
	$diff=[];
	foreach ($array1 as $key=>$value) {
		$diff[$key]=$value;
	}
	foreach ($array2 as $key=>$value) {
		if(isset($diff[$key])){
			unset($diff[$key]);
		}else{
			$diff[$key]=$value;
		}
	}
  return $diff;
}
function dd($var){
	echo '<pre>';
	//var_dump($var);
	var_dump(func_get_args ());
	echo '</pre>';
	die();
}
