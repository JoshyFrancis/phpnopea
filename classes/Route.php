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
	public static $routes=[];
	public static $middleware_stack=[];
	public static $group;
    function __construct() {
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
		//var_dump(self::$routes);
		global $GLOBALS;
			$routes=& $GLOBALS['routes'];
		$routes=self::$routes;
    }
}
function add_route($method, $parameters) {
	global $GLOBALS;
			$request=$GLOBALS['request'];
			$route_method=$GLOBALS['route_method'];		
	switch($method){
		case 'group':			
			$group=  $parameters[0] ;		 
			Route::$middleware_stack[]= $group ;
			call_user_func($parameters[1], $group);
			array_pop(Route::$middleware_stack);
			return;
		break;
		case 'match':
			$method=implode(',',array_shift($parameters));
		break;
		case 'delete':case 'put': case 'patch':
			if($request->input('_method')===strtoupper($method)){
				$method='post';
			}else{
				return;
			}
		break;
		case 'resource':			 
			Route::get($parameters[0], $parameters[1].'@index') ;
			//Route::get($parameters[0].'/create', $parameters[1].'@create') ;
			Route::post($parameters[0], $parameters[1].'@store') ;
			Route::get($parameters[0].'/{id}', $parameters[1].'@show') ;
			Route::get($parameters[0].'/{id}/edit', $parameters[1].'@edit') ;
			//Route::post($parameters[0].'/{id}', $parameters[1].'@update') ;
			Route::post($parameters[0].'/{id}', $parameters[1].'@destroy') ;
			return;
		break;
	}
	 
	Route::$group=  Route::$middleware_stack ; 
		
		$i=0;
				
	//Route::$routes[$method][$parameters[0]]=$parameters;			
	
	//if( strpos(strtolower($method) ,$route_method)!==false || $method==='any'){
	if( strpos( $method  ,$route_method)!==false || $method==='any'){			
		//$path=strtolower( trim($parameters[0],'/') );
		$path=  trim($parameters[0] ,'/')  ;
				
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
					$route_domain=$GLOBALS['route_domain'];
						$domain_match=false;
					if(substr_count($route_domain,'.')===substr_count($domain,'.') ){
							$domain_match=true;
						if( strpos($domain,'{')!==false){
							$username_var=trim( explode('.',$domain)[0],'{}');
							$username=[$username_var=> explode('.',$route_domain)[0]];
						}
					}				 
				}
				if($domain_match===false){
					return;
				}
			}
			  		
		//$a_path1=explode('/',$route_path);
			$a_path1=$GLOBALS['a_path1'];
			$a_path2=explode('/',$path);
					 
			 
				$fire_args=array();
				$match=count($a_path1)===count($a_path2);
					/* //Optional parameters avoided to gain performance
				$has_optional=false;
					foreach($a_path2 as $k=>$v){
						if( strpos($v,'?}' )!==false){//optional parameter
							$match=true;
							break;
						}
					}
					*/
			if($match===true){
					//$has_optional=false;
				foreach($a_path2 as $k=>$v){
					//if( strpos($v,'?}' )!==false){//optional parameter
					//	$has_optional=$match;
					//}
					if(!isset($a_path1[$k])){ 
					//	$match=$has_optional;
						break;
					}
					if($v===$a_path1[$k] || strpos($v,'{' )!==false){// || $has_optional===true     ){
							$match=true;
						if( strpos($v,'{' )!==false){					
							$fire_args[$k]=$a_path1[$k];
						}
					}else{
						$match=false;
						break;
					}
				}
			}
			if($match===true ){
									
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
								
						$name=array_shift($a_path1);
						$action=array_pop($a_path1);
						
						if(!empty($action) ){
							if($action==='create'){							 
								$func='create';
							}
						}
						 
						if($method==='post'){
							switch($request->input('_method')){
								case 'DELETE':
									$func=='destroy';
								break;
								case 'PUT':
									$func=='update';
								break;
							}
						}
						 
						$controllers_path=$GLOBALS['controllers_path'];
						
						include  $controllers_path . '/Controller.php';

						$controller_file=$controllers_path.$namespace.$controller.'.php';
					 
						include  $controller_file;
						$class = 'App\\Http\\Controllers\\'.$namespace.$controller;
						
						$controller_class=new $class() ;
						 
						//$reflection_class = new ReflectionClass($class);
						//$reflection = $reflection_class->getMethod($func);
						//var_dump($reflection);
						$reflection = new ReflectionMethod($class, $func);
						$func_args=$reflection->getParameters();
						
						//var_dump($func_args);
						$fire_args=array();
						$request_pos=-1;
						$username_pos=-1;
						$count_args=count($func_args);
						for($i=0;$i<$count_args;$i++){
							if(strtolower($func_args[$i]->name)==='request'){
								$request_pos= $i;						 
							}elseif( $func_args[$i]->name===$username_var){
								$username_pos= $i;	
							}else{								 						 
								$fire_args[$func_args[$i]->name ]=$action;
							}
						}
						if($request_pos!==-1){
							//array_insert_assoc($fire_args,$request_pos  ,['request'=> new Illuminate\Http\Request($request) ] );
							array_insert_assoc($fire_args,$request_pos  ,['request'=> $request ] );	
						}
						if(count($username)>0 && $username_pos!==-1){
							array_insert_assoc($fire_args,$request_pos  ,$username );
						}
						
						//$fire_args = array_values($fire_args);
						//echo $controller_class->$func(...$fire_args);
						$current_route=& $GLOBALS['current_route'];
						$current_route=['func'=>$func, 'args'=>$fire_args,'group'=>Route::$group];
						
						//var_dump($fire_args);
								//call_user_func(__CLASS__.'::load_classes');
									load_classes();
						if(count(Route::$group)>0){
							//echo call_user_func(__CLASS__.'::through_middleware',$request, $func, $fire_args,$controller_class );
							echo through_middleware($request, $func, $fire_args,$controller_class );
						}else{
							echo call_user_func_array(array($controller_class, $func), $fire_args);
						}
						 
					}
					return;
				}else{//handles routes
					 
						$reflection = new ReflectionFunction( $func);
						$func_args=$reflection->getParameters();				
						$request_pos=-1;
						$username_pos=-1;
						$count_args=count($func_args);
							//var_dump($func_args);
							
						for($i=0;$i<$count_args;$i++){ 
							if(strtolower($func_args[$i]->name)==='request'){
								$request_pos= $i; 
							}elseif( $func_args[$i]->name===$username_var){
								$username_pos= $i;
							}
						}
						
						if($request_pos!=-1){
							array_insert_assoc($fire_args,$request_pos  ,['request'=> $request ] );
						}
						if(count($username)>0 && $username_pos!==-1){
							array_insert_assoc($fire_args,$request_pos  ,$username );
						}
						 						  
						$current_route=& $GLOBALS['current_route'];
						$current_route=['func'=>$func, 'args'=>$fire_args,'group'=>Route::$group];
										 
								load_classes();
						if(count(Route::$group)>0){
							//echo call_user_func(__CLASS__.'::through_middleware',$request, $func, $fire_args );
							echo through_middleware($request, $func, $fire_args );
						}else{
							echo call_user_func_array($func, $fire_args);
						}
				}
				
			}
	}
}
function load_classes(){
	global $GLOBALS;
		$public_path=$GLOBALS['public_path'];
		$request=$GLOBALS['request'];
		$lifetime=$GLOBALS['lifetime'];
		$session_name=$GLOBALS['session_name'];
	
	include $public_path . '/../classes/SessionManager.php';	

		$session=new SessionManager($public_path . '/../storage/sessions' , $lifetime,$session_name );
		$session->setId($request->cookies->get($session_name )  );
		$session->start(); 
		$request->set_session($session);
			
	
	include $public_path . '/../classes/Storage.php';	

	include $public_path . '/../classes/DB.php';
		//$db=new DB();
		//$GLOBALS['db']=$db;
	include $public_path . '/../classes/Auth.php';
		$auth=new Auth();
		$GLOBALS['auth']=$auth;
	 

}
function through_middleware($request,$func, $fire_args,$controller_class=null){
	global $GLOBALS;
	
	include $GLOBALS['http_path'] . 'Kernel.php' ;
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
			 $GLOBALS['kernel_class']=$kernel_class;
		//var_dump($kernel_class->middlewares);
	
		$kernel_class=$GLOBALS['kernel_class'];
		*/
		$public_path=$GLOBALS['public_path'];
	$res=null;
	//$called=false;
	$middleware_args=[];
	$middleware_args[]= $request ;
	$middleware_args[]= function($request) use($func, $fire_args,& $res,$controller_class){//, &$called){
							if($res===null){// && $called===false){
								if($controller_class!==null){
									$res= call_user_func_array(array($controller_class, $func), $fire_args);
								}else{
									$res= call_user_func_array($func, $fire_args);
								}
								//$called=true;
							}
							return $res;
						};
			//$middleware_args[]= Route::$group[count(Route::$group)-1]['middleware'][0] ;
			$middleware_args[]=null;
			//var_dump($middleware_args);
	
	//var_dump(Route::$group);
	$middleware_groups=[];
		$middleware_groups[]='_default';
	//foreach(Route::$group as $items){
	foreach(Route::$group as $items){
		foreach($items as $key=>$val){
			if($key==='middleware'){
				if(is_array($val)){
					foreach($val as  $item){
						$middleware_groups[]=$item;
					}
				}else{
					$middleware_groups[]=$val;
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
					include  $middleware_file;	
				$class=new $class() ;	
			$res2= call_user_func_array(array($class, 'handle'), $middleware_args);
		if($res!==$res2){
			//$called=true; 
			$res=$res2;
		} 
	}
		foreach($kernel_class->middlewareGroups as $group=>$classes){
			//var_dump($group);
			if(in_array($group,$middleware_groups)){
					$middleware_args[2]=$group;
				foreach($classes as $class){
					$middleware_file=$public_path.'/../'. str_replace('\\','/', $class).'.php';			 
						include  $middleware_file;	
					$class=new $class() ;
						$res2= call_user_func_array(array($class, 'handle'), $middleware_args);
					if($res!==$res2){
						//$called=true;
						$res=$res2;
					}
				}
			}
		}
	foreach($kernel_class->routeMiddleware as $key=>$class){
		if(in_array($key,$middleware_groups)){
				$middleware_file=$public_path.'/../'. str_replace('\\','/', $class).'.php';			 
					include  $middleware_file;	
				$class=new $class() ;
			$middleware_args[2]=$key;
				$res2= call_user_func_array(array($class, 'handle'), $middleware_args);
			if($res!==$res2){
				//$called=true; 
				$res=$res2;
			}
			//var_dump($res2);
		}
	}
	
	//var_dump($res);
	if($res===null){// && $called===false){
		//var_dump($res);
		$res=$middleware_args[1]($request);
	}
	return $res;
}
