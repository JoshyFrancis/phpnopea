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
	return '';//'Unknown PHP error';
}

// Register handler
set_error_handler("error_handler");
set_exception_handler("error_handler");
register_shutdown_function("error_handler");

function error_handler($code=null,$message='',$file='',$line=0){
    
    // Check for unhandled errors (fatal shutdown)
    $e=null;
    $trace = debug_backtrace();//DEBUG_BACKTRACE_IGNORE_ARGS 
	if($code instanceof Exception){//Exception
		$e=$code;
		$trace = $e->getTrace();
	}elseif($code===null){//Shutdown
		$e = error_get_last();
		if($e){
			$e=new ErrorException($e['message'],0,$e['type'],$e['file'],$e['line']);
		}else{
			return;
		}
		//$trace = debug_backtrace();//DEBUG_BACKTRACE_IGNORE_ARGS
	}else{//Error
		$e=new ErrorException($message,0,$code,$file,$line);
		//$trace = debug_backtrace();//DEBUG_BACKTRACE_IGNORE_ARGS
	}
	
	//ob_clean();
	while (ob_get_level()){
		//$out.=ob_get_clean();
		ob_end_clean();
	}
	 
	
	if (!headers_sent()) {
		http_response_code(500);
	}
	// get_class($e),
	$message=sprintf('Uncaught Exception %s: "%s" at %s line %s.<br>',codeToString($e->getCode()),$e->getMessage(), $e->getFile(), $e->getLine());
		
	
    // Output error page
    //echo $message;
    //var_dump($trace);
			$public_path=$GLOBALS['public_path'];
			$class_path= $public_path. '/../classes/' ;
		$base_path=dirname($public_path);
		$GLOBALS['base_path']=$base_path;
				function replace_file_mtime($file){
					global $GLOBALS;
						$base_path=$GLOBALS['base_path'];
					 
					if(($pos=strrpos($file,'_'))!==false && ($pos2=strpos($file,'.blade.php',$pos))!==false){
						$ext=substr($file,$pos2);
						$file=substr($file, 0, $pos);
						$dir=dirname($file);
						$filename=basename($file);
						$file=$dir.DIRECTORY_SEPARATOR. str_replace('.',DIRECTORY_SEPARATOR,$filename).$ext;	
					}
					if(($pos=strrpos($file,$base_path))!==false){
							//$file=str_replace($base_path,'',$file);
							$file=substr($file,$pos+strlen($base_path));
						if(($pos=strrpos($file,'\storage'))!==false || ($pos=strrpos($file,'/storage'))!==false){
							$file='\resources'. substr($file,$pos+8);
						}
					}
					return $file;
				}
    $exception=file_get_contents($class_path.'exception.html');
    $code=$e->getCode();
    $message=$e->getMessage();
    $description=$e->getMessage();
    $file=replace_file_mtime($e->getFile());
    $file_name=$file;//basename($file);
    $line=$e->getLine();
    $home=url('/');
    $back=url()->previous();
    $trace_head='
		<tr>
			<th>
				<h3 class="trace-class">
					<span class="text-muted">'.$code.'</span>
					<span class="exception_title"><abbr title="'.$file_name.'">'.$message.'</abbr></span>
				</h3>
				<p class="break-long-words trace-message"></p>
			</th>
		</tr>
	';
    $trace_body='
		<tr>
			<td>
				<span class="block trace-file-path">in 
					<a title="'.$file.'">
						<strong>'.$file_name.'</strong> line '.$line.'</a>
				</span>
			</td>
		</tr>
	';
		
	foreach($trace as $key=>$val){
		 
		$file=replace_file_mtime(isset($val['file'])?$val['file']:'');
				
		$file_name=$file;
		$line=isset($val['line'])?$val['line']:0;
		$function=isset($val['function'])?$val['function']:'';
			if($function==='error_handler'){
				continue;
			}
		$class=isset($val['class'])?$val['class']:explode('.',$file_name)[0];
		$type=isset($val['type'])?$val['type']:'';
		$args=isset($val['args'])?$val['args']:[];
		$s_args='<em>object</em>(
							<abbr title="Illuminate\Http\Request">Request</abbr>), 
							<em>object</em>(<abbr title="Closure">Closure</abbr>)
			';
			if($class==='DB' && count($args)>1){
					$sql=$args[0];
					$bindings=$args[1];
				foreach($bindings as $value){
					if(is_string($value)){
						$value="'".replace_file_mtime($value)."'";
					}elseif($value instanceof DateTimeInterface){
						$value = $value->format('Y-m-d H:i:s');
					}elseif (is_bool($value)){
						$value = (int) $value;
					}
						$pos=strpos($sql,'?');
					if($pos!==false){
						$sql=substr($sql, 0, $pos) .$value.substr($sql,  $pos+ 1 ) ;	 	
					}
				}
				$s_args='"'.$sql.';"';
			}else{
				
					$s_args='';
				foreach($args as $item){
					if($s_args!==''){
						$s_args.=',';
					}
					
					if(is_string($item)){
						$item="'".replace_file_mtime($item)."'";
					}elseif(is_numeric($item)){
					}else{
						$item='('.gettype($item).')';
					}
					$s_args.=$item;
				}
			}
		 
		$trace_body.='
			<tr>
				<td>at 
					<span class="trace-class">
						<abbr title="'.$file_name.'">'.$class.'</abbr>
						</span>
						<span class="trace-type">'.$type.'</span>
						<span class="trace-method">'.$function.'</span>(<span class="trace-arguments">'.$s_args.'</span>)
						<span class="block trace-file-path">in 
							<a title="'.$file.'">
								<strong>'.$file_name.'
								</strong> line '.$line.'</a>
						</span>
				</td>
			</tr>
		';
		 
	}
		
    $keys=['{{Code}}','{{Exception}}','{{Explanation}}','{{home}}','{{back}}','{{trace_head}}','{{trace_body}}'];
    $changes=[$code,$message,$description,$home,$back,$trace_head,$trace_body];
    echo str_replace($keys,$changes,$exception);
    exit;
}
