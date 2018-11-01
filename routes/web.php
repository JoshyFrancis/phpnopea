<?php

/*
Route::group(['middleware' => ['admin']], function () {
	Route::get('admin/dashboard', ['as'=>'admin.dashboard','uses'=>'AdminController@dashboard']);

	Route::get('admin/test', function () {
		return 'Admin Page';
		$user = auth()->user();
		dd($user);
		 
	});
	
});
Route::post('forget_password', ['as'=>'user2.auth_forgot','uses'=>'User2LoginController@forget_password']);
Route::resource('password_reset', 'password_reset\password_resetController');
*/
	include_once(app_path().'/Http/Controllers/Common/helper.php');
	
Route::get('optional/arg2/{name?}/{id?}/{c?}', function ($name = null,$id = null,$c = null) {
	var_dump($name);
	var_dump($id);
	var_dump($c);
    return 'optional'. $name;
});
$optional2=function ($name = null,$id = null,$c = null) {
	var_dump($name);
	var_dump($id);
	var_dump($c);
    return 'optional'. $name;
};
Route::get('optional2/arg2/',$optional2);
Route::get('optional2/arg2/{name}',$optional2);
Route::get('optional2/arg2/{name}/{id}',$optional2);
Route::get('optional2/arg2/{name}/{id}/{c}',$optional2);


Route::get('/routes', function ( Request $request ){ 
	 var_dump($request->url());	 
	return 'routes';
});
Route::get('/routes/index', function ( Request $request ){ 
	 var_dump($request->url());	 
	return 'routes/index';
});
Route::get('/routes/create', function ( Request $request ){ 
	 var_dump($request->url());	 
	return 'routes/create';
});
Route::get('/routes/{id}', function ($id1, Request $request ){
	var_dump($id1);
	 var_dump($request->url());	 
	return 'routes/1';
});
Route::get('/routes/{id}/edit', function ($id1, Request $request ){
	var_dump($id1);
	 var_dump($request->url());	 
	return 'routes/1/edit';
});

//return ;

Route::get('/routes/{id1}/test/{id3}/test2/{id2}', function ($id1,$id2,Request $request,$id3){///routes/1/test/3/test2/2 
	var_dump($id1);
	var_dump($id2);
	var_dump($id3);
	//var_dump($request->url());
	//var_dump($request->session->getId() );
	//var_dump($request->session->token() );
	//var_dump($request->session->get('asd',null) );
	//var_dump($request->session->get('arr',null) );
	
	return 'ok';
	$request->session->save( );
	
	$view = View::make('home',['test'=>$request->url(),'arr'=>[1,2,3,4] ]);
    return $view->render();
});



Route::group(['namespace' => 'Admin','middleware'=>['demo','web']], function(){
    Route::get('admin/news', [
        'uses' => 'NewsController@index'
    ]);

    Route::get('admin/users', [
        'uses' => 'UserController@index'
    ]);

});


Route::match(['get', 'post'], '/match', function(Request $request){
	$url=$request->url();
	if($request->getrequestMethod()=='POST'){
		return 'Mathced post<br/><a href="'.$url .'" >Back</a>';
	}else{
		
		  return "
			<form action=\"$url\" method=\"POST\"  >
				<input type=\"hidden\" name=\"field1\" value=\"form_data\" /><br/>
				Matched get
				<input type=\"button\" onclick=\"document.forms[0].submit();\" value=\"submit\" />
		   </form>
		";
	}
});
Route::any('/any', function(Request $request){
		$url=$request->url();	
	if($request->getrequestMethod()=='POST'){
		return 'Any post<br/><a href="'.$url.'" >Back</a>';
	}else{

		  return "
			<form action=\"$url\" method=\"POST\"  >
				<input type=\"hidden\" name=\"field1\" value=\"form_data\" /><br/>
				Any get
				<input type=\"button\" onclick=\"document.forms[0].submit();\" value=\"submit\" />
		   </form>
		";
	}
});
Route::get('/delete', function(Request $request){
		$url=$request->url();	
	  return "
		<form action=\"$url\" method=\"POST\"  >
			<input type=\"hidden\" name=\"_method\" value=\"DELETE\">
			<input type=\"hidden\" name=\"field1\" value=\"form_data\" /><br/>
			Delete
			<input type=\"button\" onclick=\"document.forms[0].submit();\" value=\"submit\" />
	   </form>
	";
});
Route::delete( '/delete', function(Request $request){
	$url=$request->url();	
		return 'Deleted<br/><a href="'.$url.'" >Back</a>';
});


Route::group(['prefix' => 'books'], function () {
	Route::group(['prefix' => 'shelf'], function () {
		// First Route
		Route::get('/1', function () {
			return 'Books/Shelf/1';
		});
		Route::group(['prefix' => 'store'], function () {
			// First Route
			Route::get('/1', function () {
				return 'Books/Shelf/Store/1';
			});	
		});
	});
	// Second Route
	Route::get('/1', function () {
		return 'Books 1';
	});
	Route::group(['prefix' => 'store'], function () {
		// Third Route
		Route::get('/1', function () {
			return 'Books/store/1';
		});
	});
});

Route::get('/files/{id}', function ($id) {
	
	var_dump($id);
	//var_dump( asset('storage/file.txt'));
	
	Storage::disk('local')->put('file.txt', 'Contents');
	Storage::put('files/file.txt', 'Contents2');
	$contents = Storage::get('files/file.txt');
	//$url = Storage::temporaryUrl( 'files/file.txt', now()->addMinutes(5) );
	
	//$path = $request->file('avatar')->store('avatars');
	//$path = Storage::putFile('avatars', $request->file('avatar'));
	Storage::delete('files/file.txt');
	//$path = Storage::putFileAs( 'avatars', $request->file('avatar'), $request->user()->id );

    return $contents.':'.$id;
    return $id;
});
Route::get('/files2/{id}', function (Request $request,$id) {
	var_dump($request->input('head'));
	var_dump($request->url());
	 
    return $id;
});


Route::get('/data', function (Request $request ) {
$url=$request->url();
  return "
  	<form action=\"$url?route=fileupload\" enctype=\"multipart/form-data\"  method=\"POST\"  >
        <input type=\"hidden\" name=\"field1\" value=\"form_data\" /><br />
        <input  multiple  name=\"userfile[]\" type=\"file\" /><br />
            <script type=\"text/javascript\">
               // alert(document.forms[0]['field1'].value);
            </script>
            
        <input type=\"button\" onclick=\"document.forms[0].submit();\" value=\"submit\" />
   </form>
";
});
Route::post('/data', function (Request $request ) {
 //return json_encode($request);
  //return $request->input('a');
  //var_dump($_FILES);
  		 
		if($request->hasFile('userfile')) {
			$doc=$request->file('userfile') ;
				 var_dump($doc);
			if(!is_array($doc)){
				//$filename=  $doc->getClientOriginalExtension();
				$filename=$doc->getClientOriginalName() ;
				var_dump($filename);
				//$doc->move(__DIR__ . '/../storage/app/public',$filename);
				//$path = Storage::putFileAs('avatars', $doc ,$filename );
				$filename=uniqid().'.'.$file->getClientOriginalExtension() ;
				//$path = Storage::putFileAs(  $doc ,$filename );
				$newfilename = $doc->store( );
				//var_dump($newfilename);
			}else{
				foreach($doc as $file){
					$filename=$file->getClientOriginalName() ;
					var_dump($filename);
					//$file->move(__DIR__ . '/../storage/app/public',$filename);
					//$path = Storage::putFileAs('avatars', $file ,$filename );
					$filename=uniqid().'.'.$file->getClientOriginalExtension() ;
					//$path = Storage::putFileAs(  $file ,$filename );
					//$newfilename = $file->store( );
					//$newfilename = $file->store('docs' );
					$newfilename = $file->store('docs', 'public' );//path,disk
					Storage::move($newfilename,  'docs/moved.'.$file->getClientOriginalExtension());
					var_dump($newfilename);
				}
			}
		}
 // var_dump($request->files);
  return count($request->files);
  
});
$route->get('photo', function (Request $request ) {
	//Storage::delete('avatars/logo.png' );
	//$Photo=Storage::get('logo.png' );
	//var_dump($Photo);
	//header('Content-Type: image/jpeg');
	//return $Photo;
			//$contents = Storage::get('logo.png');
			//$affected = DB::update('update employee set signature=? where EmpId = ?', [$contents, 5]);
			
		$employee = DB::select('select signature from employee where EmpId = ?',[5 ]);	
			if($employee && count($employee)>0){
				$Photo=$employee[0]->signature;
			}
		if(!isset($Photo)){
			//url('/').'/assets/No_Image_200x150.png';
			//storage_path().
			$Photo=file_get_contents(public_path().'/assets/No_Image_200x150.png');
		}
	return response($Photo)->withHeaders([
		'Content-Type' => 'image/jpeg',
	]);	 
});

	Route::post('logout', function (Request $request ) {
		auth()->logout();
		 
		return redirect('home');
	});	

View::share('shared_data', 'shared accross all views');
Route::pattern('id', '[0-9]+');// Only called if {id} is numeric.

//Route::resource('rc/resource', 'ResourceController');
//Route::any('API/rc/resource', 'ResourceController@API');
Route::get('/home', 'HomeController@index') ;

Route::get('send_error_report', function (Request $request) {
	var_dump($request->input('error_file'));
	//var_dump($request);
});	
Route::group(['middleware' => ['web']], function ( ) {
	
	Route::get('validatortest/create', 'ValidatorTestController@create');
	Route::post('validatortest', 'ValidatorTestController@store');


	Route::get('/login', 'Auth\\LoginController@index') ;
	Route::post('/login', 'Auth\\LoginController@login') ;
	
	
	//Route::get('/home', 'HomeController@index') ;
	Route::resource('rc/resource', 'ResourceController',['parameters' => [ "extra" => 'template_1' ]]);
	Route::any('API/rc/resource', 'ResourceController@API');
	Route::get('rc/resource/{id}/{id2}/m', 'ResourceController@method_test');
	
	Route::get('/test1', function ( Request $request ) {
		//var_dump($middleware1);
		//return 'middleware_web_ok' ;
		return view('home') ;
	});
	Route::group(['middleware' => ['admin']], function ( ) {
		Route::get('/test2', function ( Request $request ) {
			//var_dump($middleware1);
			//var_dump($middleware2);
			return 'middleware_web_admin_ok' ;
		});
	});
	Route::group(['middleware' => ['user']], function ( ) {
		Route::get('/test3', function ( Request $request ) {
			//var_dump($middleware1);
			//var_dump($middleware2);
			return 'middleware_web_user_ok' ;
		});
		Route::group(['middleware' => ['demo']], function ( )  {
			Route::get('/test4', function ( Request $request ) {
				//var_dump($middleware1);
				//var_dump($middleware3);
				return 'middleware_web_user_demo_ok' ;
			});
		});
	});
	Route::get('/test5', function ( Request $request )  {
		//var_dump($middleware1);
		return 'middleware_web_ok' ;
	});
});


/*
					Subdomain Routing
		
		http://www.w3programmers.com/laravel-route-groups/
		
file: C:\Windows\System32\Drivers\etc\hosts
	127.0.0.1       fakebook.dev
	127.0.0.1       asd.fakebook.dev
	127.0.0.1       qwe.fakebook.dev
	::1             localhost
File Path : /apache/conf/extra/httpd-vhosts.conf or httpd.conf
  
<VirtualHost *:80>
DocumentRoot "C:\xampp\htdocs\laranopea\public"
ServerName fakebook.dev
<directory "C:\xampp\htdocs\laranopea\public">
    Options Indexes FollowSymLinks
    AllowOverride all
    Order Deny,Allow
    Deny from all
    Allow from all
</directory>
</VirtualHost>

*/

Route::group(['domain' => 'fakebook.dev'], function(){
    Route::any('/', function(){
        return 'My own domain';
    }); 
}); 

Route::group(['domain' => '{username}.fakebook.dev'], function(){
    Route::any('/', function($username){
        return 'You visit your account: '. $username; 
    });
	$data_user = [ 
		'asd' => [ 
		   'profile' => ' a cute programmer. ', 
		   'status' => [ 'I\'m cool!', 'I\'m cool very Cool!', 'Fantastic!'] 
		 ], 
		'qwe' => [ 
		   'profile' => 'a boss programmer.' , 
		   'status' => [ 'Sweet!', 'Today is incredible!', 'Nice ..'] 
		 ] 
	];
	Route :: get ( 'profile', function ($username) use ($data_user){ 
			return $username." is a ".$data_user[$username] [ 'profile']; 
	});
	Route :: get ('status', function ($username ) { 
		return $username. ' selected no status: ' ; 
	});
	Route :: get ('status/{id}', function ($username, $id) use ($data_user){ 
		return $username. ' writes: '. $data_user [$username] ['status'] [$id]; 
	}); 
});


//return;

Route::get('/', function (Request $request) {
	var_dump($request->getHost());
	//var_dump(debug_backtrace());
	echo "<pre>";
		//debug_print_backtrace();
		// print backtrace, getting rid of repeated absolute path on each file
		$e = new Exception();
		//echo $e->getTraceAsString() ;
		//print_r(str_replace('/path/to/code/', '', $e->getTraceAsString()));
		//var_dump($e->getTraceAsString());
		
		function debug_backtrace_string() {
			$stack = '';
			$i = 1;
			$trace = debug_backtrace();
			unset($trace[0]); //Remove call to this function from stack trace
			foreach($trace as $node) {
				/*
				if(isset($node['file'])) {
					$stack .= "#$i ".  $node['file']  ."(" .$node['line']."): "; 
				}
				if(isset($node['class'])) {
					$stack .= $node['class'] . "->"; 
				}
				$stack .= $node['function'] . "()" . PHP_EOL;
				*/
				$stack .="#$i " ;
				foreach($node as $k=>$v){
					if(!is_array($v) && !is_object($v)){
						$stack .= $k . "=" .$v. "\t";
					}
				}
				$stack .=    PHP_EOL;
				$i++;
			}
			return $stack;
		} 
		echo debug_backtrace_string();
		
	echo "</pre>";
	echo "url is " , url()->current() , "\n";
	var_dump($request->url());
	var_dump($request->root());
	var_dump($_SERVER);
	var_dump($request);
  return <<<HTML
  <h1>Hello world</h1>
HTML;
});

//return ;

for($i=0;$i<10000;$i++){
	//route999/asd/test/123/test2/qwe
	Route::get('/route'.$i .'/{id}/test/{id3}/test2/{id2}', function ($id,Request $request ) use($i) {
		return $i .':' .  $id;
	});
}
