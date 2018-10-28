<?php
class View {
    protected $view;
    protected $data;
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
    public static $use_array_merge=false;//false better speed
    
    public function __construct($view=null,$data=[],$sections=null,$sectionStack=null,$inner_view=false){
        $this->data=$data;
        /*
        $erros=isset( $this->data['errors'])?$this->data['errors']->all():[];
				
			if(self::$use_array_merge===true){
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
    public static function make($view,$data = [],$inner_view=false){
		return new View($view,$data,null,null,$inner_view);
	}
	public static function share($key,$val){
		if(self::$use_array_merge===true){
			self::$shared_data=array_merge(self::$shared_data,[$key=>$val]);
		}else{
			self::$shared_data= self::$shared_data + [$key=>$val];
		}
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
            $this->sections[$last] = ob_get_clean();
        return $last;
    }
	public function yieldContent($section ){
        return isset($this->sections[$section])? $this->sections[$section]:'';
    }
    public function startParent(){
		$last=$this->stopSection();
		//$this->sections[$last]='';
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
					,'@endsection'
					,'@else'
					,'@endif'
					,'@guest'
					,'@endguest'
					,'@endforeach'
					,'@parent'
					,'@show'
					];
			$changes=[
					'<?php echo ',';?>'
					,'<?php echo ',';?>'
					,'<?php $this->stopSection(); ?>'
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
			$javascript=false;
			$single_line_comment=false;
			$multi_line_comment=false;
			 
			//ini_set("auto_detect_line_endings", true);
			$handle = fopen($this->path, 'rb');
			$handlew = fopen($this->storage_path, 'w');
			if ($handle) {
				//while (!feof($handle) ) {
					while (($line = fgets($handle,65535 )) !== false) {
					//while($line=stream_get_line($handle,65535,"\n")) {
						
						if(strpos($line,'@extends')!==false){
							$extends=str_replace(['@extends',')'],['<?php echo $this->view_make',',$this)->compile_render(); ?>'],trim($line)).PHP_EOL;
							continue;
						}
						if(strpos($line,'@include')!==false){
							/*
							 $line=str_replace(['@include',')'],['<?php echo $this->view_make',',$this)->compile_render(); ?>'],$line);
							 */
							$line2=str_replace(['@include',')'],'',trim($line));
							$line='<?php $_view=$this->view_make'.$line2.',$this);$_view->compile();include $_view->storage_path; ?>'.PHP_EOL ;
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
						if(strpos($line,'@section')!==false){
							$line=str_replace(['@section',')'],['<?php $this->startSection','); ?>'],trim($line)).PHP_EOL;
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
						if(strpos($line,'@yield')!==false){
							$line=str_replace(['@yield',')'],['<?php echo $this->yieldContent','); ?>'],trim($line)).PHP_EOL;
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
						if(strpos($line,'@if')!==false){
							$line=str_replace( '@if' , '<?php if' ,trim($line)).'{ ?>'.PHP_EOL;
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
						if(strpos($line,'@elseif')!==false){
							$line=str_replace( '@elseif' , '<?php }elseif' ,trim($line)).'{ ?>'.PHP_EOL;
							//$contents.=$line;
							fwrite($handlew,$line);
							continue;
						}
						if(strpos($line,'@foreach')!==false){
							$line=str_replace( '@foreach' , '<?php foreach' ,trim($line)).'{ ?>'.PHP_EOL;
							//$contents.=$line; 
							fwrite($handlew,$line);
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
							if(strpos($line,'<script')!==false ){
								$javascript=true;
							}
						if($php===true || $javascript===true){
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
							if(strpos($line,'</script')!==false){
								$javascript=false;
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
	public function compile_render(){
		$this->compile();
		return $this->render();
	}
    public function render(){
		
		$__path=$this->storage_path;
		
		$obLevel = ob_get_level();

        ob_start();

		//extract($this->data, EXTR_SKIP);//Import variables from an array into the current symbol table.
		foreach($this->data as $key=>$value){//http://php.net/manual/en/function.extract.php#115757     Surprisingly for me extract is 20%-80% slower then foreach construction. I don't really understand why, but it's so.
		    $$key = $value; 
		}
		foreach(self::$shared_data as $key=>$value){//http://php.net/manual/en/function.extract.php#115757     Surprisingly for me extract is 20%-80% slower then foreach construction. I don't really understand why, but it's so.
		    $$key = $value; 
		}
        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            include $__path;
        } catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
		}
        return ltrim(ob_get_clean());
    }
	protected function handleViewException(Exception $e, $obLevel){
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }
        throw $e;
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
			$this->contents=$this->compile_render();
		}
			$this->setStatus();
		return $this->contents;
	}
	public function withErrors($data){
		if($data instanceof Validator){
			$data=$data->errors();
		}
		if(self::$use_array_merge===true){
			$data=array_merge( isset( $this->data['errors'])?$this->data['errors']->all():[],$data);
		}else{
			$data= (isset( $this->data['errors'])?$this->data['errors']->all():[]) +$data;
		}
		Route::$request->session->set('_errors',$data);
		Route::$request->session->save();
		return $this;
	}
	public function withInput(){
		Route::$request->session->set('_input',Route::$request->all_input());
		Route::$request->session->save();
		return $this;
	}
	public function with($data){
		if(self::$use_array_merge===true){ 
			$data=array_merge($this->data,$data);
		}else{
			$data= $this->data+$data;
		}
		Route::$request->session->set('_data',$data);
		Route::$request->session->save();
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
}
function http_response_status($code,$text){
	$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
	header($protocol . ' ' . $code . ' ' . $text);
}
function back(){
	if(Route::$request->session->get('_view')){		
		$view = View::make(Route::$request->session->get('_view'));
		return $view;
	}	
}
function response($content,$code=null){
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
	$url=url($route);
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
	
	$view->redirect_url($url);
	
	//var_dump($view);
	//exit;
	
	return  $view;	
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
