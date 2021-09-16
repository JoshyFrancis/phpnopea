<?php
error_reporting(E_ALL);
//error_reporting(E_STRICT);
set_error_handler("error_handler");
set_exception_handler("error_handler");
register_shutdown_function("error_handler");
function replace_file_mtime($file){
		$base_path=App::$base_path;
	 
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
function error_handler($code=null,$message='',$file='',$line=0){
    
    // Check for unhandled errors (fatal shutdown)
    $e=null;
    $trace = debug_backtrace();//DEBUG_BACKTRACE_IGNORE_ARGS 
	if($code===null){//Shutdown
		$e = error_get_last();
		if($e){
			$e=new ErrorException($e['message'],0,$e['type'],$e['file'],$e['line']);
		}else{
			return;
		}
	}elseif(is_numeric($code)){//Error
		$e=new ErrorException($message,0,$code,$file,$line);
	}else{//Exception
		$e=$code;
		$trace = $e->getTrace();
	}
	
	//ob_clean();
	while (ob_get_level()){
		//$out.=ob_get_clean();
		ob_end_clean();
	}
	 
	
	if (!headers_sent()) {
		http_response_code(500);
	}
	
    // Output error page
    //echo $message;
    //var_dump($trace);
			$public_path=App::$public_path;
			$class_path= $public_path. '/../classes/' ;
				if(App::$classes_path){
					$class_path=App::$classes_path;
				}
			$storage_path= $public_path. '/../storage/' ;
		$base_path=App::$base_path;
				
    $exception=file_get_contents($class_path.'exception.html');
    $code=$e->getCode();
    $message=str_replace($base_path,'',$e->getMessage());
    $message=str_replace($public_path,'',$message);
	$pos=strpos($message,'include');
		if($pos!==false){
			$pos=strpos($message,'(',$pos+1);
			if($pos!==false){
				$file=substr($message,$pos+1) ;
				$pos=strpos($file,')',$pos+1);
				$file=substr($file,0,$pos-1) ;
				$file_name=basename($file);
				if(($pos=strrpos($file_name,'\\'))!==false){
					$file_name=substr($file_name,  $pos+1) ;	 	
				}elseif(($pos=strrpos($file_name,'/'))!==false){
					$file_name=substr($file_name,  $pos+1) ;	 	
				}
				$message=str_replace($file,$file_name,$message);
			}
		}
    $description=$message;
    $Exception=$message;
    $subject=str_replace("'","\'",$message);
			if(strpos($Exception,'Integrity constraint violation')!==false){
				$Exception='Cannot delete this document/item, while it has references. ';
			}
			if($e instanceof PDOException){ 	
				if(strpos($Exception,':')!==false){
					$Exception=explode(':',$Exception )[1];
				}				
			}

    $file=replace_file_mtime($e->getFile());
    //$file_name=$file;//basename($file);
    $file_name=basename($file);
		if(($pos=strrpos($file_name,'\\'))!==false){
			$file_name=substr($file_name,  $pos+1) ;	 	
		}elseif(($pos=strrpos($file_name,'/'))!==false){
			$file_name=substr($file_name,  $pos+1) ;	 	
		}
    $line=$e->getLine();
    if( class_exists('Route') && Route::$request!==null){
		$url=url('/');
		$back=Route::$request->previous();
		$error_dispaly=Route::$request->ajax()?'':'none';
	}else{
		$url='';
		$back='';
		$error_dispaly='';
	}

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
	if ( !function_exists(  'is_iterable' ) ){
		function is_iterable( $obj ){
			return is_array( $obj ) || ( is_object( $obj ) && ( $obj instanceof \Traversable ) );
		}
	}	
	foreach($trace as $key=>$val){
		 
		$file=replace_file_mtime(isset($val['file'])?$val['file']:'');
				
		//$file_name=$file;
		$file_name=basename($file);
				if(($pos=strrpos($file_name,'\\'))!==false){
					$file_name=substr($file_name,  $pos+1) ;	 	
				}elseif(($pos=strrpos($file_name,'/'))!==false){
					$file_name=substr($file_name,  $pos+1) ;	 	
				}
		$line=isset($val['line'])?$val['line']:0;
		$function=isset($val['function'])?$val['function']:'';
			if($function==='error_handler'){
				continue;
			}
				$s_name=$file_name;
				//if(($pos=strrpos($file_name,'\\'))!==false){
				//	$s_name=substr($file_name,  $pos+1) ;	 	
				//}elseif(($pos=strrpos($file_name,'/')+1)!==false){
				//	$s_name=substr($file_name,  $pos) ;	 	
				//}
				
		$class=isset($val['class'])?$val['class']:explode('.',$s_name)[0];
		$type=isset($val['type'])?$val['type']:'';
		$args=isset($val['args'])?$val['args']:[];
		$s_args='<em>object</em>(
							<abbr title="Phpnopea\Http\Request">Request</abbr>), 
							<em>object</em>(<abbr title="Closure">Closure</abbr>)
			';
			if($class==='DB' && count($args)>1 ){
					$sql=$args[0];
				if(is_string($sql)){
						$bindings=$args[1];
					if(is_iterable($bindings) ){
						foreach($bindings as $value){
							if(is_string($value)){
								$value="'".$value."'";
							}elseif($value instanceof DateTimeInterface){
								$value="'".$value->format('Y-m-d H:i:s')."'";
							}elseif(is_bool($value)){
								$value=(int)$value;
							}elseif(is_object($value)){
								$value=get_class($value);
							}elseif(is_numeric($value)){
								$value=$value;
							}else{
								$value=gettype($value);
							}
							
									$pos=strpos($sql,'?');
								if($pos!==false){
									$sql=substr($sql, 0, $pos) .$value.substr($sql,  $pos+ 1 ) ;	 	
								}
							
						}
					}
					$s_args='"'.$sql.';"';
				}else{
					$s_args=is_object($sql)?get_class($sql):gettype($sql);
				}
			}else{
				
					$s_args='';
				foreach($args as $item){
					if($s_args!==''){
						$s_args.=',';
					}
					
					if(is_string($item)){
						$item=replace_file_mtime($item);
						$item=basename($item);
						if(($pos=strrpos($item,'\\'))!==false){
							$item=substr($item,  $pos+1) ;	 	
						}elseif(($pos=strrpos($item,'/'))!==false){
							$item=substr($item,  $pos+1) ;	 	
						}
						$item="'".$item."'";
					}elseif(is_numeric($item)){
						
					}else{
						$item='('.gettype($item).')';
					}
					$s_args.=$item;
				}
			}
		 
		$trace_body.='
			<tr>
				<td>at <span class="trace-class"><abbr title="'.$file_name.'">'.$class.'</abbr></span>
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
		$file_name= 'error_'.$code.'_' .date("d-M-Y_H-i-s",time()).'.html';
		$path= $storage_path.'errors'  ;  
			if(!is_dir($path)){
				mkdir($path);
			}
		
    $keys=['{{Code}}','{{Exception}}','{{Explanation}}','{{url}}','{{back}}','{{trace_head}}','{{trace_body}}','{{error_file}}','{{subject}}'];
    $changes=[$code,$Exception,$description,$url,$back,$trace_head,$trace_body,$file_name,$subject];
    $html=str_replace($keys,$changes,$exception);
    echo $html;
			$file=$path.'/'.$file_name;	
		file_put_contents($file,$html);
    exit;
}
