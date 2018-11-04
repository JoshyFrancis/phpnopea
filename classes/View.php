<?php
class View{
    protected $view;
    public $data;
    protected $path;
    public $storage_path;
    protected $sections=[];
    protected $sectionStack=[];
    protected $contents='';
    public $status=200;
    protected $curly_braces_open='{{';
    protected $curly_braces_close='}}';
    protected $url='';
    public static $shared_data=[];
    public static $use_array_merge=false;//false=better speed
    public static $views=[];
    public static $views_data=[];
    public function __construct($view=null,$data=[],$sections=null,$sectionStack=null,$inner_view=false){
		 
			//View::$views[]=$this;//not used now
			View::$views_data=$data;
        $this->data=$data;
        /*
        $erros=isset( $this->data['errors'])?$this->data['errors']->all():[];
				
			if(View::$use_array_merge===true){
				$this->data=array_merge($this->data,['errors'=>new ParameterBag($erros)]);
			}else{
				unset($this->data['errors']);
				$this->data= $this->data + ['errors'=>new ParameterBag($erros)] ; 
			}
			*/
			if($inner_view===false){
				if(Route::$request->session->has('_input')){
					Route::$request->setInput(Route::$request->session->get('_input'));
				}
				$this->data+=Route::$request->session->get('_data',[]);
				$this->data+=['errors'=>new ParameterBag(Route::$request->session->get('_errors',[]))];
					
					Route::$request->session->remove('_input');
					Route::$request->session->remove('_data');
					Route::$request->session->remove('_errors');
			}
			
        $this->prepare_view($view,$inner_view);
        
			if($sections!==null){
				$this->sections=$sections;
			}
			if($sectionStack!==null){
				$this->sectionStack=$sectionStack;
			}
    }
    public function prepare_view($view,$inner_view=false){
		$this->view=$view;
		if($view!==null){
			global $GLOBALS;
				$public_path=$GLOBALS['public_path'];
				$view_path=$GLOBALS['view_path'];  
				 
			$storage_view_path= $public_path. '/../storage/views/' ;
					
				if($inner_view==false){
				
					Route::$request->session->put('_view',$view);
				}
				
				$view2=str_replace('.','/',$view);
				
			$path = $view_path .$view2 . '.blade.php' ;
			$storage_path=$storage_view_path .$view . '_'. filemtime($path) . '.blade.php' ;
			
			$this->storage_path = $storage_path ; 
			$this->path = $path ;       
		} 
    }
    public static function make($view,$data=[],$inner_view=false){
			//foreach(View::$views as $v){//not used now
			//	$data+=$v->data;
			//}
			//$data+=View::$views_data;
		return new View($view,$data,null,null,$inner_view);
	}
	public static function share($key,$val){
		if(View::$use_array_merge===true){
			View::$shared_data=array_merge(View::$shared_data,[$key=>$val]);
		}else{
			View::$shared_data=View::$shared_data + [$key=>$val];
		}
	}
	public function view_include($view,$data,$parent_view){
		return new View($view,$this->data,null,null,true);
    }
	public function view_make($view,$parent_view){
		//return new View($view, $this->data ,$this->sections,$this->sectionStack,true);
		$this->prepare_view($view,true);	
		return $this;
    }
	public function startSection($section ){
		if (ob_start()) {
			$this->sectionStack[] = $section;
		}
    }
	public function stopSection(){
        $last = array_pop($this->sectionStack);
			if(!isset($this->sections[$last])){
				$this->sections[$last]='';
			}
            $this->sections[$last]=ob_get_clean().$this->sections[$last];
        return $last;
    }
	public function yieldContent($section ){
        return isset($this->sections[$section])? $this->sections[$section]:'';
    }
    public function startParent(){
		$last=$this->stopSection();
		$this->startSection('parent_'.$last);
		return $last;
    }
    public function showParent(){
		$last=$this->stopSection();
		return $this->yieldContent('parent_'.$last);
    }
    public function compile(){
		if($this->expired()){
			//$contents= file_get_contents( $this->path);
			//$contents='';
			$extends='';
			
			$keys=[
					'{{','}}'
					,'{!!','!!}'

					];
			$changes=[
					'<?php echo ',';?>'
					,'<?php echo ',';?>'
					];
			$statement_keys=[
					'@endsection'
					,'@else'
					,'@endif'
					,'@guest'
					,'@endguest'
					,'@endforeach'
					,'@parent'
					,'@show'
					];
			$statement_changes=[
					'<?php $this->stopSection(); ?>'
					,'<?php }else{ ?>'
					,'<?php } ?>'
					,'<?php if(auth()->guard()->guest()){ ?>'
					,'<?php } ?>'
					,'<?php } ?>'
					,'<?php $this->startParent(); ?>'
					,'<?php echo $this->showParent(); ?>'
					];
			$line='';
			$line2='';
			$php=false;
			$single_line_comment=false;
			$multi_line_comment=false;
			$pos=false;
			$pos2=false; 
			//ini_set("auto_detect_line_endings", true);
			$handle = fopen($this->path, 'rb');
			$handlew = fopen($this->storage_path, 'w');
			if ($handle) {
				//while (!feof($handle) ) {
					while (($line = fgets($handle,65535 )) !== false) {
					//while($line=stream_get_line($handle,65535,"\n")) {
												
							$pos=strpos($line,'@extends');
						if($pos!==false){
							$line=substr($line, 0, $pos) .'<?php echo $this->view_make' .substr($line,  $pos+ 8 ) ;	 	
							$pos=strrpos($line, ')');
							if($pos!==false){		
								$line=substr($line, 0, $pos) . ',$this)->render(); ?>' .substr($line,  $pos+ 1 ) ;
							}
							$extends=$line;
							continue;
						}
							$pos=strpos($line,'@include');
						if($pos!==false){
							//$line=substr($line, 0, $pos) .'<?php $_view=$this->view_include' .substr($line,  $pos+ 8) ;	
							$line=substr($line, 0, $pos) .'<?php $_view=$this->view_make' .substr($line,  $pos+ 8) ;	 	
							$pos=strrpos($line, ')');
							if($pos!==false){		
								/*
								 $line=substr($line, 0, $pos) . ',get_defined_vars(),$this);$_view->compile();include $_view->storage_path; ?>' .substr($line,  $pos+ 1 ) ;
								 */
								$line=substr($line, 0, $pos) . ',$this);$_view->compile();include $_view->storage_path; ?>' .substr($line,  $pos+ 1 ) ;
							}
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
							$pos=strpos($line,'@section');
						if($pos!==false){
							$line=substr($line, 0, $pos) .'<?php $this->startSection' .substr($line,  $pos+ 8 ) ;	 	
							$pos=strrpos($line, ')');
							if($pos!==false){		
								$line=substr($line, 0, $pos) . '); ?>' .substr($line,  $pos+ 1 ) ;
							}
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
							$pos=strpos($line,'@yield');
						if($pos!==false){
							$line=substr($line, 0, $pos) .'<?php echo $this->yieldContent' .substr($line,  $pos+ 6 ) ;	 	
							$pos=strrpos($line, ')');
							if($pos!==false){		
								$line=substr($line, 0, $pos) . '); ?>' .substr($line,  $pos+ 1 ) ;
							}
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
							$pos=strpos($line,'@if');
						if($pos!==false){
							$line=substr($line, 0, $pos) .'<?php if' .substr($line,  $pos+ 3 ) ;	 	
							$pos=strrpos($line, ')');
							if($pos!==false){		
								$line=substr($line, 0, $pos) . '){ ?>' .substr($line,  $pos+ 1 ) ;
							}
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
							$pos=strpos($line,'@elseif');
						if($pos!==false){
							$line=substr($line, 0, $pos) .'<?php }elseif' .substr($line,  $pos+ 7 ) ;	 	
							$pos=strrpos($line, ')');
							if($pos!==false){		
								$line=substr($line, 0, $pos) . '){ ?>' .substr($line,  $pos+ 1 ) ;
							}
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
							$pos=strpos($line,'@foreach');
						if($pos!==false){
							$line=substr($line, 0, $pos) .'<?php foreach' .substr($line,  $pos+ 8 ) ;	 	
							$pos=strrpos($line, ')');
							if($pos!==false){		
								$line=substr($line, 0, $pos) . '){ ?>' .substr($line,  $pos+ 1 ) ;
							}
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
							$pos2=false; 
						foreach($statement_keys as $k=>$v){
								$pos=strpos($line,$v);
							if($pos!==false){
								$line=substr($line, 0, $pos) .$statement_changes[$k] .substr($line,  $pos+ strlen($v) ) ;
								//$contents.=$line;
								fwrite($handlew,$line);
								$pos2=true; 
								break;
							}
						}
						if($pos2===true){
							continue;
						}
						/*
						$count=count($changes);
						$j=0;
						for($j=0;$j<$count;$j++){
							$str=$keys[$j];
							$len=strlen($str);
							$pos=false;
							do{
								$pos = strpos($line, $str);							
								if($pos!==false && substr($line,$pos,$len)==$str ){//									 					
									$line=substr($line, 0, $pos ) . $changes[$j] .substr($line,  $pos+ $len ) ;
								}
							}while($pos!==false);
						}
						*/	
						$line2='';
						
						
							if(strpos($line,'<?php')!==false){
								$php=true;
							}
						if($php===true){// excluding comments
							 
							$single_line_comment=false;	
							if(strpos($line,'//')!==false){
								$line2=substr($line, strpos($line,'//') );
								$line=substr($line,0,strpos($line,'//'));
								$single_line_comment=true;
							}elseif(strpos($line,'/*')!==false && $multi_line_comment===false){
								$line2=substr($line, strpos($line,'/*'));
								$line=substr($line,0,strpos($line,'/*'));
								$multi_line_comment=true;
							}elseif(strpos($line,'*/')!==false && $multi_line_comment===true){
								$line2=substr($line, strpos($line,'*/'));
								$line=substr($line,0,strpos($line,'*/'));
								$multi_line_comment=false;
							}elseif($multi_line_comment===true){
								$line2=$line;
								$line='';
							}
							if(strpos($line,'?>')!==false){
								$php=false;
							}
						}
						
						if(strpos($line,'@{{')!==false){							
								$pos=false;
								$pos2=false;
							do{
								$pos = strpos($line, '@{{');							
								if($pos!==false){
									$line=substr($line, 0, $pos) .'<?php echo $this->curly_braces_open;?>' .substr($line,  $pos+ 3 ) ;	 	
									$pos2=strpos($line, '}}',$pos);
									if($pos2!==false){		
										$line=substr($line, 0, $pos2) . '<?php echo $this->curly_braces_close;?>' .substr($line,  $pos2+ 2 ) ;
									}
								}
							}while($pos!==false);	
						}
						
						 	$line=str_replace($keys ,$changes ,$line).$line2;
						 
						
						//if(strpos($line,'<@')!==false){
						//	$line=str_replace('<@' ,'{{{' ,$line);
						//}
						//if(strpos($line,'@>')!==false){
						//	$line=str_replace('@>' ,'}}}' ,$line);
						//}
						
						//$contents.=$line; 
						fwrite($handlew,$line);
					} 
					//if (!feof($handle)) {
						//echo "Error: unexpected fgets() fail\n";
					//}
				//}
				fclose($handle);
				//$contents.=$extends; 
				fwrite($handlew,$extends);
				fclose($handlew);
			}
			
			//file_put_contents($this->storage_path,$contents);
		}
	}
	public function render(){
		$this->compile();
		return $this->_render();
	}
    private function _render(){
			//use Exception;
			//use Throwable;
		$__path=$this->storage_path;
		
		$obLevel = ob_get_level();

        ob_start();

		//extract($this->data, EXTR_SKIP);//Import variables from an array into the current symbol table.
		foreach($this->data as $key=>$value){//http://php.net/manual/en/function.extract.php#115757     Surprisingly for me extract is 20%-80% slower then foreach construction. I don't really understand why, but it's so.
		    $$key = $value; 
		}
		foreach(View::$shared_data as $key=>$value){//http://php.net/manual/en/function.extract.php#115757     Surprisingly for me extract is 20%-80% slower then foreach construction. I don't really understand why, but it's so.
		    $$key = $value; 
		}
        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            include $__path;
        } catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
		//} catch (Error $e) {
        //   $this->handleViewException($e, $obLevel);
        }
        
        //return ltrim(ob_get_clean());
        return ob_get_clean();
    }
	protected function handleViewException($e, $obLevel){
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }
        
        //var_dump($e);
        //$e = new ErrorException($e->getMessage().' (View: '. $this->view .')', 0, 1, $e->getFile(), $e->getLine(), $e);
        //$e = new ErrorException($e->getMessage().' (View: '. $this->view .')', 0, 1, $e->getFile(), $e->getLine());
        //var_dump($e);
        
        //throw $e;
		error_handler($e);
		/*
		echo "Message: " . $e->getMessage();
		echo "<br>";
		echo "getCode(): " . $e->getCode();
		echo "<br>";
		echo "__toString(): " . $e->__toString();
		exit();
		*/
    }
    protected function expired(){
		$dir=dirname($this->storage_path);
		$s=basename($this->storage_path,'.blade.php');
		$t=filemtime($this->path);
		$s2=$this->view ;
		$s3=$s2.'_'.$t;
		
			$found=false;
        if ($handle = opendir($dir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$file=$dir.'/'.$entry;
					//if(!is_dir($file)){
						if(stripos($file,$s2)!==false){
							if(basename($file,'.blade.php')==$s3){
								$found=true;
							}else{
								unlink($file);
							}
							////break;
						}
					//}
				}
			}
			closedir($handle);
		}
		return !$found;
    }
    public function setContents($contents){
		$this->contents=$contents;
	}
	private function setStatus(){
		//header($_SERVER['SERVER_PROTOCOL']." 301 Moved Permanently",true,301);
		//header('Status: 301 Moved Permanently', false, 301); 
		
		  
		//header( 'refresh:0;url='.$url);//,true,302
		//header('Connection: close');
		//header_remove('Cache-Control');
		if($this->status===302){
			header('Location: '.$this->url,true,302 );
		}
	}
    public function __tostring(){
		if($this->contents==='' && $this->view!==null){
			$this->contents=$this->render();
		}
			$this->setStatus();
		return $this->contents;
	}
	public function withErrors($data){
		if($data instanceof Validator){
			$data=$data->errors();
		}
		if(View::$use_array_merge===true){
			$data=array_merge( isset( $this->data['errors'])?$this->data['errors']->all():[],$data);
		}else{
			$data= (isset( $this->data['errors'])?$this->data['errors']->all():[]) +$data;
		}
		Route::$request->session->set('_errors',$data);
		return $this;
	}
	public function withInput(){
		Route::$request->session->set('_input',Route::$request->all_input());
		return $this;
	}
	
	public function with($data,$val=null){
		clear_session_tmp();
			if(!is_array($data)){
				$data=[$data=>$val];
			}
		foreach($data as $k=>$v){
			Route::$request->session->set('tmp_'.$k,'');
			Route::$request->session->set($k,$v);
		}
		return $this;
	}
	public function __call($method,$args){
		if(substr($method,0,4)==='with'){
			$method=strtolower(substr($method,4));
			return $this->with($method,$args[0]);
		}
		return $this;
	}
	public function intended($route){
		$url=url($route);
		$this->redirect_url($url);
		return $this;
	}
	public function withHeaders($headers){
		if(!headers_sent()){
			foreach($headers as $key=>$val){
				header($key.': ' . $val);
			}
			//header('Content-Length: ' . strlen($this->contents));
		}
		return $this;
	}
	public function redirect_url($url){
		Route::$request->session->set('_back','false');
		$this->status=302;
		$this->url=$url;
		$this->setContents(sprintf('<!DOCTYPE html>
			<html>
				<head>
					<meta charset="UTF-8" />
					<meta http-equiv="refresh" content="0;url=%1$s" />

					<title>Redirecting to %1$s</title>
				</head>
				<body>
					Redirecting to <a href="%1$s">%1$s</a>.
				</body>
			</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')) );
		
	}
	public function json($json){
		//// 15 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
		$this->setContents(json_encode($json,15));
		if(!headers_sent()){
			//header('Content-Type: text/javascript');
			header('Content-Type: application/json');
			header('Content-Length: ' . strlen($this->contents));
		}
		return $this;
	}
	public function file($file,$headers=[]){
		$this->setContents(file_get_contents($file));
		return $this->withHeaders($headers);
	}
	public function back(){		
		$url=Route::$request->previous();
		$this->redirect_url($url);
		Route::$request->session->set('_back','true');
		return $this;
	}
	public function to($route){
		$url=url($route);
		$this->redirect_url($url);
		return $this;
	}
}
function http_response_status($code,$text){
	$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
	header($protocol . ' ' . $code . ' ' . $text);
}
function view($view,$data=[]){
	$view = View::make($view,$data);
	return $view;
}
function back(){
	$view=new View();
	return  $view->back();		
}
function response($content=null,$code=null){
	if($code!==null){
		http_response_status($code,$content);
	}
	$view=new View();
	$view->setContents($content);
	return $view;
}
function redirect($route=null){
	global $GLOBALS;
			$routes=$GLOBALS['routes'];
			$current_route=$GLOBALS['current_route'];
		
	if($route===null){
		return  new View();
	}
	
		//Route::$request->session()->set('backUrl',$url);
		//Route::$request->session->save();	
		/*
		if($url===Route::$request->getUri() || str_replace('.','/', Route::$request->session->get('_view'))===$route ){
			//var_dump($request->session->get('_request_data'));
			//var_dump($current_route);
			//exit;
			//var_dump($request->session->get('_view')); 
			$view = View::make(Route::$request->session->get('_view'));
			return $view;
		}
		*/
		
	$view=new View();	
	return  $view->to($route);	
}
function send_file($path){
	$out = fopen('php://output', 'wb');
	$file = fopen($path, 'rb');
	$size=filesize($path);
	$offset=0;
	
	stream_copy_to_stream($file, $out, $size, $offset);

	fclose($out);
	fclose($file);  
}
function page_not_found(){
	global $GLOBALS;
		$public_path=$GLOBALS['public_path'];
	
	http_response_code(404);
	 
	send_file($public_path. '/../classes/page_not_found.html');

}
function token_mismatch(){
	global $GLOBALS;
		$public_path=$GLOBALS['public_path'];
	
	http_response_status(419,'Authentication Timeout');
	
	//send_file($public_path. '/../classes/token_mismatch.html');	
	return file_get_contents($public_path. '/../classes/token_mismatch.html');	
}
