<?php
class View{
    protected $view;
    public $data;
    public $path;
    public $storage_path;
    protected $sections=[];
    protected $sectionStack=[];
    protected $contents='';
    protected $file='';
	protected $headers=[];
    public $statusCode=200;
    protected $targetUrl='';
    public static $shared_data=[];
    public static $use_array_merge=false;//false=better speed
    public static $main_view=null;
    public static $views=[];
    public static $views_data=[];
	public static $last_redirected_url='';
	public static $redirect_count=[];
	public static $trim_left_whitespace=true;//true : trim , false : rtrim
	public static $preserve_line_numbers=false;//false: skip empty lines
	public static $paths=[];//custom locations
    public function __construct($view=null,$data=[],$inner_view=false){
		if(View::$main_view===null){
			View::$main_view=$this;
		}
			//View::$views[]=$this;//not used now
			View::$views_data=$data;
		
			//dd($this->data);
			//dd(Route::$request->session->get('_errors',[]));
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
				//echo '<pre>';
				//var_dump( debug_backtrace(false));
				//echo '</pre>';
				//die();
				//dd(Route::$request->session->get('_errors',[]));
				if(Route::$request->session->has('_input')){
					Route::$request->setInput(Route::$request->session->get('_input'));
				}
				$this->data+=Route::$request->session->get('_data',[]);
				//$this->data+=['errors'=>new ParameterBag(Route::$request->session->get('_errors',[]))];
				if(isset($this->data['errors'])){
					$this->data['errors']=new ParameterBag($this->data['errors']->all()+Route::$request->session->get('_errors',[]));
				}else{
					$this->data['errors']=new ParameterBag( Route::$request->session->get('_errors',[]));
				}
				$all_session= Route::$request->session->all();
				foreach($all_session as $key=>$val){
					if(substr($key,0,6)=='_flash'){
						$this->data[substr($key,6)]=$val;
					}
				}
					Route::$request->session->remove('_input');
					Route::$request->session->remove('_data');
					Route::$request->session->remove('_errors');
			}
			
        $this->prepare_view($view,$inner_view);
			/*
			if($sections!==null){
				$this->sections=$sections;
			}
			if($sectionStack!==null){
				$this->sectionStack=$sectionStack;
			}
			*/
    }
    public function prepare_view($view,$inner_view=false){
		$this->view=$view;
		if($view!==null){
				$public_path=App::$public_path;
				$view_path=App::$view_path;  
					//mkdir views
					//mkdir sessions
					//mkdir errors
					//sudo chmod 755 /var/www
					//sudo chown -R www-data:www-data views
					//sudo chown -R www-data:www-data sessions
			$storage_view_path= $public_path. '/../storage/views/' ;
					
				if($inner_view==false){
				
					Route::$request->session->put('_view',$view);
				}
				
				$view2=str_replace('.','/',$view);
				
			$path = $view_path .$view2 . '.blade.php' ;
			if(!file_exists($path)){
				foreach(View::$paths as $item){
					$path = $item.'/'.$view2 . '.blade.php' ;
					if(file_exists($path)){
						break;
					}
				}
			}
			$storage_path=$storage_view_path .$view . '_'. filemtime($path) . '.blade.php' ;
			
			$this->storage_path = $storage_path ; 
			$this->path = $path ;       
		} 
    }
	public function addLocation($path){
		View::$paths[]=rtrim($path,'/');
	}
    public static function make($view,$data=[],$inner_view=false){
			//foreach(View::$views as $v){//not used now
			//	$data+=$v->data;
			//}
			//$data+=View::$views_data;
		return new View($view,$data,$inner_view);
	}
	public static function share($key,$val){
		if(View::$use_array_merge===true){
			View::$shared_data=array_merge(View::$shared_data,[$key=>$val]);
		}else{
			View::$shared_data=View::$shared_data + [$key=>$val];
		}
	}
	public function view_include($view,$data_new=[],$vars=[],$vars2=[]){
		$data=$vars+$vars2;
		foreach($data_new as $key=>$val){
			$data[$key]=$val;
		}
		unset($data['__path']);
		unset($data['__data']);
		//var_dump($data);
		//exit;
		return new View($view,$data,true);
    }
	public function view_make($view,$parent_view){
		return new View($view, $this->data,true);
		//$this->prepare_view($view,true);	
		//return $this;
    }
	public function startSection($section ){
		if (ob_start()) {
			View::$main_view->sectionStack[] = $section;
		}
    }
	public function stopSection(){
        $last = array_pop(View::$main_view->sectionStack);
			if(!isset(View::$main_view->sections[$last])){
				View::$main_view->sections[$last]='';
			}
			//View::$main_view->sections[$last].=ob_get_clean();
            View::$main_view->sections[$last]=ob_get_clean().View::$main_view->sections[$last];
            
        return $last;
    }
	public function yieldContent($section ){
        return isset(View::$main_view->sections[$section])?View::$main_view->sections[$section]:'';
    }
    public function startParent(){
		$last=View::$main_view->stopSection();
		View::$main_view->startSection('parent_'.$last);
		return $last;
    }
    public function showParent(){
		$last=View::$main_view->stopSection();
		return View::$main_view->yieldContent('parent_'.$last);
    }
    public function e($string){
		//if($string!==strip_tags($string)){
		/*if(preg_match("/<\s?[^\>]*\/?\s?>/i",$string)!==0){*/
		if(preg_match("/<[^<]+>/i",$string)!==0){
			return $string;
		}else{
			return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
		}
	}
    public function compile(){
		$extends='';
		$conditions=[
				['@include','(',')','<?php $_view=$this->view_include(',',get_defined_vars());echo $_view->render(); ?>']
				,['@section','(',')','<?php $this->startSection(','); ?>']
				,['@yield','(',')','<?php echo $this->yieldContent(','); ?>']
				,['@if','(',')','<?php if(','){ ?>']
				,['@elseif','(',')','<?php }elseif(','){ ?>']
				,['@foreach','(',')','<?php foreach(','){ ?>']
							
				,['@endsection','<?php $this->stopSection(); ?>']
				,['@else','<?php }else{ ?>']
				,['@endif','<?php } ?>']
				,['@guest','<?php if(auth()->guard()->guest()){ ?>']
				,['@endguest','<?php } ?>']
				,['@endforeach','<?php } ?>']
				,['@parent','<?php $this->startParent(); ?>']
				,['@show','<?php echo $this->showParent(); ?>']
			];	
		$statements=[
				
			];
		$shortcuts=[
				['@{{','}}','@<<','>>']
				,['{{','}}','<?php echo $this->e(',');?>']/*,['{{','}}','<?php echo ',';?>']*/
				,['{!!','!!}','<?php echo ',';?>']
				,['@<<','>>','{{','}}']
				
			];
		$line='';
		$line2='';
		$php=false;
		$js=false;
		$css=false;
		$single_line_comment=false;
		$multi_line_comment=false;
		$html_comment=false;//<!--  -->
		$pos=false;
		$pos2=false; 
		//ini_set("auto_detect_line_endings", true);
		$handle = fopen($this->path, 'rb');
		$dir=dirname($this->storage_path);
		if(!file_exists($dir)){
			Storage::makeDirectory($dir,0755,true,true);
		}
		$handlew = fopen($this->storage_path, 'w');
		if ($handle) {
			//while (!feof($handle) ) {
			while (($line = fgets($handle)) !== false) {
				$line2='';
				$eof='';
				//<!--  --> $html_comment also preserving line numbers and reduce file size
				if(strstr($line, PHP_EOL)) {
					$eof=PHP_EOL;
				}else if(strpos($line, "\n") !== false) {
					$eof="\n";
				}else if(strpos($line, "\r\n") !== false) {
					$eof="\r\n";
				}else if(strpos($line, "\r") !== false) {
					$eof="\r";
				}
				if(View::$trim_left_whitespace===true){//true : trim , false : rtrim
					$line=trim($line);
				}else{
					$line=rtrim($line);
				}
					$pos=strpos($line,'@extends');
					$pos2=false;
				if($pos!==false){ 	
					$pos2=strrpos($line, ')',$pos);
					if($pos2!==false){
						$extends=substr($line,$pos+8,($pos2-$pos)+1-8);
						$extends='<?php echo $this->view_make(' . trim($extends,'()') . ',$this)->render(); ?>';
						$line=substr($line, 0, $pos) .substr($line,  $pos2+ 1 ); 
					}
				}	
				if($line!==''){
					if(strpos($line,'-->')!==false && $html_comment===true){
						$line=substr($line, strpos($line,'-->')+3) ;
						$html_comment=false;
					}elseif($html_comment===true){
						$line=''; 
					}
					if(strpos($line,'<!--') !==false && $html_comment===false){
							$pos=0;
						do{
							if($pos>=strlen($line)){
								$pos=0;
							}
							$pos = strpos($line, '<!--',$pos);							
							if($pos!==false){
								$html_comment=true;
									$pos2 = strpos($line, '-->',$pos);
								if( $pos2!==false ){
									$line=substr($line, 0, $pos) .  substr($line,  $pos2+3 ) ; 
									$pos=0;
									$html_comment=false;
								}else{
									$line=substr($line, 0, $pos); 
									break;
								}
							}
						}while($pos!==false);
					}
				}

				if($line!==''){
					foreach($conditions as $val){
							$count=count($val);
							if($count===5){
								$statement=$val[0];
								$open_brace=$val[1];
								$close_brace=$val[2];
								$replace_open=$val[3];
								$replace_close=$val[4];
							}elseif($count===4){
								$statement=$val[0];
								$open_brace=$val[0];
								$close_brace=$val[1];
								$replace_open=$val[2];
								$replace_close=$val[3];
							}elseif($count===2){
								$statement=$val[0];
								$open_brace='';
								$close_brace='';
								$replace_open=$val[1];
								$replace_close='';
							}
						if(strpos($line,$statement)!==false){			
								$pos=0;
								$len_statement=strlen($statement);
									if($count===4){
										$len_statement=0;
									}
								$len_open=strlen($open_brace);
								$len_close=strlen($close_brace);
								$len_replace_open=strlen($replace_open);
								$len_replace_close=strlen($replace_close);
							if($count===2){
								do{
									if($pos>=strlen($line)){
										$pos=0;
									}
										$pos = strpos($line, $statement,$pos);							
									if($pos!==false){
										$line=substr($line, 0, $pos) .$replace_open .substr($line,$pos+ $len_statement);
										$pos+=$len_replace_open;
									}
								}while($pos!==false);
							}else{
								do{
									if($pos>=strlen($line)){
										$pos=0;
									}
									$pos = strpos($line, $statement,$pos);							
									if($pos!==false){
											$p_level=0;
											$length=strlen($line);
											$pos2=false;
											$pos3=false;
										 
										for($p=$pos+$len_statement;$p<$length;$p++){
											if(substr($line,$p,$len_open)===$open_brace){
												if($p_level===0){
													$pos2=$p;
												}
												$p+=$len_open-1;
												$p_level+=1;
											}
											if(substr($line,$p,$len_close)===$close_brace){
												$p_level-=1;
												if($p_level===0){
													$pos3=$p;
													break;
												}
												$p+=$len_close-1;
											}
										}
										
										if($p_level===0 && $pos2!==false && $pos3!==false){
											$line=substr($line, 0, $pos3) .$replace_close.substr($line,  $pos3+$len_close ) ;
											$line=substr($line, 0, $pos) .$replace_open.substr($line,  $pos2+$len_open ) ;
											$pos=$pos3+$len_replace_close;
										}else{
											$pos+=$len_statement;
										}
										break;
									}
								}while($pos!==false);
							}
						}
					}
				 
						if(strpos($line,'<?php')!==false){
							$php=true;
						}
						if(strpos($line,'<style>')!==false){
							$css=true;
						}
						if(strpos($line,'<script>')!==false){
							$js=true;
						}
				
					if( $php===true || $css===true || $js===true ){// excluding comments
						 
						$single_line_comment=false;	
						$pos=strpos($line,'//') ;
						if($pos!==false  && $multi_line_comment===false ){// (Only multiline comments allowed for single line php code eg:< ? php / *echo $share_otf; */ ? >)
							if(substr($line, $pos-1,1)!=':'){	# http://
								$line=substr($line,0,$pos).$eof;
								$single_line_comment=true;
							}
						} 

						if(strpos($line,'*/')!==false && $multi_line_comment===true){
							$line=substr($line, strpos($line,'*/')+2) ;
							$multi_line_comment=false;
						}elseif($multi_line_comment===true){
							$line=''; 
						}
							$pos=strpos($line,'/*') ;
						if($pos !==false && $single_line_comment===false && $multi_line_comment===false){
								$pos=0;
							do{
								if($pos>=strlen($line)){
									$pos=0;
								}
								$pos = strpos($line, '/*',$pos);							
								if($pos!==false){
									$multi_line_comment=true;
										$pos2 = strpos($line, '*/',$pos);
									if( $pos2!==false ){
										$line=substr($line, 0, $pos) .  substr($line,  $pos2+2 ) ; 
										$pos=0;
										$multi_line_comment=false;
									}else{
										$line=substr($line, 0, $pos); 
										break;
									}
								}
							}while($pos!==false);
						}
						  
						if(strpos($line,'?>')!==false){
							$php=false;
						}
						if(strpos($line,'</style>')!==false){
							$css=false;
						}
						if(strpos($line,'</script>')!==false){
							$js=false;
						}
					}
					/*	  
					foreach($statements as $val){
							$statement=$val[0];
							$replace=$val[1];
						if(strpos($line,$statement)!==false){
								$len_statement=strlen($statement);
								$len_replace=strlen($replace);
								$pos=0;
							do{
								$pos = strpos($line, $statement,$pos);							
								if($pos!==false){
									$line=substr($line, 0, $pos) .$replace .substr($line,$pos+ $len_statement);
									$pos+=$len_replace;
								}
							}while($pos!==false);
						}
					}
					*/					
					
					foreach($shortcuts as $key=>$val){
							$open_brace=$val[0];
							$close_brace=$val[1];
							$replace_open=$val[2];
							$replace_close=$val[3];
						if(strpos($line,$open_brace)!==false){
								$len_open=strlen($open_brace);
								$len_close=strlen($close_brace);
								$len_replace_open=strlen($replace_open);
								$len_replace_close=strlen($replace_close);						
								$pos=0;
								$pos2=false;
							do{
								if($pos>=strlen($line)){
									$pos=0;
								}
								$pos = strpos($line, $open_brace,$pos);							
								if($pos!==false){
										$p_level=0;
										$length=strlen($line);
										$pos2=false;
										$pos3=false;
									for($p=$pos;$p<$length;$p++){	
										if(substr($line,$p,$len_open)===$open_brace){
											if($p_level===0){
												$pos2=$p;
											}
											$p+=$len_open-1;
											$p_level+=1;
										}
										if(substr($line,$p,$len_close)===$close_brace){
											$p_level-=1;
											if($p_level===0){
												$pos3=$p;
												break;
											}
											$p+=$len_close-1;
										}
									}
									if($p_level===0 && $pos2!==false && $pos3!==false){
										$line=substr($line, 0, $pos3) .$replace_close.substr($line,  $pos3+$len_close ) ;
										$line=substr($line, 0, $pos) .$replace_open.substr($line,  $pos2+$len_open ) ;
										$pos=$pos3+$len_replace_close;
									}else{
										$pos+=$len_open;
									}
								}
							}while($pos!==false);
						}
					}
				 
				}
				
				$line=$line.$line2;
				if(View::$trim_left_whitespace===true){//true : trim , false : rtrim
					$line=trim($line);
				}else{
					$line=rtrim($line);
				}
				if($line!=='' || View::$preserve_line_numbers===true){//false: skip empty lines
					$line=$line.$eof;
				}
				if($line!=='' || View::$preserve_line_numbers===true){//false: skip empty lines
					//$contents.=$line; 
					fwrite($handlew,$line);
				}
			} 
			//if (!feof($handle)) {
			//	echo "Error: unexpected fgets() fail\n";
			//}
			fclose($handle);
			fwrite($handlew,$extends);
			fclose($handlew);
		}
	}
	public function render(){
		$b=$this->expired();
		//$b=true;
		if($b===true){
			$this->compile();
		}
		return $this->_render();
	}
    private function _render(){
		//dd($this->data);
		//echo '<pre>';
		//var_dump( debug_backtrace(false));
		//echo '</pre>';
		//die();
		 
			//use Exception;
			//use Throwable;
		$__path=$this->storage_path;
		
		$obLevel = ob_get_level();

        ob_start();

		foreach(View::$shared_data as $key=>$value){//http://php.net/manual/en/function.extract.php#115757     Surprisingly for me extract is 20%-80% slower then foreach construction. I don't really understand why, but it's so.
		    $$key = $value; 
		}
		//extract($this->data, EXTR_SKIP);//Import variables from an array into the current symbol table.
		foreach($this->data as $key=>$value){//http://php.net/manual/en/function.extract.php#115757     Surprisingly for me extract is 20%-80% slower then foreach construction. I don't really understand why, but it's so.
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
        //return ob_get_clean(); 
        return ltrim(ob_get_clean());
       
       
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
		return !file_exists($dir.'/'.$s3.'.blade.php');
		//to clear view run the following
		//php artisan view:clear
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
		if($this->statusCode===302){
			header('Location: '.$this->targetUrl,true,302 );
		}
	}
	public static function getContentType($path){
		$ext='';
			if(strpos($path,'.')){
				$ext=substr($path,strrpos($path,'.')+1);
			}
		 
		$formats =[
			'html' => ['text/html', 'application/xhtml+xml']
			,'txt' => ['text/plain']
			,'js' => ['application/javascript', 'application/x-javascript', 'text/javascript']
			,'css' => ['text/css']
			,'json' => ['application/json', 'application/x-json']
			,'jsonld' => ['application/ld+json']
			,'xml' => ['text/xml', 'application/xml', 'application/x-xml']
			,'rdf' => ['application/rdf+xml']
			,'atom' => ['application/atom+xml']
			,'rss' => ['application/rss+xml']
			,'form' => ['application/x-www-form-urlencoded']
			,'mp4'=>['video/mp4']
			,'pdf'=>['application/pdf']
			,'bin'=>['application/octet-stream']
			,'csv'=>['text/plain;charset=UTF-8']
			,'xls'=>['application/vnd.ms-excel']
			,'xlsx'=>['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
			,'ppt'=>['application/vnd.ms-powerpoint']
			,'pptx'=>['application/vnd.openxmlformats-officedocument.presentationml.presentation']
			,'docx'=>['application/vnd.openxmlformats-officedocument.wordprocessingml.document']
		];
		//$result=isset($formats[$ext])?$formats[$ext][0]:'application/octet-stream';
		$result=isset($formats[$ext])?$formats[$ext][0]:false;
		if($result!==false){
			return $result;
		}
			$result = false;
		if (function_exists('finfo_open') === true){
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			if (is_resource($finfo) === true) {
				$result = finfo_file($finfo, $path);
			}
			finfo_close($finfo);
		} else if (function_exists('mime_content_type') === true){
			$result = preg_replace('~^(.+);.*$~', '$1', mime_content_type($path));
		} else if (function_exists('exif_imagetype') === true){
			$result = image_type_to_mime_type(exif_imagetype($path));
		}
		
		  
        return $result;
    }
    public function __tostring(){
		if($this->file!==''){
			//- turn off compression on the server
			@ini_set('zlib.output_compression', 'Off');
			$is_attachment =true;
			$file = $this->file;
			$mimetype=View::getContentType($file);
			$path_parts = pathinfo($file);
			$file_name  = $path_parts['basename'];
			$file_ext   = $path_parts['extension'];
			$fp = @fopen($file, 'rb');
			$file_size   = filesize($file); // File size
			$length = $file_size;           // Content length
			$start  = 0;               // Start byte
			$end    = $file_size - 1;       // End byte
			 
				$date = DateTime::createFromFormat('U',filemtime($file));
				$date->setTimezone(new \DateTimeZone('UTC'));
				header('Last-Modified: '.$date->format('D, d M Y H:i:s').' GMT');
			//dd($mimetype);	
			header('Content-type: '.$mimetype);
			// The three lines below basically make the
			// download non-cacheable 
			//header('Cache-control: private');
			//header('Pragma: private');
			//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			#header('Cache-Control: no-store, no-cache, must-revalidate');
			#header('Expires: Thu, 19 Nov 1981 00:00:00 GMT');
			#header('Pragma: no-cache');
			// set the headers, prevent caching
			header("Pragma: public");
			header("Expires: -1");
			header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
			// set appropriate headers for attachment or streamed file
			if ($is_attachment){
				header("Content-Disposition: attachment; filename=\"$file_name\"");
			}else{
				header('Content-Disposition: inline;');
				header('Content-Transfer-Encoding: binary');
			}
				foreach($this->headers as $key=>$val){
					header($key.': ' . $val,true);
				}
			/*
			//check if http_range is sent by browser (or download manager)
			if(isset($_SERVER['HTTP_RANGE'])){
				list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
				if ($size_unit == 'bytes'){
					//multiple ranges could be specified at the same time, but for simplicity only serve the first range
					//http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
					list($range, $extra_ranges) = explode(',', $range_orig, 2);
				}else{
					$range = '';
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					exit;
				}
			}else{
				$range = '';
			}
			//figure out download piece from range (if set)
			list($seek_start, $seek_end) = explode('-', $range, 2);
			//set start and end based on range (if set), else set defaults
			//also check for invalid ranges.
			$seek_end   = (empty($seek_end)) ? ($file_size - 1) : min(abs(intval($seek_end)),($file_size - 1));
			$seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)),0);
			//Only send partial content header if downloading a piece of the file (IE workaround)
			if ($seek_start > 0 || $seek_end < ($file_size - 1)){
				header('HTTP/1.1 206 Partial Content');
				header('Content-Range: bytes '.$seek_start.'-'.$seek_end.'/'.$file_size);
				header('Content-Length: '.($seek_end - $seek_start + 1));
			}else{
			  header("Content-Length: $file_size");
			}
			header('Accept-Ranges: bytes');
			set_time_limit(0);
			fseek($file, $seek_start);
			$buffer = 1024 * 8;
			while(!feof($file)){
				print(@fread($file,$buffer));
				ob_flush();
				flush();
				if (connection_status()!=0){
					@fclose($file);
					exit;
				}			
			}
			// file save was a success
			@fclose($file);
			exit;
			*/
			
			
			header('Accept-Ranges: bytes');//header("Accept-Ranges: 0-$length");
			if (isset($_SERVER['HTTP_RANGE'])) {
				$c_start = $start;
				$c_end   = $end;
				list( , $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
				if (strpos($range, ',') !== false) {
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					header("Content-Range: bytes $start-$end/$file_size");
					exit;
				}
				if ($range == '-') {
					$c_start = $file_size - substr($range, 1);
				}else{
					$range  = explode('-', $range);
					$c_start = $range[0];
					$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $file_size;
				}
				$c_end = ($c_end > $end) ? $end : $c_end;
				if ($c_start > $c_end || $c_start > $file_size - 1 || $c_end >= $file_size) {
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					header("Content-Range: bytes $start-$end/$file_size");
					exit;
				}
				$start  = $c_start;
				$end    = $c_end;
				$length = $end - $start + 1;
				fseek($fp, $start);
				header('HTTP/1.1 206 Partial Content');
			}
			
			header("Content-Range: bytes $start-$end/$file_size");
			header("Content-Length: ".$length);
			header("Connection: Keep-Alive");
			header("Keep-Alive: timeout=5");
			set_time_limit(0);
			$buffer = 1024 * 8;
			while(!feof($fp) && ($p = ftell($fp)) <= $end) {
				if ($p + $buffer > $end) {
					$buffer = $end - $p + 1;
				}
				//set_time_limit(0);
				echo fread($fp, $buffer);
				ob_flush();
				flush();
				if (connection_status()!=0){
                    fclose($fp);
                    exit;
                }
			}
			fclose($fp);
			exit();
			
		}
		if(!headers_sent()){
			foreach($this->headers as $key=>$val){
				header($key.': ' . $val,true);
			}
		}
		if($this->contents==='' && $this->view!==null){
			$this->contents=$this->render();
		}
			$this->setStatus();
		return $this->contents;
	}
	public function getContent(){
		return $this->toString();
	}
	public function withErrors($data){
		if($data instanceof Validator){
			$data=$data->errors();
		}
		if(View::$use_array_merge===true){
			$data=array_merge( isset( $this->data['errors'])?$this->data['errors']->all():[],$data);
		}else if($data instanceof ParameterBag){
			$data= (isset( $this->data['errors'])?$this->data['errors']->all():[]) +$data->all();
		}else{
			$data= (isset( $this->data['errors'])?$this->data['errors']->all():[]) +$data;
		}
		Route::$request->session->set('_errors',$data);
		//if(isset($this->data['errors'])){
		//	$this->data['errors']=new ParameterBag($this->data['errors']->all()+$data);
		//}else{
		//	$this->data['errors']=new ParameterBag( $data);
		//}
		//dd($data);
		//dd($this->data);
		Route::$request->session->save();
		//dd(Route::$request->session->get('_errors',[]));
		return $this;
	}
	public function withInput(){
		Route::$request->session->set('_input',Route::$request->all_input());
		Route::$request->session->save();
		return $this;
	}
	public function with($data,$val=null){
			if(!is_array($data)){
				$data=[$data=>$val];
			}
		foreach($data as $k=>$v){
			Route::$request->session->flash($k,$v);
		}
		Route::$request->session->save();
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
		/*
		if(!headers_sent()){
			foreach($headers as $key=>$val){
				header($key.': ' . $val);
			}
			//header('Content-Length: ' . strlen($this->contents));
		}
		*/
		$this->headers=$headers;
		return $this;
	}
	public function redirect_url($url){
		Route::$request->session->set('_back','false');
		$this->statusCode=302;
		$this->targetUrl=$url;
		//if(View::$last_redirected_url===$url){
		//	$error= 'redirected you too many times!';
		//	throw new Exception($error);
		//	exit;
		//}
		View::$last_redirected_url=$url;
		if (array_key_exists($url,View::$redirect_count)===false){			
			View::$redirect_count[$url]=0 ;
			//echo $url;
			//exit;
		}
			View::$redirect_count[$url]+=1;
			 
		
			if(View::$redirect_count[$url]>2){
				View::$redirect_count[$url]=0;
				$error= 'redirected too many times!';
				throw new Exception($error);
				//echo $url;
				
				exit;
			}
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
		$this->contents=json_encode($json,15);
		if(!headers_sent()){
			//header('Content-Type: text/javascript');
			header('Content-Type: application/json');
			header('Content-Length: ' . strlen($this->contents));
		}
		return $this;
	}
	public function file($file,$headers=[]){
		//$this->setContents(file_get_contents($file));
		$this->contents='';
		$this->file=$file;
		
		return $this->withHeaders($headers);
	}
	public function back(){		
		$url=Route::$request->previous();
		$this->redirect_url($url);
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
function view($view=null,$data=[]){
	if($view!==null){
		$view = View::make($view,$data);
	}else{
		$view=new View();
	}
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
			$routes=App::$routes;
			$current_route=App::$current_route;
		
	if($route===null){
		return  new View();
	}
		
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
	http_response_code(404);
	if(file_exists(__DIR__.'/page_not_found.html')){
		send_file(__DIR__.  '/page_not_found.html');
	}else{
		send_file(App::$public_path. '/../classes/page_not_found.html');
	}
}
function token_mismatch(){
	http_response_status(419,'Authentication Timeout');
	//send_file($public_path. '/../classes/token_mismatch.html');	
	if(file_exists(__DIR__.'/token_mismatch.html')){
		return file_get_contents(__DIR__. '/token_mismatch.html');	
	}else{
		return file_get_contents(App::$public_path. '/../classes/token_mismatch.html');	
	}
}
function app(){
	return new View();
}
class Blade{
	public static function compileString($contents){
				$public_path=App::$public_path;
				$view_path=App::$view_path;  
		/*
			$storage_view_path= $public_path. '/../storage/views/' ;
		$view=new View();
			$path=$storage_view_path . uniqid() . '.blade.php' ;
			$storage_path=$storage_view_path . uniqid() . '.blade.php' ;
			$view->path=$path;
			$view->storage_path=$storage_path ; 
		file_put_contents($path,$contents);	 
		$contents=$view->render();
			//unlink($path);
			//unlink($storage_path);
		return $contents;
		*/
		$path=$view_path .  'lnpf__temp.blade.php' ;
			if(!file_exists($path)){
				foreach(View::$paths as $item){
					$path = $item.'/'.$view2 . '.blade.php' ;
					if(file_exists($path)){
						break;
					}
				}
			}
		file_put_contents($path,$contents);	 
		$view= view('lnpf__temp');
		$contents=$view->render();
		unlink($view->storage_path);
		//unlink($path);
		return $contents; 
	}
}
class Response{
	public static function make($contents,$status=null,$headers=[]){
		if($status!=null){
			http_response_code($status);
		}
		if(!headers_sent()){
			foreach($headers as $key=>$val){
				header($key.': ' . $val,true);
			}
		}
		return Blade::compileString($contents); 
	}
}
