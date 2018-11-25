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

class app_loader{
	protected $_server;
	protected $_cookie;
	protected $engine;
	protected $app;
	protected $response;
	public $request;
	function __construct($engine,$path,$uri,$index='index.php',Closure $run){
		$this->engine=$engine;
			$index='/'.$index;
			$new_path=str_replace($_SERVER['PHP_SELF'],$index,$_SERVER['SCRIPT_FILENAME']);
			$new_uri=substr($index,0,strrpos($index,'/')+1).$uri;
			$this->_server=[];
		foreach($_SERVER as $key=>$val){
			$this->_server[$key]=$val;
		}
			$_request=[];
		foreach($_REQUEST as $key=>$val){
			$_request[$key]=$val;
		}
			$_get=[];
		foreach($_GET as $key=>$val){
			$_get[$key]=$val;
		}
			$_post=[];
		foreach($_POST as $key=>$val){
			$_post[$key]=$val;
		}
			$this->_cookie=[];
		foreach($_COOKIE as $key=>$val){
			$this->_cookie[$key]=$val;
		}
			$_SERVER['SCRIPT_FILENAME']=$new_path; 
			$_SERVER['PHP_SELF']=$index; 
			$_SERVER['SCRIPT_NAME']=$index;
			$_SERVER['REQUEST_URI']=$new_uri; 
			$qs=(strpos($new_uri,'?')!==false?substr($new_uri,strrpos($new_uri,'?')+1):'');
			$_SERVER['QUERY_STRING']=$qs;
				$_REQUEST=[];
				$_GET=[];
				$_POST=[];
				$_COOKIE=[];
			if($qs!==''){
				parse_str($qs,$_GET);
			}
		if($engine==='laranopea'){
			include $path .'/../classes/App.php';//for laranopea
				$app=new App();
					$app->load();
			$this->app=$app;
			$request=Route::$request;
					
		}elseif($engine==='laravel-5.4'){
			include $path.'/../bootstrap/autoload.php';	//for laravel-5.4
			include $path.'/../bootstrap/app.php';	//for laravel-5.4
			$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);			
			
			$request = Illuminate\Http\Request::createFromGlobals();
			$response = $kernel->handle($request);
			$this->app=$kernel;
			$this->response=$response;
			
		}
			$this->request=$request;
		if(call_user_func($run,$engine,$request,$this)){
			
		}
			
			$_REQUEST=$_request;
			$_GET=$_get;
			$_POST=$_post;
	}
	public function post($class,$post,$method='store',$args=[]){
		$_SERVER['REQUEST_METHOD']='POST';
		$controller_class=new $class();
		$this->request->replace($post);
			if(count($args)===0){
				$args=[$this->request];
			}
		return call_user_func_array([$controller_class, $method],$args);
	}
	public function get($class,$method='index',$args=[]){
		$_SERVER['REQUEST_METHOD']='GET';
		$controller_class=new $class();
			if(count($args)===0){
				$args=[$this->request];
			}
		return call_user_func_array([$controller_class, $method],$args);
	}
	public function terminate(){
		//if (defined('app_engine') && app_engine==='laranopea') {
		if($this->engine==='laranopea'){
			$this->app->terminate();
		//if (defined('LARAVEL_START')) {//5.7
		}elseif($this->engine==='laravel-5.4'){
			$this->app->terminate($this->request, $this->response);				
		}		
			$_SERVER=$this->_server;
			$_COOKIE=$this->_cookie;
	}
}


function test_app(){
	$app_path=readlink($_SERVER['DOCUMENT_ROOT'].'/work') . '/HR/laravel-5.4.23/public';//'/var/www/base_path/public';
	$app_index='work/HR/laravel-5.4.23/public/index.php';//'index.php';
	$app_uri='customer';
		
	//$app_engine='laravel-5.4';
	$app_engine='laranopea';
	
	$app=new app_loader($app_engine,$app_path,$app_uri,$app_index,function($engine,$request,$app){
		echo $engine;
		
		$user=Auth::loginUsingId(13);
			Auth::guard('user2')->login($user);
			
				$user = Auth::guard('user2')->user();
			
			Session::put('FinYearID',3);
			Session::put('BranchID',1);
		
		//var_dump($request);
		//var_dump($user);
							
			var_dump(url(''));
			var_dump(url()->current());

		//exit;

			//$controller_file=$path.'/../app/Http/Controllers/customer/customerController.php';//ResourceController
			//include $controller_file;
		/*
			$class = 'App\\Http\\Controllers\\customer\customerController';//ResourceController
			//$controller_class=new $class() ;
			
			$post=[];
			$post['customerType']='1';
			$post['Customer_Name']='Test Customer 1';
			$post['CountryCode']='4-AND';
			$post['CityID']='Andorra la Vella';
			$post['Address']='Address1';
			$post['Telephone']='1234';
			$post['credit_days']='0';
			$post['InvoiceTaxable']='1';
		
		//	$request->replace($post);
		//$res=through_middleware('store',$args,$controller_class);//works
		//$res= call_user_func_array([$controller_class, 'store'], [$request]);
		$res=$app->post($class,$post);
		
			//var_dump($res);
		
		if(is_object($res)){
			 if($res instanceof View){
				$obj=class_get_protected($res);//,View::class);
				var_dump($obj->targetUrl);
				var_dump($obj->statusCode);
			}elseif($res instanceof Illuminate\Http\RedirectResponse){
				//var_dump($res);
				$obj=class_get_protected($res);//,Illuminate\Http\RedirectResponse::class);
				
				var_dump($obj->targetUrl);
				var_dump($obj->statusCode);
			}
		}
		*/
	});	
		$class = 'App\\Http\\Controllers\\customer\customerController';//ResourceController
			//$controller_class=new $class() ;
			
			$post=[];
			$post['customerType']='1';
			$post['Customer_Name']='Test Customer 1';
			$post['CountryCode']='4-AND';
			$post['CityID']='Andorra la Vella';
			$post['Address']='Address1';
			$post['Telephone']='1234';
			$post['credit_days']='0';
			$post['InvoiceTaxable']='1';
		
		//	$request->replace($post);
		//$res=through_middleware('store',$args,$controller_class);//works
		//$res= call_user_func_array([$controller_class, 'store'], [$request]);
		
		$res=$app->post($class,$post);
		
			//var_dump($res);
		
		if(is_object($res)){
			 if($res instanceof View){
				$obj=class_get_protected($res);//,View::class);
				var_dump($obj->targetUrl);
				var_dump($obj->statusCode);
			}elseif($res instanceof Illuminate\Http\RedirectResponse){
				//var_dump($res);
				$obj=class_get_protected($res);//,Illuminate\Http\RedirectResponse::class);
				
				var_dump($obj->targetUrl);
				var_dump($obj->statusCode);
			}
		}
	$app->terminate();
}
	//test_app();
