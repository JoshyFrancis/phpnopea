<?php
/* 			
Jesus Christ is the only savior, who will redeem you.
യേശുക്രിസ്തു മാത്രമാണ് രക്ഷകൻ , അവൻ നിങ്ങളെ വീണ്ടെടുക്കും.
ישוע המשיח היחיד שיגאל אותך.
	
 All honor and glory and thanks and praise and worship belong to him, my Lord Jesus Christ.
സകല മഹത്വവും ബഹുമാനവും സ്തോത്രവും സ്തുതിയും ആരാധനയും എന്റെ കർത്താവായ യേശുക്രിസ്തുവിന് എന്നേക്കും ഉണ്ടായിരിക്കട്ടെ.
כל הכבוד והתהילה והשבחים והשבחים שייכים לו, אדוננו ישוע המשיח.


*/
#echo "ok";
#exit;

$start = microtime(true);

ini_set('html_errors', true);
ini_set('display_errors', 1);//very important in case of server
error_reporting(E_ALL);
/*
	include __DIR__ .'/../classes/App.php';

		// custom path
*/		
		
		include __DIR__.'/../github/laranopea/classes/App.php';
		App::$file_view=__DIR__.'/../github/laranopea/classes/View.php';
		App::$public_path=realpath(__DIR__ .'/../public');
		
		App::$http_path=App::$public_path.'/../app/Http/';
		App::$controllers_path=App::$public_path.'/../app/Http/Controllers/';
		App::$view_path=App::$public_path.'/../resources/views/';
		App::$file_env=App::$public_path.'/../.env';
		App::$file_web=App::$public_path.'/../routes/web.php';
		
			
App::$Kernel='Kernel_.php';	
$app=new App('cloudoux_session',60*60*2);
	//App::$public_path=realpath(__DIR__ .'/../public');// custom path
	$app->run();


//var_dump($request->headers->all());
//var_dump($request->session->get('_request_data'));
//var_dump($routes);
//var_dump($current_route);
//var_dump(App::$request->getBaseUri());
//var_dump(App::$request->getBaseUrl());
//var_dump(App::$request->getPathInfo());
//var_dump(App::$request->getBasePath());
//var_dump(App::$request->root());
//var_dump(App::$request->url());
//var_dump(App::$request->fullUrl());
//var_dump(App::$request->path());
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
	
 
/* 	
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
			 
		//$file= $file.'('. number_format(filesize($file)/1024,2) . 'KB)';
		 
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
	


$end = microtime(true);
$time = $end - $start;
echo('script took ' . $time . ' seconds to execute.');
//function bytes_formatted($size){
//    $unit=array('b','kb','mb','gb','tb','pb');
//    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
//}
var_dump(bytes_formatted(memory_get_peak_usage(false)));
*/
