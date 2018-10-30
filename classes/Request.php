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
		if((isset($_SERVER['HTTPS']) && in_array(strtolower($_SERVER['HTTPS']), array('on','1' ,'ssl')) ) || intval($_SERVER['SERVER_PORT'])==443 || $_SERVER['REQUEST_SCHEME']==='https'){
			$secure=true;
		}
		$scheme=$secure?'https':'http';
		$port =isset($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:null;
		if($port===null){
			$port =$secure? 443 : 80;
		}
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
             
        }else{
			 $host=$host.':'.$port;
		}
		
        return $scheme.'://'.$host.  $this->getBaseUrl() ;
    }
	public function path(){
        //$uri=explode('/', strtolower(explode('?', $_SERVER['REQUEST_URI'])[0])) ;
		//$doc_root= explode('/',strtolower($_SERVER['SCRIPT_FILENAME'])) ;
        $uri=explode('/',  explode('?', $_SERVER['REQUEST_URI'])[0])  ;
		$doc_root= explode('/', $_SERVER['SCRIPT_FILENAME'])  ;
			foreach($doc_root as $item){
				foreach($uri as $key=>$val){
					if($item===$val){
						unset($uri[$key]);
						break;
					}
				}
			}
			$uri=ltrim( implode('/',$uri),'/');
		return $uri;
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
		return isset($_REQUEST[$name]);
	}
	public function input($name,$default=null){
		return isset($_REQUEST[$name])? $_REQUEST[$name]:$default  ;
	}
	public function hasFile($name){
		return isset($_FILES[$name]);
	}
	public function file($name){
		return $this->files->get($name);
	}
	public function __get($name){
		if(isset($_FILES[$name])){
			return $this->files->get($name);
		}elseif(isset($_REQUEST[$name])){
			return $_REQUEST[$name];
		}else{
			return null;
		}
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
	public function isSecure(){
		if ( (isset($_SERVER['HTTPS']) && in_array(strtolower($_SERVER['HTTPS']), array('on','1' ,'ssl')) ) || intval($_SERVER['SERVER_PORT'])==443 || $_SERVER['REQUEST_SCHEME'] == "https" )		{
			true;
		}else{
			false;
		}
	}
	public function getHost(){
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
                return '';
        }
        return $host;
    }
    public static function normalizeQueryString($qs){
        if ('' == $qs) {
            return '';
        }
        $parts = array();
        $order = array();
        foreach (explode('&', $qs) as $param) {
            if ('' === $param || '=' === $param[0]) {
                // Ignore useless delimiters, e.g. "x=y&".
                // Also ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
                // PHP also does not include them when building _GET.
                continue;
            }
            $keyValuePair = explode('=', $param, 2);
            // GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
            // PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str. This is why we use urldecode and then normalize to
            // RFC 3986 with rawurlencode.
            $parts[] = isset($keyValuePair[1]) ?
                rawurlencode(urldecode($keyValuePair[0])).'='.rawurlencode(urldecode($keyValuePair[1])) :
                rawurlencode(urldecode($keyValuePair[0]));
            $order[] = urldecode($keyValuePair[0]);
        }
        array_multisort($order, SORT_ASC, $parts);
        return implode('&', $parts);
    }
    public function getScheme(){
        return $this->isSecure() ? 'https' : 'http';
    }
    public function getPort(){
        if (!$port = $_SERVER['SERVER_PORT']) {
            return $_SERVER['SERVER_PORT'];
        }
        return 'https' === $this->getScheme() ? 443 : 80;
    }
    public function getrequestMethod(){
		return $_SERVER['REQUEST_METHOD'];
    }
    public function method(){
		return $_SERVER['REQUEST_METHOD'];
    }
    public function getHttpHost(){
        $scheme = $this->getScheme();
        $port = $this->getPort();
        if (('http' == $scheme && 80 == $port) || ('https' == $scheme && 443 == $port)) {
            return $this->getHost();
        }
        return $this->getHost().':'.$port;
    }
    public function getRequestUri(){
        return $_SERVER['REQUEST_URI'];
    }
    public function getSchemeAndHttpHost(){
        return $this->getScheme().'://'.$this->getHttpHost();
    }
    public function getQueryString(){
        $qs = static::normalizeQueryString($_SERVER['QUERY_STRING']);
        return '' === $qs ? null : $qs;
    }
	private function getUrlencodedPrefix($string, $prefix){
        if (0 !== strpos(rawurldecode($string), $prefix)) {
            return false;
        }
        $len = \strlen($prefix);
        if (preg_match(sprintf('#^(%%[[:xdigit:]]{2}|.){%d}#', $len), $string, $match)) {
            return $match[0];
        }
        return false;
    }
    public function getBaseUrl(){
        $filename = basename($_SERVER['SCRIPT_FILENAME']);
        if (basename($_SERVER['SCRIPT_NAME']) === $filename) {
            $baseUrl = $_SERVER['SCRIPT_NAME'];
        } elseif (basename($_SERVER['PHP_SELF']) === $filename) {
            $baseUrl = $_SERVER['PHP_SELF'];
        } elseif (basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
            $baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $_SERVER['PHP_SELF'];
            $file = $_SERVER['SCRIPT_FILENAME'];
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = \count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }
        // Does the baseUrl have anything in common with the request_uri?
        //$requestUri = $this->getRequestUri();
        $requestUri =$_SERVER['REQUEST_URI'];
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/'.$requestUri;
        }
        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
            // full $baseUrl matches
            return $prefix;
        }
        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, rtrim(\dirname($baseUrl), '/'.\DIRECTORY_SEPARATOR).'/')) {
            // directory portion of $baseUrl matches
            return rtrim($prefix, '/'.\DIRECTORY_SEPARATOR);
        }
        $truncatedRequestUri = $requestUri;
        if (false !== $pos = strpos($requestUri, '?')) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }
        $basename = basename($baseUrl);
        if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
            // no match whatsoever; set it blank
            return '';
        }
        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of baseUrl. $pos !== 0 makes sure it is not matching a value
        // from PATH_INFO or QUERY_STRING
        if (\strlen($requestUri) >= \strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && 0 !== $pos) {
            $baseUrl = substr($requestUri, 0, $pos + \strlen($baseUrl));
        }
        return rtrim($baseUrl, '/'.\DIRECTORY_SEPARATOR);
    }
    public function getPathInfo(){
        //if (null === ($requestUri = $this->getRequestUri())) {
        if (null === ($requestUri = $_SERVER['REQUEST_URI'])) {
            return '/';
        }
        // Remove the query string from REQUEST_URI
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/'.$requestUri;
        }
        if (null === ($baseUrl = $this->getBaseUrl())) {
            return $requestUri;
        }
        $pathInfo = substr($requestUri, \strlen($baseUrl));
        if (false === $pathInfo || '' === $pathInfo) {
            // If substr() returns false then PATH_INFO is set to an empty string
            return '/';
        }
        //return (string) $pathInfo;
        return $pathInfo;
    }
    
    public function getUri(){
        if (null !== $qs = $this->getQueryString()) {
            $qs = '?'.$qs;
        }
        return $this->getSchemeAndHttpHost().$this->getBaseUrl().$this->getPathInfo().$qs;
    }
    public function url(){
        return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
    }
    public function root(){
        return rtrim($this->getSchemeAndHttpHost().$this->getBaseUrl(), '/');
    }
    public function current(){
        return  $this->url()  ;
    }
    public function getBaseUri2(){
        return $this->getSchemeAndHttpHost().$this->getBaseUrl() ;
    }
    public function session(){
        return  $this->session  ;
    }
    public function previous($fallback=false){
				$referrer =isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:null;
			$url = $referrer ? url($referrer) : $this->session->get('_previous_url');
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

