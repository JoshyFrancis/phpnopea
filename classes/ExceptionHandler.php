<?php
error_reporting(E_ALL);
$fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
function codeToString($code){
	switch ($code) {
		case E_ERROR:
			return 'E_ERROR';
		case E_WARNING:
			return 'E_WARNING';
		case E_PARSE:
			return 'E_PARSE';
		case E_NOTICE:
			return 'E_NOTICE';
		case E_CORE_ERROR:
			return 'E_CORE_ERROR';
		case E_CORE_WARNING:
			return 'E_CORE_WARNING';
		case E_COMPILE_ERROR:
			return 'E_COMPILE_ERROR';
		case E_COMPILE_WARNING:
			return 'E_COMPILE_WARNING';
		case E_USER_ERROR:
			return 'E_USER_ERROR';
		case E_USER_WARNING:
			return 'E_USER_WARNING';
		case E_USER_NOTICE:
			return 'E_USER_NOTICE';
		case E_STRICT:
			return 'E_STRICT';
		case E_RECOVERABLE_ERROR:
			return 'E_RECOVERABLE_ERROR';
		case E_DEPRECATED:
			return 'E_DEPRECATED';
		case E_USER_DEPRECATED:
			return 'E_USER_DEPRECATED';
	}
	return 'Unknown PHP error';
}

// Register handler
set_error_handler("error_handler");
set_exception_handler("error_handler");
register_shutdown_function("error_handler");

function error_handler($code=null,$message='',$file='',$line=0){
    
    // Check for unhandled errors (fatal shutdown)
    $e=null;
    $trace=null;
	if($code instanceof Exception){//Exception
		$e=$code;
		//$trace = $e->getTrace();
		//var_dump('Exception');
	}elseif($code===null){//Shutdown
		$e = error_get_last();
		if($e){
			$e=new ErrorException($e['message'],0,$e['type'],$e['file'],$e['line']);
		}else{
			return;
		}
		//$trace = debug_backtrace();//DEBUG_BACKTRACE_IGNORE_ARGS
		//var_dump('Shutdown');
	}else{//Error
		$e=new ErrorException($message,0,$code,$file,$line);
		//$trace = debug_backtrace();//DEBUG_BACKTRACE_IGNORE_ARGS
		//var_dump('Error');
	}
	$trace = debug_backtrace();//DEBUG_BACKTRACE_IGNORE_ARGS 
	//ob_clean();
	while (ob_get_level()){
		//ob_get_clean();
		ob_end_clean();
	}
	 
	
	if (!headers_sent()) {
		http_response_code(500);
	}
	// get_class($e),
	$message=sprintf('Uncaught Exception %s: "%s" at %s line %s.<br>',codeToString($e->getCode()),$e->getMessage(), $e->getFile(), $e->getLine());
		
	
    // Output error page
    echo $message;
    var_dump($trace);
    exit;
}
