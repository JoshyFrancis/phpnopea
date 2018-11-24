<?php

class App{
	public static $public_path;
	public static $Kernel='Kernel.php';
	public static $http_path;
	public static $controllers_path;
	public static $base_path;
	public static $view_path;
	public static $env_data;
	public static $app_key;
	public static $request;
	public static $current_route=null;
	public static $route_method;
	public static $route_path;
	public static $a_path1;
	public static $route_domain;
	public static $session_name;
	public static $session_lifetime;
	public static $routes=[];
	public static $route;
	function __construct($session_name='laranopea_session',$lifetime=60){//(60*60)*2;//in seconds
		App::$session_name=$session_name;
		App::$session_lifetime=$lifetime;
		
	}
	public function load(){
		load_classes();
		include App::$public_path.'/../routes/web.php';	
		App::$route=null;
	}
	public function run(){
		if(stripos( $_SERVER['REQUEST_URI'],'index.php')!==false){
			page_not_found();
		}else{
							
			$this->load();
		 
			if (defined('make_app') && make_app===true) {
				return App::$current_route;
			}
				if(App::$current_route===null){
					page_not_found();
				}else{		 
					echo through_middleware(App::$current_route['func'],App::$current_route['args'],App::$current_route['controller_class']);				 
				}
			Route::$request->session->save();
			App::$env_data=null;
			App::$request=null;
			
			//App::$db=null;
		}
	}
}
	//App::$public_path=__DIR__ .'/../public';
	App::$public_path=realpath(__DIR__ .'/../public');
	
	App::$base_path=dirname(App::$public_path);
	include App::$public_path.'/../classes/ExceptionHandler.php';
		//throw new Exception("Just invoking the exception handler.", 2);
		if($_SERVER['HTTP_ACCEPT']==='*/*' && !isset($_SERVER['HTTP_COOKIE']) ){//Microsoft Edge 42.17134.1.0(Microsoft EdgeHTML 17.17134) and without any cookie, this will break our session handling
			//header("404 not found",true,404);
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found',true,404);
			exit();	
		}
		$method=strtolower($_SERVER['REQUEST_METHOD']);
		if($method==='post' && isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE']==='application/json'){//https://www.toptal.com/php/10-most-common-mistakes-php-programmers-make
				$_POST=json_decode(file_get_contents('php://input'),true);
			if($_POST!==null){
				$_REQUEST+=$_POST;
			}
		}
	App::$http_path=App::$public_path.'/../app/Http/';
	App::$controllers_path=App::$public_path.'/../app/Http/Controllers/';
	App::$view_path=App::$public_path.'/../resources/views/';

	date_default_timezone_set(date_default_timezone_get ());
	//$random_session_id=bin2hex(openssl_random_pseudo_bytes(122));
	//var_dump($random_session_id);

	include App::$public_path.'/../classes/helpers.php';
	include App::$public_path.'/../classes/ParameterBag.php';
	if(count($_FILES)>0){
		include App::$public_path.'/../classes/UploadedFile.php';
		include App::$public_path.'/../classes/FileBag.php';
	}
	include App::$public_path.'/../classes/Request.php';
	include App::$public_path.'/../classes/Illuminate_Request.php';
	include App::$public_path.'/../classes/Route.php';	
	
	$file_env=App::$public_path.'/../.env';
	App::$env_data=parse_ini_file($file_env,false,INI_SCANNER_RAW ) ;

		$app_key=isset(App::$env_data['APP_KEY'])?App::$env_data['APP_KEY']:'';// env('APP_KEY');
	if(strpos($app_key,'base64:')!==false){
		//$app_key=base64_decode(explode('base64:',$app_key)[1]);	
		$app_key=base64_decode(substr( $app_key,7) );	
	}
	App::$app_key=$app_key;

	include App::$public_path.'/../classes/Cookie.php';

	//$request = new Request;
	App::$request = new Illuminate\Http\Request;
	
	App::$route_method=$method;//strtolower(App::$request->method());
	//App::$route_path=strtolower(trim(App::$request->getPathInfo(),'/'));
	//App::$route_path= trim(App::$request->getPathInfo(),'/') ;	
	//App::$route_path= App::$request->path();
	App::$route_path=App::$request->getCurrentUri();//fastest
	App::$a_path1=explode('/',App::$route_path);
	App::$route_domain=App::$request->getHost();
	App::$route = new Route(App::$request);

	include App::$public_path.'/../classes/View.php';
