<?php
class ViewData implements Countable{
    protected $data = [];
    public function __construct( $data = [] ){
		$this->data=$data;
	}
    public function has($key = 'default'){
        return isset($this->data[$key]);
    }
    public function get($key)    {
        return  $this->data[$key] ;
    }
    public function put($key, $val)    {
        $this->data[$key] = $val;
        return $this;
    }
    public function remove($key )    {
        unset($this->data[$key]);
        return $this;
    }
    public function any(){
        return count($this->data) > 0;
    }
    public function count() {
        return count($this->data);
    }
	public function all() {
		return $this->data ;
    }
    public function __call($method, $parameters){
        //return $this->$method(...$parameters);
        return $this->get(...$parameters);
    }
    public function __get($key){
        return $this->get($key);
    }
    public function __set($key, $value){
        $this->put($key, $value);
    }
	public function __unset($key){
        $this->remove($key);
    }
}
class View {
    protected $view;
    protected $data;
    protected $path;
    protected $storage_path;
    protected $sections = [];
    protected $sectionStack = [];
    protected $contents='';
    public static $shared_data=[];
    public function __construct($view=null,$data = [],$sections=null,$sectionStack=null,$inner_view=false){
        $this->view = $view;
        $this->data = $data;
        $erros=isset( $this->data['errors'])?$this->data['errors']->all():[];
			unset($this->data['errors']);
		$this->data=array_merge($this->data,$data,['errors'=>new ViewData($erros)]); 
		
        if($view!==null){
			global $GLOBALS;
				$public_path=$GLOBALS['public_path'];
				$view_path=$GLOBALS['view_path'];  
				$request=$GLOBALS['request'];
			$storage_view_path= $public_path. '/../storage/views/' ;
					
				if($inner_view==false){
				
					$request->session->put('_view',$view);
				}
				
				$view2=str_replace('.','/',$view);
				
			$path = $view_path .$view2 . '.blade.php' ;
			$storage_path=$storage_view_path .$view . '_'. filemtime($path) . '.blade.php' ;
			
			$this->storage_path = $storage_path ; 
			$this->path = $path ;       

		}
			if($sections!==null){
				$this->sections=$sections;
			}
			if($sectionStack!==null){
				$this->sectionStack=$sectionStack;
			}
    }
    public static function make($view,$data = [],$inner_view=false){
		return new View($view,$data,null,null,$inner_view);
	}
	public static function share($key,$val){
		self::$shared_data=array_merge(self::$shared_data,[$key=>$val]);
	}
	public function view_make($view,$inner_view=false )    {
		return new View($view, $this->data ,$this->sections,$this->sectionStack,$inner_view) ;
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
		//var_dump($this->sections);
        return isset($this->sections[$section])? $this->sections[$section]:'';
    }
    public function render(){
		if($this->expired()){
			$contents='';
			//$contents= file_get_contents( $this->path);
			$extends='';
			$keys=[
					'{{','}}'
					,'{!!','!!}'
					,'@endsection'
					,'@else'
					,'@endif'
					,'@guest'
					,'@endguest'
					];
			$changes=[
					'<?php echo ',';?>'
					,'<?php echo ',';?>'
					,'<?php $this->stopSection(); ?>'
					,'<?php }else{ ?>'
					,'<?php } ?>'
					,'<?php if(auth()->guard()->guest()){ ?>'
					,'<?php } ?>'
					];
			//ini_set("auto_detect_line_endings", true);
			$handle = fopen($this->path, 'rb');
			if ($handle) {
				while (!feof($handle) ) {
					//while (($line = fgets($handle,65535 )) !== false) {
					while($line=stream_get_line($handle,65535,"\n")) {
						
						if(strpos($line,'@extends')!==false){
							$extends=str_replace(['@extends',')'],['<?php echo $this->view_make',',$this->data,true)->render(); ?>'],$line);
							continue;
						}
						if(strpos($line,'@include')!==false){
							$line=str_replace(['@include',')'],['<?php echo $this->view_make',',$this->data,true)->render(); ?>'],$line);
							$contents.=$line.PHP_EOL;
							continue;
						}
						if(strpos($line,'@section')!==false){
							$line=str_replace(['@section',')'],['<?php $this->startSection','); ?>'],$line);
							$contents.=$line.PHP_EOL;
							continue;
						}
						if(strpos($line,'@yield')!==false){
							$line=str_replace(['@yield',')'],['<?php echo $this->yieldContent','); ?>'],$line);
							$contents.=$line.PHP_EOL;
							continue;
						}
						if(strpos($line,'@if')!==false){
							$line=str_replace( '@if' , '<?php if' ,$line) . '{ ?>';
							$contents.=$line.PHP_EOL;
							continue;
						}
						if(strpos($line,'@elseif')!==false){
							$line=str_replace( '@elseif' , '<?php }elseif' ,$line) . '{ ?>';
							$contents.=$line.PHP_EOL;
							continue;
						} 
						//if(strpos($line,'{{{')!==false){
						//	$line=str_replace('{{{' ,'<@' ,$line);
						//}
						//if(strpos($line,'}}}')!==false){
						//	$line=str_replace('}}}' ,'@>' ,$line);
						//}
							
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
							
						 	$line=str_replace($keys ,$changes ,$line);
						 	 
						
						//if(strpos($line,'<@')!==false){
						//	$line=str_replace('<@' ,'{{{' ,$line);
						//}
						//if(strpos($line,'@>')!==false){
						//	$line=str_replace('@>' ,'}}}' ,$line);
						//}
						
						$contents.=$line. PHP_EOL ;
						
					} 
					if (!feof($handle)) {
						//echo "Error: unexpected fgets() fail\n";
					}
				}
				fclose($handle);
				$contents.=  $extends. PHP_EOL;
			}
			
			file_put_contents($this->storage_path,$contents);
		}
		
		$__path=$this->storage_path;
		
		$obLevel = ob_get_level();

        ob_start();

        //$data=array_merge($this->data,self::$shared_data);
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
    public function __tostring(){
		if($this->contents==='' && $this->view!==null){
			$this->contents=$this->render();
		}
		return $this->contents;
	}
	public function withErrors($data){
		//var_dump($data);
		$data=array_merge( isset( $this->data['errors'])?$this->data['errors']->all():[],$data);
		
		$this->data=array_merge($this->data,['errors'=>new ViewData($data)]); 
		return $this;
	}
	public function withInput(){
		global $GLOBALS;
			$request=$GLOBALS['request'];
		$erros=isset( $this->data['errors'])?$this->data['errors']->all():[];
			unset($this->data['errors']);
		$this->data=array_merge($this->data,['errors'=>new ViewData($erros)]);
		$data=array_merge($request->all(),$request->session->get('_request_data',[]) );
		$request->setInput($data);
			//var_dump($data);
		return $this;
	}
	public function with($data){
		global $GLOBALS;
			$request=$GLOBALS['request'];
		 
		$erros=isset( $this->data['errors'])?$this->data['errors']->all():[];
			unset($this->data['errors']);
		$this->data=array_merge($this->data,$data,['errors'=>new ViewData($erros)]); 
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
		//header($_SERVER['SERVER_PROTOCOL']." 301 Moved Permanently",true,301);
		//header('Status: 301 Moved Permanently', false, 301); 
		header('Location: '.$url,true,302 );
		  
		//header( 'refresh:0;url='.$url);//,true,302
		//header('Connection: close');
		//header_remove('Cache-Control');
		
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
function back(){
	global $GLOBALS;
			$request=$GLOBALS['request'];
	if($request->session->get('_view')){		
		$view = View::make($request->session->get('_view'));
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
			$request=$GLOBALS['request'];
			$routes=$GLOBALS['routes'];
			$current_route=$GLOBALS['current_route'];
			
				//if($request->session->get('_previous_url')!==$request->getUri()){
				//	$request->session->put('_request_data',$_REQUEST);
				//	$request->session->put('_previous_url',$request->getUri());			 
				//}
		$request->session->save();	
	if($route===null){
		return  new View();
	}
	$url=url($route);
	
		if($url===$request->getUri() && $request->session->get('_view')){
			//var_dump($request->session->get('_request_data'));
			//var_dump($current_route);
			//exit;
			//var_dump($request->session->get('_view')); 
			$view = View::make($request->session->get('_view'));
			return $view;
		}
		
	$view=new View();
	
	$view->redirect_url($url);
	
	return  $view;	
}
function http_response_status($code,$text){
	$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
	header($protocol . ' ' . $code . ' ' . $text);
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
