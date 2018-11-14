<?php
class Request{
	//public $server;
	//public $request ;
	public $files;
	public $cookies;
	public $session;
	//public $headers;
	function __construct(){
		//$server=[];
		//foreach($_SERVER as $key => $value)    {
		//  $server{$this->toCamelCase($key)} = $value;
		//}
		//$this->server=new ParameterBag($server);  
		//$this->request  = new ParameterBag($_REQUEST);
		if(count($_FILES)>0){
			$this->files = new FileBag($_FILES);
		}
		//$this->headers = new ParameterBag(getallheaders ()); 
	}
	public function set_cookies($data){
		$this->cookies=new ParameterBag($data);
	}
	public function set_session($session){
		$this->session=$session;
	}
	public function getCurrentUri(){
		//$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		//$path=explode('/', $_SERVER['SCRIPT_NAME']);
		//	array_pop($path);
		//$basepath = implode('/', $path) . '/';
		//$basepath =substr( $_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],'index.php')-1);
		$basepath =substr( $_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/'));
		$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
		if (strpos($uri, '?')!==false) $uri = substr($uri, 0, strpos($uri, '?'));
		return trim($uri, '/');
	}
	public function getBaseUri(){
		$secure=false;
		if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || (isset($_SERVER['SERVER_PORT']) && intval($_SERVER['SERVER_PORT'])==443) || $_SERVER['REQUEST_SCHEME']==='https'){
			$secure=true;
		}
		$scheme=$secure?'https':'http';
		$port =isset($_SERVER['SERVER_PORT'])?intval($_SERVER['SERVER_PORT']):null;
		
		if (!$host = $_SERVER['HTTP_HOST']) {
			if (!$host = $_SERVER['HOST']) {
				if (!$host = $_SERVER['SERVER_NAME']) {
					$host = $_SERVER['SERVER_ADDR'];
				}
			}
        }
        // trim and remove port number from host
        // host is lowercase as per RFC 952/2181
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));
        // as the host can come from the user (HTTP_HOST and depending on the configuration, SERVER_NAME too can come from the user)
        // check that it does not contain forbidden characters (see RFC 952 and RFC 2181)
        // use preg_replace() instead of preg_match() to prevent DoS attacks with long host names
        if ($host && '' !== preg_replace('/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/', '', $host)) {
                $host='';
        }
        if (('http' === $scheme && 80 === $port) || ('https' === $scheme && 443 === $port)) {
             
        }elseif($port!==null){
			 $host=$host.':'.$port;
		}
		
        $baseUrl = dirname($_SERVER['PHP_SELF']);
        if (empty($baseUrl) ) {
            // no match whatsoever; set it blank
            $baseUrl= '';
        }
                
        return $scheme.'://'.$host .rtrim($baseUrl, '/'.\DIRECTORY_SEPARATOR);
    }
	public function getHost(){
		if (!$host = $_SERVER['HTTP_HOST']) {
			if (!$host = $_SERVER['HOST']) {
				if (!$host = $_SERVER['SERVER_NAME']) {
					$host = $_SERVER['SERVER_ADDR'];
				}
			}
        }
        return $host;
    }
    public function path(){
		return $this->getCurrentUri();
    }
	private function toCamelCase($string){
		$result = strtolower($string);
		preg_match_all('/_[a-z]/', $result, $matches);
		foreach($matches[0] as $match)    {
			$c = str_replace('_', '', strtoupper($match));
			$result = str_replace($match, $c, $result);
		}
		return $result;
	}
	public function server($name){
		return isset($_SERVER[$name])? $_SERVER[$name]:null  ;
	}
	public function header($name){
		return isset($_SERVER[$name])? $_SERVER[$name]:null  ;
	}
	public function has($name){
		return isset($_REQUEST[$name]) && $_REQUEST[$name]!=='';//!empty($_REQUEST[$name]);
	}
	public function input($name,$default=null){
		return isset($_REQUEST[$name])? $_REQUEST[$name]:$default  ;
	}
	public function hasFile($name){
		return isset($_FILES[$name]) && $this->files->get($name)!==null;
	}
	public function file($name){
		return $this->files->get($name);
	}
	public function __isset($name){
		if(isset($_FILES[$name])){
			return true;
		}elseif(isset($_REQUEST[$name])){
			return true;
		}
			return false;
	}
	public function __get($name){
		if(isset($_FILES[$name])){
			return $this->files->get($name);
		}elseif(isset($_REQUEST[$name])){
			return $_REQUEST[$name];
		}
			return null;
	}
	public function all(){
        //return array_merge($_REQUEST,$_FILES);
        return  $_REQUEST+$_FILES;//better speed
    }
    public function all_input(){
        return $_REQUEST;
    }
    public function setInput($data=null){
		if($data!==null){
			$_REQUEST=$data;
		}
        $this->request  = new ParameterBag($_REQUEST);
    }
    public function getrequestMethod(){
		return $_SERVER['REQUEST_METHOD'];
    }
    public function method(){
		return $_SERVER['REQUEST_METHOD'];
    }
    public function url(){
		$base_url=$this->getBaseUri();
		$cur_url=$this->getCurrentUri();
		if($base_url===$cur_url){
			return $cur_url;
		}
        return $base_url.'/'.$cur_url;
    }
    public function fullUrl(){
		$qs='';
		if (strpos($_SERVER['REQUEST_URI'], '?')!==false){
			$qs = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?'));
		}
		$base_url=$this->getBaseUri();
		$cur_url=$this->getCurrentUri();
		if($base_url===$cur_url){
			return $cur_url.$qs;
		}
        return $this->getBaseUri().'/'.$this->getCurrentUri().$qs;
    }
    public function root(){
        return  $this->getBaseUri();
    }
    public function current(){
        return  $this->url()  ;
    }
    public function session(){
        return  $this->session  ;
    }
    public function previous($fallback=false){
				$referrer =isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:null;
			$url = $referrer ? url($referrer) : (isset($this->session)?$this->session->get('_previous_url'):'');
        if($url){
            return $url;
        }elseif($fallback){
            return url($fallback);
        }else{
            return url('/');
        }
    }
    public function isXmlHttpRequest(){
        //return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
        return 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH'];
    }
    public function ajax(){
        //return $this->isXmlHttpRequest();
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
    }
    public function ajax_secure($hour=1){
		$token=$this->input('_token','');
        return check_ajax_csrf_token($token,$hour) || hash_equals($this->session()->token(), $token) ; 
    }
    private function formatRoute($route)  {
		$result = rtrim($route, '/');
		if ($result === '')    {
		  return '/';
		}
		return $result;
	}
	private function trimRoute($route)  {
		$result = trim($route, '/');
		if ($result === '')    {
		  return '/';
		}
		return $result;
	}
}

