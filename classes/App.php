<?php
define('app_engine','laranopea');
class App{
	public static $classes_path;
	public static $public_path;
	public static $Kernel='Kernel.php';
	public static $file_view='';
	public static $http_path;
	public static $controllers_path;
	public static $base_path;
	public static $view_path;
	public static $file_env;
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
	public static $file_web;
	public static $routes=[];
	public static $route;
	function __construct($session_name='laranopea_session',$lifetime=(60*60)*2){
		App::$session_name=$session_name;
		App::$session_lifetime=$lifetime;
		$user_agent = $_SERVER['HTTP_USER_AGENT']; 
		if($_SERVER['HTTP_ACCEPT']==='*/*' && preg_match('/Edge/i', $user_agent) && !isset($_SERVER['HTTP_COOKIE']) ){//Microsoft Edge 42.17134.1.0(Microsoft EdgeHTML 17.17134) and without any cookie, this will break our session handling
			$public_path=App::$public_path;
			$storage_path= $public_path. '/../storage/' ;
			$path= $storage_path.'logs'  ;  
			if(!is_dir($path)){
				mkdir($path);
			}
			$file_name= 'log_'.date("d-M-Y_H-i-s",time()).'.txt';
			$file=$path.'/'.$file_name;
			$log=array('server'=>$_SERVER,'request'=>$_REQUEST);
			file_put_contents($file,json_encode($log ), FILE_APPEND);
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
		if (function_exists('parse_ini_file')){
			App::$env_data=parse_ini_file(App::$file_env,false,INI_SCANNER_RAW ) ;
		}else{
			App::$env_data=parse_ini_file_quotes_safe(App::$file_env ) ;
		}

			$app_key=isset(App::$env_data['APP_KEY'])?App::$env_data['APP_KEY']:'';// env('APP_KEY');
		if(strpos($app_key,'base64:')!==false){
			//$app_key=base64_decode(explode('base64:',$app_key)[1]);	
			$app_key=base64_decode(substr( $app_key,7) );	
		}
		App::$app_key=$app_key;

		include App::$classes_path.'Cookie.php';

		//$request = new Request;
		App::$request = new Illuminate\Http\Request;
		
		App::$route_method=$method;//strtolower(App::$request->method());
		//App::$route_path=strtolower(trim(App::$request->getPathInfo(),'/'));
		//App::$route_path= trim(App::$request->getPathInfo(),'/') ;	
		App::$route_path= App::$request->path();
		//App::$route_path=App::$request->getCurrentUri();//fastest
		 
			/*
			$f='index.php';
			$p=stripos(App::$route_path,$f);
			if($p!==false){
				App::$route_path=substr(App::$route_path,$p+strlen($f)+1);
			}
			*/
		//dd(App::$route_path);
		 
		App::$a_path1=explode('/',App::$route_path);
		App::$route_domain=App::$request->getHost();
		App::$route = new Route(App::$request);
		if(App::$file_view!==''){
			include App::$file_view;
		}else{
			include App::$classes_path.'View.php';
		}
	}
	public function load(){
			
		//@@@@@@@@@@@@@@@@@
		load_classes();
		include App::$file_web;
		App::$route=null;
	}
	public function run(){
		if(stripos( $_SERVER['REQUEST_URI'],'index_.php')!==false){
			page_not_found();
		}else{
							
			$this->load();
			
			if (defined('make_app') && make_app===true) {
				return App::$current_route;
			}
				$qs='';
				if (strpos($_SERVER['REQUEST_URI'], '?')!==false){
					$qs = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?'));
				}
				//dd(App::$current_route);
				$file=public_path().'/'.App::$route_path;
						//dd($file);
						//dd(file_exists($file));
						//dd(is_dir($file));
						//dd(substr($file,-4));
				/*
				if(file_exists($file) && !is_dir($file) && strtolower(substr($file,-4))!=='.php' ){
					$p_path=str_replace('\\','/', public_path());
					$file=str_replace('\\','/', $file);
					$r_file=str_replace($p_path,'', $file);
					//download_file($file);
					 
					//dd($file);
					//dd($p_path);
					//dd($r_file);
					//$view=new View();
					//echo  $view->file($file);		
					//exit(0); 
					
					//header('X-Sendfile: ' . $file );
					//header('X-Accel-Redirect: ' . $file );
					$mimetype=View::getContentType($file);
					//dd($mimetype);
					header("Content-Type: $mimetype");
					header("Content-Length: ".filesize($file));
					//header('Content-Disposition: attachment; filename="'.basename($file).'"');
					readfile($file); // Reading the file into the output buffer
					
					
					exit(0);
					
					$url=asset(App::$route_path).str_replace('?','&', $qs);
					//dd($url  );
					//header('Location: ' . $url );
					exit(0);
				}
				*/
				if(App::$current_route===null){
					/*
					$url=url(App::$route_path);
					
					if(stripos( $url,'index.php/')!==false){
						$url=str_replace('index.php/','',$url);
					}
					//$url=$url.App::$route_path;
					//dd($url);
					
						
					if(file_exists($file)){
						//download_file($file);
						
						//dd($url.$qs);
						header('Location: ' . $url.$qs);
						exit(0);
					}else{
						//var_dump(public_path());
						//var_dump(url(''));
						//dd(App::$route_path);
						$url=url('');
						if($url===App::$route_path){
							header('Location: ' . $url.$qs);
							exit(0);
						}else{
							
							echo '<br>';
							echo 'page_not_found';
							 exit;
							page_not_found();
						}
					}
					*/
					page_not_found();
				}else{	
						//exit;
					if(count(App::$current_route['group'])>0){						
						echo through_middleware(App::$current_route['func'],App::$current_route['args'],App::$current_route['controller_class'],App::$current_route['group']);				 
					}else{
						if(isset(App::$current_route['controller_class'])){
							echo call_user_func_array([App::$current_route['controller_class'], App::$current_route['func']], App::$current_route['args']);
						}else{
							echo call_user_func_array(App::$current_route['func'], App::$current_route['args']);
						}
					}
				}
			
			Route::$request->session->save();
			
			$this->terminate();
			
		}
	}
	public function terminate(){
		App::$env_data=null;
		App::$request=null;
		
		//App::$db=null;
	}
}
	App::$classes_path=__DIR__ .'/';
	//App::$public_path=__DIR__ .'/../public';
	App::$public_path=realpath(__DIR__ .'/../public');
	
	App::$base_path=dirname(App::$public_path);
	 
		
	include App::$classes_path.'ExceptionHandler.php';
		//throw new Exception("Just invoking the exception handler.", 2);
		
		
	App::$http_path=App::$public_path.'/../app/Http/';
	App::$controllers_path=App::$public_path.'/../app/Http/Controllers/';
	App::$view_path=App::$public_path.'/../resources/views/';

	date_default_timezone_set(date_default_timezone_get ());
	//$random_session_id=bin2hex(openssl_random_pseudo_bytes(122));
	//var_dump($random_session_id);

	include App::$classes_path.'helpers.php';
	include App::$classes_path.'ParameterBag.php';
	if(count($_FILES)>0){
		include App::$classes_path.'UploadedFile.php';
		include App::$classes_path.'FileBag.php';
	}
	include App::$classes_path.'Request.php';
	include App::$classes_path.'Illuminate_Request.php';
	include App::$classes_path.'Route.php';	
	
	App::$file_env=App::$public_path.'/../.env';
	App::$file_web=App::$public_path.'/../routes/web.php';
	 
