<?php
/* 			
Jesus Christ is the only savior, who will redeem you.
യേശുക്രിസ്തു മാത്രമാണ് രക്ഷകൻ , അവൻ നിങ്ങളെ വീണ്ടെടുക്കും.
ישוע המשיח היחיד שיגאל אותך.
	
 All honor and glory and thanks and praise and worship belong to him, my Lord Jesus Christ.
സകല മഹത്വവും ബഹുമാനവും സ്തോത്രവും സ്തുതിയും ആരാധനയും എന്റെ കർത്താവായ യേശുക്രിസ്തുവിന് എന്നേക്കും ഉണ്ടായിരിക്കട്ടെ.
כל הכבוד והתהילה והשבחים והשבחים שייכים לו, אדוננו ישוע המשיח.


*/

$start = microtime(true);

ini_set('html_errors', true);
ini_set('display_errors', 1);//very important in case of server
error_reporting(E_ALL);

function exception_handler($exception) {
  //echo "Uncaught exception: " , $exception->getMessage(), "\n";
  
  //array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";'));
	//$db=debug_backtrace();
	//array_walk($db,function($item, $key){
	//	echo $item['function'].'()('. (isset($item['file'])? basename($item['file']):'') .':'. (isset($item['line'])?$item['line']:'') ; 
	//});
	echo '<pre>'; 
	debug_print_backtrace();
	echo '</pre>';
}
//set_exception_handler('exception_handler');

$public_path=__DIR__;
	$GLOBALS['public_path']=$public_path;
	$GLOBALS['http_path']=$public_path. '/../app/Http/';
	$GLOBALS['controllers_path']=$public_path. '/../app/Http/Controllers/';
	$GLOBALS['view_path']=$public_path. '/../resources/views/' ;
	
//$random_session_id=bin2hex(openssl_random_pseudo_bytes(122));
//var_dump($random_session_id);

	date_default_timezone_set(date_default_timezone_get ());
	
include __DIR__ . '/../classes/helpers.php';
include __DIR__ . '/../classes/ParameterBag.php';
	if(count($_FILES)>0){
		include __DIR__ . '/../classes/UploadedFile.php';
		include __DIR__ . '/../classes/FileBag.php';
	}
include __DIR__ . '/../classes/Request.php';
include __DIR__ . '/../classes/Illuminate_Request.php';
include __DIR__ . '/../classes/Route.php';	
	
	//header('X-Powered-By:PHP/7.1.8');

$file_env=__DIR__ .  '/../.env';
/*
	$data_env=explode(chr(10), file_get_contents($file_env));
	$env_data=[];
	for($i=0;$i<count($data_env);$i++){
		$config_a=explode('=', $data_env[$i]);
		$config_name=trim( $config_a[0]);
		if(strlen($config_name)>0){
			$config_value=isset($config_a[1])? trim( $config_a[1]):null;
			$env_data[$config_name]=$config_value;
		} 
	}
*/
//$env_data=parse_ini_file_ext($file_env,false,INI_SCANNER_RAW ) ;
$env_data=parse_ini_file($file_env,false,INI_SCANNER_RAW ) ;


		$GLOBALS['env']=$env_data;
//var_dump(env('DB_HOST','127.0.0.1'));
//var_dump(env('DB_DATABASE' ));
$app_key= env('APP_KEY');
if(strpos($app_key,'base64:')!==false){
	//$app_key=base64_decode(explode('base64:',$app_key)[1]);	
	$app_key=base64_decode(substr( $app_key,7) );	
}
	$GLOBALS['app_key']=$app_key;

include __DIR__ . '/../classes/Cookie.php';	

//$request = new Request;
$request = new Illuminate\Http\Request;

$current_route=null;

	$GLOBALS['request']=$request;
	$GLOBALS['current_route']=$current_route;
	$GLOBALS['app_key']=$app_key;
	$GLOBALS['route_method']=strtolower($request->method());
	//$GLOBALS['route_path']=strtolower(trim($request->getPathInfo(),'/'));
	$GLOBALS['route_path']= trim($request->getPathInfo(),'/') ;	
	//$GLOBALS['route_path']= $request->path()  ;
	$GLOBALS['a_path1']=explode('/',$GLOBALS['route_path']);
	$GLOBALS['route_domain']=$request->getHost();
	
$cookie_vars= decrypt_coookies();
 
	$request->set_cookies($cookie_vars);

if( $request->headers->get('Accept')=='*/*' && $request->headers->get('Cookie',null)===null ){//Microsoft Edge 42.17134.1.0(Microsoft EdgeHTML 17.17134) and without any cookie, this will break our session handling
	//header("404 not found",true,404);
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found',true,404);
	exit();	
}
	$session_name='laraquick_session';	
	//$lifetime=(60*60)*2;//in seconds
	$lifetime= 60 ;//in seconds
		$GLOBALS['session_name']=$session_name;
		$GLOBALS['session_lifetime']=$lifetime;
	
////var_dump(storage_path('app/public'));
////storage_path('app/public'), public_path('storage')
//if (!file_exists(public_path('storage'))) {//or php artisan storage:link
//	storage_link(storage_path('app/public'),public_path('storage'));
//}

	
$routes=[];
$GLOBALS['routes']=$routes;
	$route = new Route();

include __DIR__ . '/../classes/View.php';

	
	
/*
	$csrf=true;
	$session_csrf_name= $request->session()->session_name.'_csrf';
	
	if(!in_array($request->method(), ['HEAD', 'GET', 'OPTIONS'])  ){
		$token = $request->has('_token') ?$request->input('_token'): $request->cookies->get($session_csrf_name);
			if($request->has('_token') && $request->cookies->has($session_csrf_name) && $request->input('_token')!==$request->cookies->get($session_csrf_name)){
				$token='';
			}
		if($token!==null){
			$csrf=hash_equals($request->session()->token(), $token) ;
		}
	}
	 
	if($csrf===false){
			$request->session()->destroy_current();
			remove_cookie($session_csrf_name);
		//echo 'token mismatch';
		echo token_mismatch();
	}else{
		set_cookie($session_csrf_name , $request->session()->token() ,time()+$request->session()->seconds);
	} 
	*/
//if($csrf==true){
	if(stripos( $_SERVER['REQUEST_URI'],'index.php')!==false){
		page_not_found();
	}else{
		
			
		include __DIR__ . '/../routes/web.php' ;	

		$route=null;
		
			if($current_route==null){
				page_not_found();
				
			}
		$env_data=null;
		$request=null;
		
		$db=null;		
	}
//}

//var_dump($request->headers->all());
//var_dump($request->session->get('_request_data'));
//var_dump($routes);
//var_dump($current_route);
//var_dump($request->getBaseUrl());
//var_dump($request->getPathInfo());
//var_dump($request->getBaseUri());
//var_dump(url('/'));
//var_dump(url('test'));	 
//var_dump(url('/routes/'));	 
//var_dump(url()->current());	
//var_dump(asset('css/app.css'));	

//var_dump(get_defined_vars());


//preg_match('/({)(.*)(})/', '{id}', $matches,PREG_OFFSET_CAPTURE); 
//var_dump($matches);
//var_dump(PHP_EOL);

//var_dump((int)true);
	
 
	
echo "<pre>";
echo "<br>";
echo "Included files";
echo "<br>";
//	print_r(get_included_files ());
	$files=get_included_files ();
echo "filess count :".count($files) ;
echo "<br>";

	foreach($files as &$file){
		 
		//$file=[ 'name'=> $file ,'size'=> number_format(filesize($file)/1024,2) . 'KB' ];
			 
		$file= $file.'('. number_format(filesize($file)/1024,2) . 'KB)';
		 
	}
	//print_r($files);

echo "<br>";
//echo "Included classes";
//echo "<br>";
//print_r(get_declared_classes());
	$classes=get_declared_classes();
echo "classes count :".count($classes) ;
echo "<br>";

echo "</pre>";
/* 	*/


$end = microtime(true);
$time = $end - $start;
echo('script took ' . $time . ' seconds to execute.');
//function bytes_formatted($size){
//    $unit=array('b','kb','mb','gb','tb','pb');
//    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
//}
var_dump(bytes_formatted(memory_get_peak_usage(false)));
