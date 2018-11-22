<?php
/* 			
Jesus Christ is the only savior, who will redeem you.
യേശുക്രിസ്തു മാത്രമാണ് രക്ഷകൻ , അവൻ നിങ്ങളെ വീണ്ടെടുക്കും.
ישוע המשיח היחיד שיגאל אותך.
	
 All honor and glory and thanks and praise and worship belong to him, my Lord Jesus Christ.
സകല മഹത്വവും ബഹുമാനവും സ്തോത്രവും സ്തുതിയും ആരാധനയും എന്റെ കർത്താവായ യേശുക്രിസ്തുവിന് എന്നേക്കും ഉണ്ടായിരിക്കട്ടെ.
כל הכבוד והתהילה והשבחים והשבחים שייכים לו, אדוננו ישוע המשיח.


*/
	define('make_app',true);//for laranopea
	
function class_get_protected($obj,$class=null){
	if($class===null){
		class get_protected{
			function __construct($parentObj ){
					$array =(array)$parentObj;
				foreach($array AS $key=>$val){
						$name = str_replace("\0*\0",'',$key);			
					if($name!==$key){
						$array[$name]=$val;
						unset($array[$key]);
					}
					$this->$name=$val;
				}
			}
		}
		return new get_protected($obj);
	}
	eval('class get_protected extends '.$class.'{
		function __construct($parentObj ){
			$objValues = get_object_vars($parentObj); // return array of object values
			foreach($objValues AS $key=>$value){
				 $this->$key = $value;
			}
		}
	}');
		$new_obj=new get_protected($obj);
	return $new_obj;
}

function load_app(){//$engine,$path,$class){
	
	//$path=__DIR__;
	$path='E:/Work/HR/laravel-5.4.23/public';///	'var/www/example.com/public';
	
		$uri='customer';
		$redirect_path='/work/HR/laravel-5.4.23/public/index.php';//	'/index.php';
		$new_path=str_replace($_SERVER['PHP_SELF'],$redirect_path,$_SERVER['SCRIPT_FILENAME']);
		$new_uri=substr($redirect_path,0,strrpos($redirect_path,'/')+1).$uri;
		 
		$_SERVER['SCRIPT_FILENAME']=$new_path;//	'C:/www/.../laravel-5.4.23/public/index.php';
		$_SERVER['PHP_SELF']=$redirect_path;//		'/laravel-5.4.23/public/index.php';
		$_SERVER['SCRIPT_NAME']=$redirect_path;
		$_SERVER['REQUEST_URI']=$new_uri;//			'/laravel-5.4.23/public/customer';

		$_SERVER['REQUEST_METHOD']='POST';
	
	$GLOBALS['public_path']=$path;
	include $path.'/index.php';//for laranopea
	 
	//include $path.'/../bootstrap/autoload.php';	//for laravel
	//include $path.'/../bootstrap/app.php';	//for laravel
	
		$post=[];
		$post['customerType']='1';
		$post['Customer_Name']='Test Customer 1';
		$post['CountryCode']='4-AND';
		$post['CityID']='Andorra la Vella';
		$post['Address']='Address1';
		$post['Telephone']='1234';
		$post['credit_days']='0';
		$post['InvoiceTaxable']='1';
		
	if (defined('app_engine') && app_engine==='laranopea') {
		echo 'laranopea';
		$request=Route::$request;
		
		
		$args=[];
		$args['request']=$request;
		
		//echo through_middleware($func,$args,null);//works
		//var_dump($routes['get']['rc/resource']);
		
			$user=Auth::loginUsingId(13);
			Auth::guard('user2')->login($user);
			
				$user = Auth::guard('user2')->user();
			
			Session::put('FinYearID',3);
			Session::put('BranchID',1);
		
		//var_dump($request);
		//var_dump($user);
			
				$array = (array)$user;
			foreach($array as $key=>$val) {
					$name = str_replace("\0*\0",'',$key);
				if($name!==$key){
					$array[$name]=$val;
					unset($array[$key]);
				}
			}
		var_dump($array);
		
			var_dump(url(''));
			var_dump(url()->current());

		//exit;

			//$controller_file=$path.'/../app/Http/Controllers/customer/customerController.php';//ResourceController
			//include $controller_file;
			$class = 'App\\Http\\Controllers\\customer\customerController';//ResourceController
			$controller_class=new $class() ;

		
			$request->replace($post);
		//$res=through_middleware('store',$args,$controller_class);//works
		$res= call_user_func_array([$controller_class, 'store'], $args);
		
			//var_dump($res);
		
		if(is_object($res) && $res instanceof View){
			$obj=class_get_protected($res);//,View::class);
			var_dump($obj->targetUrl);
			var_dump($obj->statusCode);
		}
		
	}else{
		echo 'laravel';
				 
				
		$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
			
		$request = Illuminate\Http\Request::createFromGlobals();
			
		$response = $kernel->handle($request);
					
			$user=Auth::loginUsingId(13);
			Auth::guard('user2')->login($user);
			
				$user = Auth::guard('user2')->user();
			
			//echo '<pre>';
			//var_dump($user);
			//echo '</pre>';
			
			Session::put('FinYearID',3);
			Session::put('BranchID',1);
					 
			//$controller_file=$path.'/../app/Http/Controllers/customer/customerController.php';
			//include $controller_file;
			$class = 'App\\Http\\Controllers\\customer\\customerController';
			$controller_class=new $class() ;
			
			$request->replace($post);

			//public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
			
			//$request->initialize([],$vars);
			//$request = self::createRequestFromFactory($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
			
			//$request->initialize($_GET, $post, array(), $_COOKIE, $_FILES, $server);
			
			//	$_POST=$post;
			//$request = Request::createFromGlobals();

			var_dump(url(''));
			var_dump(url()->current());
			//dd($request->request->all());
		//var_dump($response->getContent());
		//echo $response->getContent();
		//exit;
		$args=[];
		$args['request']=$request;
				
		$res= call_user_func_array([$controller_class, 'store'], $args);
		
		if(is_object($res) && $res instanceof Illuminate\Http\RedirectResponse){
			//var_dump($res);
			$obj=class_get_protected($res);//,Illuminate\Http\RedirectResponse::class);
			
			var_dump($obj->targetUrl);
			var_dump($obj->statusCode);
		}
			
			////echo $res;
			
		$kernel->terminate($request, $response);

	}
}

	load_app();
	
function var_debug($variable,$strlen=100,$width=25,$depth=10,$i=0,&$objects = array())
{
  $search = array("\0", "\a", "\b", "\f", "\n", "\r", "\t", "\v");
  $replace = array('\0', '\a', '\b', '\f', '\n', '\r', '\t', '\v');
 
  $string = '';
 
  switch(gettype($variable)) {
    case 'boolean':      $string.= $variable?'true':'false'; break;
    case 'integer':      $string.= $variable;                break;
    case 'double':       $string.= $variable;                break;
    case 'resource':     $string.= '[resource]';             break;
    case 'NULL':         $string.= "null";                   break;
    case 'unknown type': $string.= '???';                    break;
    case 'string':
      $len = strlen($variable);
      $variable = str_replace($search,$replace,substr($variable,0,$strlen),$count);
      $variable = substr($variable,0,$strlen);
      if ($len<$strlen) $string.= '"'.$variable.'"';
      else $string.= 'string('.$len.'): "'.$variable.'"...';
      break;
    case 'array':
      $len = count($variable);
      if ($i==$depth) $string.= 'array('.$len.') {...}';
      elseif(!$len) $string.= 'array(0) {}';
      else {
        $keys = array_keys($variable);
        $spaces = str_repeat(' ',$i*2);
        $string.= "array($len)\n".$spaces.'{';
        $count=0;
        foreach($keys as $key) {
          if ($count==$width) {
            $string.= "\n".$spaces."  ...";
            break;
          }
          $string.= "\n".$spaces."  [$key] => ";
          $string.= var_debug($variable[$key],$strlen,$width,$depth,$i+1,$objects);
          $count++;
        }
        $string.="\n".$spaces.'}';
      }
      break;
    case 'object':
      $id = array_search($variable,$objects,true);
      if ($id!==false)
        $string.=get_class($variable).'#'.($id+1).' {...}';
      else if($i==$depth)
        $string.=get_class($variable).' {...}';
      else {
        $id = array_push($objects,$variable);
        $array = (array)$variable;
        $spaces = str_repeat(' ',$i*2);
        $string.= get_class($variable)."#$id\n".$spaces.'{';
        $properties = array_keys($array);
        foreach($properties as $property) {
          $name = str_replace("\0",':',trim($property));
          $string.= "\n".$spaces."  [$name] => ";
          $string.= var_debug($array[$property],$strlen,$width,$depth,$i+1,$objects);
        }
        $string.= "\n".$spaces.'}';
      }
      break;
  }
 
  if ($i>0) return $string;
 
  $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
  do $caller = array_shift($backtrace); while ($caller && !isset($caller['file']));
  if ($caller) $string = $caller['file'].':'.$caller['line']."\n".$string;
 
  //echo $string;
  echo nl2br(str_replace(' ','&nbsp;',htmlentities($string)));

}
