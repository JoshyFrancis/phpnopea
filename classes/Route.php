<?php
/* 			
Jesus Christ is the only savior, who will redeem you.
യേശുക്രിസ്തു മാത്രമാണ് രക്ഷകൻ , അവൻ നിങ്ങളെ വീണ്ടെടുക്കും.
ישוע המשיח היחיד שיגאל אותך.
	
 All honor and glory and thanks and praise and worship belong to him, my Lord Jesus Christ.
സകല മഹത്വവും ബഹുമാനവും സ്തോത്രവും സ്തുതിയും ആരാധനയും എന്റെ കർത്താവായ യേശുക്രിസ്തുവിന് എന്നേക്കും ഉണ്ടായിരിക്കട്ടെ.
כל הכבוד והתהילה והשבחים והשבחים שייכים לו, אדוננו ישוע המשיח.


*/
class Route{
	public static $middleware_stack=[];
	public static $group;
	public static $pattern=[];
	public static $request;
	public static $auth;
    function __construct($request) {
		Route::$request=$request;
    }
	public static function __callStatic($method, $parameters)    {
		//return call_user_func(__CLASS__.'::add_route', $method, $parameters );
		return add_route( $method, $parameters );
	}
	public function __call($method, $parameters){
		//return call_user_func(__CLASS__.'::add_route', $method, $parameters );
		return add_route( $method, $parameters );
    }
	function __destruct() {
        //echo "Destroying " . __CLASS__ . "\n";
		//echo "<br>";
    }
    public static function pattern($key, $pattern)    {
		Route::$pattern[$key]=$pattern ;
	}
}
function add_route($method, $parameters){
	if($method==='group'){
		$group=  $parameters[0] ;		 
		Route::$middleware_stack[]= $group ;
		call_user_func($parameters[1], $group);
		array_pop(Route::$middleware_stack);
		return;	
	}elseif($method==='match'){
		$method=implode(',',array_shift($parameters));
	}elseif($method==='delete' || $method==='put' || $method==='patch'){
		//if($request->input('_method')===strtoupper($method)){
		if(isset($_REQUEST['_method']) && $_REQUEST['_method']===strtoupper($method)){
			$method='post';
		}else{
			//App::$routes[$method][$parameters[0]]=$parameters;
			return;
		}
	}elseif($method==='resource'){
		Route::get($parameters[0], $parameters[1].'@index') ;
		//Route::get($parameters[0].'/create', $parameters[1].'@create') ;
		Route::post($parameters[0], $parameters[1].'@store') ;
		Route::get($parameters[0].'/{id}', $parameters[1].'@show') ;
		Route::get($parameters[0].'/{id}/edit', $parameters[1].'@edit') ;
		//Route::post($parameters[0].'/{id}', $parameters[1].'@update') ;
		Route::post($parameters[0].'/{id}', $parameters[1].'@destroy') ;
		return;
	}
	
			$route_method=App::$route_method;
	if($method==='any'){
		$method=$route_method;
	}			
				
	//App::$routes[$method][$parameters[0]]=$parameters;			
		
		
	//if( strpos(strtolower($method) ,$route_method)!==false || $method==='any'){
	//if(strpos($method,$route_method)!==false || $method==='any'){			
	if(strpos($method,$route_method)!==false ){
		//$path=strtolower( trim($parameters[0],'/') );
		$path=trim($parameters[0] ,'/');
			 
		Route::$group=Route::$middleware_stack; 
		$i=0;
			
				
		$username_var='';
		$username=[];
		$namespace='';
			$group_count=count(Route::$group);
		if($group_count>0){
				$domain='';
				$domain_match=true;
			for($i=$group_count-1;$i>=0;$i--){ 
					$val=Route::$group[$i];
				if( isset($val['prefix'])){
					$path=$val['prefix'] .'/'. $path;
				}
				if( isset($val['domain'])){
					if($domain!==''){
						$domain= '.'. $domain;
					}
					$domain=$val['domain']  . $domain;
				}
				if( isset($val['namespace'])){
					$namespace=$val['namespace'] .'\\'. $namespace;
				}
			}
			if($domain!==''){
				$route_domain=App::$route_domain;
					$domain_match=false;
				if(substr_count($route_domain,'.')===substr_count($domain,'.') ){
					$a1=explode('.',$domain);
					$a2=explode('.',$route_domain);
							$s1=array_shift($a1);
							$s2=array_shift($a2);
					if(implode('.',$a1)===implode('.',$a2)){
							$domain_match=true;
						if( strpos($domain,'{')!==false){
							$username_var=trim( $s1,'{}');//trim( explode('.',$domain)[0],'{}');
							$username=[$username_var=> $s2];//[$username_var=> explode('.',$route_domain)[0]];
						}
					}
				}				 
			}
			if($domain_match===false){
				return;
			}
		}
				
	//$a_path1=explode('/',$route_path);
		$a_path1=App::$a_path1;
		$a_path2=explode('/',$path);
				 		 
			$match=count($a_path1)===count($a_path2);
				/* //Optional parameters avoided to gain performance*/
			////$has_optional=false;
				foreach($a_path2 as $k=>$v){
					if( strpos($v,'?}' )!==false){//optional parameter
						$match=true;
						break;
					}
				}
				
		if($match===true){
			
			$fire_args=[];
				$has_optional=false;
				$s_path1='';
				$s_path2='';
				
			/*	
			foreach($a_path2 as $k=>$v){
				
				if( strpos($v,'?}' )!==false){//optional parameter
					$has_optional=$match;
				}
				if(!isset($a_path1[$k])){ 
					$match=$has_optional;
				//	$match=false;
					break;
				}
					$pos=strpos($v,'{' );
				if($v===$a_path1[$k] || $pos!==false ){//|| $has_optional===true     ){//
						$match=true;
					if( $pos!==false){					
						//$fire_args[$k]=$a_path1[$k];
						$fire_args[str_replace(['{','}'],'',$v)]=$a_path1[$k];//named arguments					
					}
				}else{
					$match=false;
					break;
				}
				
				
			}
			*/
			
			foreach($a_path1 as $k=>$v){
				$v2=isset($a_path2[$k])?$a_path2[$k]:'';
				if(strpos($v2,'{' )!==false){
					$s_path1.='{'.$v.'}#';
					$s_path2.='{'.$v.'}#';
					
					$fire_args[str_replace(['{','}','?'],'',$v2)]=$v;//named arguments				
				}else{
					$s_path1.=$v.'#';
					$s_path2.=$v2.'#';
				}
			}	
			$match=$s_path1==$s_path2;
			
				//echo json_encode($s_path1);
				//echo ' = ';
				//echo json_encode($s_path2);
				//echo ' # ';
				//echo implode('/',$a_path1);
				//echo '=';
				//echo implode('/',$a_path2);
				//echo json_encode($fire_args);	
				
			 
		}
		
				//echo json_encode($match);
				//echo '<br>';
		
		if($match===true){
			
			$current_route=& App::$current_route;
			if($current_route!==null){
				return;
			}
			
			//var_dump($a_path1);
			//var_dump($a_path2);	
			
				$func=$parameters[1];
					if(is_array($func)){				 
						if(isset($func['uses'])){
							$func=$func['uses'];
						}else{
							return;
						}
					}
			 
			//if(!is_object($func)){// handles controller
			if(is_string($func)){// handles controller
				$pos=strpos($func,'@');
				if($pos!==false){
					$controller=substr($func,0,$pos);
					$func=substr($func,$pos+1);
				}	
																	
					if($method==='post'){
						if(isset($_REQUEST['_method']) && $_REQUEST['_method']==='DELETE'){
							$func='destroy';
						}elseif(isset($_REQUEST['_method']) && $_REQUEST['_method']==='PUT')	{
							$func='update';
						}
					}else{
						//$action=end($a_path1);
						//$action=$a_path1[count($a_path1) - 1];	
						//$action=array_pop($a_path1);//fastest method
						if(array_pop($a_path1)==='create'){							 
							$func='create';
						}
					}
						load_laravel_classes();
					 
					$controllers_path=App::$controllers_path;
					
					//include  $controllers_path . '/Controller.php';

					$controller_file=$controllers_path.$namespace.$controller.'.php';
				 
					include str_replace(['App','\\'],['app','/'],$controller_file);
					$class = 'App\\Http\\Controllers\\'.$namespace.$controller;
					
					$controller_class=new $class() ;
					 
					$reflection = new ReflectionMethod($class, $func);
					$func_args=$reflection->getParameters();
					
			}else{//handles routes
				$controller_class=null;
				$reflection = new ReflectionFunction( $func);
				$func_args=$reflection->getParameters();				
				
			}
			$pattern_count=count(Route::$pattern);
					 
			$count_args=count($func_args);
			for($i=0;$i<$count_args;$i++){
				//if(strtolower($func_args[$i]->name)==='request'){
				if($func_args[$i]->name==='request'){
					//$request_pos= $i;						 
					array_insert_assoc($fire_args,$i,['request'=>Route::$request ]);	
				}elseif($func_args[$i]->name===$username_var){
					//$username_pos= $i;
					array_insert_assoc($fire_args,$i,$username);
				}else{				
					if($pattern_count>0){
						/*	
						foreach(Route::$pattern as $k=>$v){
							if($k===$func_args[$i]->name){
								if(!preg_match('/^'.$v.'$/', $fire_args[$k], $matches)){
									return;
								}
							}
						}
						*/
						if(isset(Route::$pattern[$func_args[$i]->name]) && !preg_match('/^'.Route::$pattern[$func_args[$i]->name].'$/', $fire_args[$func_args[$i]->name], $matches)){
							return;
						}
					}
				}
			}
						
			//$fire_args = array_values($fire_args);
			//echo $controller_class->$func(...$fire_args);
			
			//$current_route=['func'=>$func, 'args'=>$fire_args,'group'=>Route::$group];
			//$current_route=$path;
			
			//var_dump($fire_args);
					 
						//load_classes();
			
			//if($group_count>0){
				//echo call_user_func(__CLASS__.'::through_middleware',$request, $func, $fire_args,$controller_class );
				//if(isset($controller_class)){
				//	echo through_middleware($func, $fire_args,$controller_class );
				//}else{
				//	echo through_middleware($func, $fire_args );
				//}
								 
			//}else{
			//	if(isset($controller_class)){
			//		echo call_user_func_array([$controller_class, $func], $fire_args);
			//	}else{
			//		echo call_user_func_array($func, $fire_args);
			//	}
			//}
			$current_route=['func'=>$func, 'args'=>$fire_args,'controller_class'=>$controller_class,'params'=>$parameters];
			
		}
	}
}
function load_classes(){
	 
		$public_path=App::$public_path;
		$lifetime=App::$session_lifetime;
		$session_name=App::$session_name;
	
	include $public_path . '/../classes/SessionManager.php';	

		$cookie_vars= decrypt_coookies();
		 
			Route::$request->set_cookies($cookie_vars);

		$session=new SessionManager($public_path . '/../storage/sessions' , $lifetime,$session_name );
		$session->setId(Route::$request->cookies->get($session_name )  );
		$session->start(); 
		Route::$request->set_session($session);
		
		$previous_url=Route::$request->session->get('_previous_url','');
		$url=Route::$request->url();
			if ($previous_url!==$url && $_SERVER['REQUEST_METHOD']=== 'GET' && !Route::$request->ajax()){// && $request->route()){
				$previous_url=$url;
			}
		Route::$request->session->set('_previous_url',$previous_url );
		
	
	include $public_path . '/../classes/Storage.php';
	include $public_path . '/../classes/Illuminate_QueryException.php';//used in DB class
	include $public_path . '/../classes/DB.php';
		//$db=new DB();
		//App::$db=$db;
	
	include $public_path . '/../classes/Auth.php';
	include $public_path . '/../classes/Model.php';
	include $public_path . '/../classes/Illuminate_User.php';
		Route::$auth=new Auth();
	include $public_path . '/../classes/Illuminate_Session.php';	
	include $public_path . '/../classes/Session.php';
	include $public_path . '/../classes/Illuminate_VerifyCsrfToken.php';

}
function load_laravel_classes(){
		$public_path=App::$public_path;
	
	//@@@@@@@@@@@@@@ BEGIN Loading Model classes @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@	
	$dir=$public_path. '/..';
		/*
		 $dir=$public_path. '/../app/';
		if ($handle = opendir($dir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$file=$dir.'/'.$entry;
					if(!is_dir($file) && substr($entry,-4)==='.php' ){
						include $file;
					}
				}
			}
			closedir($handle);
		}
		*/
		spl_autoload_register(function ($class) use($dir){
				$class=strtolower(substr($class,0,1)) .substr($class,1);
				$file=$dir.'/'.$class.'.php';
			if(is_file($file)){
				include $file;
			}
		});
	//@@@@@@@@@@@@@@ END Loading Model classes @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@	
	
	include $public_path . '/../classes/Illuminate_Hash.php';
	include $public_path . '/../classes/Illuminate_EncryptCookies.php';
	include $public_path . '/../classes/Illuminate_TrimStrings.php';
	
		
	include $public_path . '/../classes/Validator.php';
			
		
		include $public_path . '/../classes/Illuminate_Controller.php';	
		include $public_path . '/../classes/Illuminate_AuthenticatesUsers.php';
		//include $public_path . '/../classes/Illuminate_Session.php';
		include $public_path . '/../classes/Illuminate_Auth.php';
		include $public_path . '/../classes/Illuminate_Storage.php';
	
		
}
function through_middleware($func, $fire_args,$controller_class=null){
	 	
	include App::$http_path.App::$Kernel;
		$class = 'App\\Http\\Kernel' ;
		$kernel_class=new $class() ;
		/*
		$kernel_class->middlewares=['_default'=>[] ];
		foreach($kernel_class->middleware as $class){			
			//$kernel_class->middlewares['_default'][]=load_middleware_class($public_path,$class);
			$kernel_class->middlewares['_default'][]= $class ;
		}
		foreach($kernel_class->middlewareGroups as $key=>$group){
			$kernel_class->middlewares[$key]=[];
			foreach($group as $class){
				//$kernel_class->middlewares[$key][]=load_middleware_class($public_path,$class);
				$kernel_class->middlewares[$key][]=  $class;
			}			
		}
		foreach($kernel_class->routeMiddleware as $key=>$class){
			//$kernel_class->middlewares[$key]=[load_middleware_class($public_path,$class)];
			$kernel_class->middlewares[$key]=[  $class ];
		}
			 App::$kernel_class=$kernel_class;
		//var_dump($kernel_class->middlewares);
	
		$kernel_class=App::$kernel_class;
		*/
		$public_path=App::$public_path;
	$res=null;
	$called=false;
	$middleware_args=[];
	$middleware_args[]= Route::$request;
	$middleware_args[]= function($request) use($func, $fire_args,&$res,$controller_class, &$called){
							if($res===null && error_get_last()['type']<2 && $called===false){
								if($controller_class!==null){
									$res= call_user_func_array([$controller_class, $func], $fire_args);
								}else{
									$res= call_user_func_array($func, $fire_args);
								}
								$called=true;
							}
							return $res;
						};
			//$middleware_args[]= Route::$group[count(Route::$group)-1]['middleware'][0] ;
			$middleware_args[]=null;
			 
	//$middleware_groups=[];
	//	$middleware_groups[]='_default';
	$middleware_groups='_default,';
	//foreach(Route::$group as $items){
	foreach(Route::$group as $items){
		foreach($items as $key=>$val){
			if($key==='middleware'){
				if(is_array($val)){
					foreach($val as  $item){
						//$middleware_groups[]=$item;
						$middleware_groups.=$item.',';
					}
				}else{
					//$middleware_groups[]=$val;
					$middleware_groups.=$val.',';
				}
			}
		}
	}
	//var_dump($middleware_groups);
	/*
	foreach($kernel_class->middlewares as $group=>$classes){
		//var_dump($group);
		if(in_array($group,$middleware_groups)  ){
				$middleware_args[2]=$group;
			foreach($classes as $class){
				//$class=load_middleware_class($public_path,$class);
				$middleware_file=$public_path.'/../'. str_replace('\\','/', $class).'.php';			 
				include  $middleware_file;	
				$class=new $class() ;
				
					$res2= call_user_func_array(array($class, 'handle'), $middleware_args);
				if($res!==$res2){
					$res=$res2;
				}
			}
		}
	}
	*/
	foreach($kernel_class->middleware as $class){
				$middleware_file=$public_path.'/../'. str_replace('\\','/', $class).'.php';			 
					include str_replace(['App','\\'],['app','/'],$middleware_file);	
				$class=new $class() ;	
			$res2= call_user_func_array([$class, 'handle'], $middleware_args);
		if($res!==$res2){
			$called=true; 
			$res=$res2;
		} 
	}
		foreach($kernel_class->middlewareGroups as $group=>$classes){
			//var_dump($group);
			//if(in_array($group,$middleware_groups)){
			if(strpos($middleware_groups,$group.',')!==false){
					$middleware_args[2]=$group;
				foreach($classes as $class){
					$middleware_file=$public_path.'/../'. str_replace('\\','/', $class).'.php';			 
						include str_replace(['App','\\'],['app','/'],$middleware_file);	
					$class=new $class() ;
						$res2= call_user_func_array([$class, 'handle'], $middleware_args);
					if($res!==$res2){
						$called=true;
						$res=$res2;
					}
				}
			}
		}
	foreach($kernel_class->routeMiddleware as $group=>$class){
		//if(in_array($group,$middleware_groups)){
		if(strpos($middleware_groups,$group.',')!==false){
				$middleware_file=$public_path.'/../'. str_replace('\\','/', $class).'.php';			 
					include str_replace(['App','\\'],['app','/'],$middleware_file);	
				$class=new $class() ;
			$middleware_args[2]=$group;
				$res2= call_user_func_array([$class, 'handle'], $middleware_args);
			if($res!==$res2){
				$called=true; 
				$res=$res2;
			}
			//var_dump($res2);
		}
	}
	
	//var_dump($res);
	if($res===null && error_get_last()['type']<2 && $called===false){
		//var_dump($res);
		$res=$middleware_args[1](Route::$request);
	}
	return $res;
}
