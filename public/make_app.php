<?php
/* 			
Jesus Christ is the only savior, who will redeem you.
യേശുക്രിസ്തു മാത്രമാണ് രക്ഷകൻ , അവൻ നിങ്ങളെ വീണ്ടെടുക്കും.
ישוע המשיח היחיד שיגאל אותך.
	
 All honor and glory and thanks and praise and worship belong to him, my Lord Jesus Christ.
സകല മഹത്വവും ബഹുമാനവും സ്തോത്രവും സ്തുതിയും ആരാധനയും എന്റെ കർത്താവായ യേശുക്രിസ്തുവിന് എന്നേക്കും ഉണ്ടായിരിക്കട്ടെ.
כל הכבוד והתהילה והשבחים והשבחים שייכים לו, אדוננו ישוע המשיח.


*/
	define('make_app',true);

//$path=__DIR__;
$path='E:/Work/HR/laravel-5.4.23/public';
	
	//include $path.'/index.php';
	
	include $path.'/../bootstrap/autoload.php';
	include $path.'/../bootstrap/app.php';
	
	
if (defined('app_engine') && app_engine==='laranopea') {
	echo 'laranopea';
	$request=Route::$request;
	//var_dump($request);
	//var_dump($routes);
	//var_dump($routes['get']['/any']);
	
	//echo through_middleware($current_route['func'],$current_route['args'],$current_route['controller_class']);
	/* if $routes available
	if(isset($routes['get']['/any'])){
		$_SERVER['REQUEST_METHOD']='GET';
		$func=$routes['get']['/any'][1];
	}else{
		$_SERVER['REQUEST_METHOD']='POST';
		$func=$routes['post']['/any'][1];
		$request->replace(['field1'=>'data tampered']);
	}
	*/
	$args=[];
	$args['request']=$request;
	
	//echo through_middleware($func,$args,null);//works
	//var_dump($routes['get']['rc/resource']);
	
		load_laravel_classes();
		 
		
		$controller_file=$path.'/../app/Http/Controllers/ValidatorTestController.php';//ResourceController
		include $controller_file;
		$class = 'App\\Http\\Controllers\\ValidatorTestController';//ResourceController
		$controller_class=new $class() ;
	
	//var_dump($controller_class);
		$request->replace(['username'=>'user']);
		echo through_middleware('store',$args,$controller_class);//works
		
}else{
	echo 'laravel';

		$server=[];
			foreach($_SERVER as $key=>$val){
				$server[$key]=$val;
			}
				//unset($_SERVER['ORIG_SCRIPT_NAME']);
		//unset($_SERVER['ORIG_PATH_INFO']);
		//unset($_SERVER['PATH_INFO']);
		$_SERVER['SCRIPT_FILENAME']='E:/Ampps/www/work/HR/laravel-5.4.23/public/index.php';
		$_SERVER['PHP_SELF']='/work/HR/laravel-5.4.23/public/index.php';
		$_SERVER['SCRIPT_NAME']=$_SERVER['PHP_SELF'];
		$_SERVER['REQUEST_URI']='/work/HR/laravel-5.4.23/public/customer';

		$_SERVER['REQUEST_METHOD']='POST';
			
	$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
		
	
	$response = $kernel->handle(
		$request = Illuminate\Http\Request::capture()
	);
	 
	
//	$response->send();
		
		$user=Auth::loginUsingId(13);
		Auth::guard('user2')->login($user);
		
			$user = Auth::guard('user2')->user();
		//dd($user);
		//exit;
		//dd($user->ID);

		
	
		/*
		$controller_file=$path.'/../app/Http/Controllers/customer/customerController.php';
		include $controller_file;
		$class = 'App\\Http\\Controllers\\customer\\customerController';
		$controller_class=new $class() ;
		*/
	//var_dump($controller_class);
		
	//$request = new \Illuminate\Http\Request();
	//$request = new \Symfony\Component\HttpFoundation\Request();
		$post=[];
		$post['customerType']='1';
		$post['Customer_Name']='Test Customer1';
		$post['CountryCode']='4-AND';
		$post['CityID']='Andorra la Vella';
		$post['Address']='Address1';
		$post['Telephone']='1234';
		$post['credit_days']='0';
		$post['InvoiceTaxable']='1';
		
		$request->replace($post);
		//public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
		
		//$request->initialize([],$vars);
		//$request = self::createRequestFromFactory($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);
		
		//$request->initialize($_GET, $post, array(), $_COOKIE, $_FILES, $server);

		
		//	$_POST=$post;
		//$request = Request::createFromGlobals();
			//$_SERVER=$server;
		
			//dd($request);
			//dd($request->request->all());
			
	//$request =Request::createFromBase($request);
	$action = $request->route()->getAction();
		var_dump($kernel);
		var_dump($response);
		var_dump($action);
		var_dump(url(''));
		dd(url()->current());
		//dd($request->request->all());
	//var_dump($response->getContent());
	//echo $response->getContent();
	$args=[];
	$args['request']=$request;
			
	$res= call_user_func_array([$controller_class, 'store'], $args);
		
		var_dump($res);
		//echo $res;
		
	$kernel->terminate($request, $response);

}
