<?php
class Request{
	public $server;
	public $request ;
	public $files;
	public $cookies;
	public $session;
	public $headers;
	function __construct(){
		//$server=[];
		//foreach($_SERVER as $key => $value)    {
		//  $server{$this->toCamelCase($key)} = $value;
		//}
		//$this->server=new ParameterBag($server);  
		
		if(count($_FILES)>0){
			$this->files = new FileBag($_FILES);
		}
		//$this->headers = new ParameterBag(getallheaders ());
		$CONTENT_TYPE=isset($_SERVER['HTTP_CONTENT_TYPE'])?$_SERVER['HTTP_CONTENT_TYPE']:'application/form-unknown';
		$REQUEST_METHOD=isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:'GET';
		if (strpos('application/x-www-form-urlencoded,multipart/form-data-encoded',strtolower($CONTENT_TYPE))!==false
            && strpos('PUT,DELETE,PATCH',strtoupper($REQUEST_METHOD))!==false
			){
            parse_str(file_get_contents('php://input'), $data);
            //$request->request = new ParameterBag($data);
            $_REQUEST=$data;
        }
        //$this->request  = new ParameterBag($_REQUEST);
        $this->request  = $this;
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
		$uri=trim($uri, '/');
		return $uri;
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
	public function getScheme(){
		$secure=false;
		if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || (isset($_SERVER['SERVER_PORT']) && intval($_SERVER['SERVER_PORT'])==443) || $_SERVER['REQUEST_SCHEME']==='https'){
			$secure=true;
		}
		$scheme=$secure?'https':'http';
        return $scheme;
    }
	public function getPort(){
        $port =isset($_SERVER['SERVER_PORT'])?intval($_SERVER['SERVER_PORT']):null;
		if($port!==null){
			return $port;
		}
		if (!$host = $_SERVER['HTTP_HOST']) {
			if (!$host = $_SERVER['HOST']) {
				if (!$host = $_SERVER['SERVER_NAME']) {
					$host = $_SERVER['SERVER_ADDR'];
				}
			}
        }
		if(strpos($host,':')!==false){
			return explode(':',$host)[1];
		}
        return 'https' === $this->getScheme() ? 443 : 80;
    }
	public function getHttpHost(){
        $scheme = $this->getScheme();
        $port = $this->getPort();
        if (('http' == $scheme && 80 == $port) || ('https' == $scheme && 443 == $port)) {
            return $this->getHost();
        }
        return $this->getHost().':'.$port;
    }
	public function getSchemeAndHttpHost(){
        return $this->getScheme().'://'.$this->getHttpHost();
    }
	protected function prepareRequestUri(){
        $requestUri = '';
        if ($this->header_has('X_ORIGINAL_URL')) {
            // IIS with Microsoft Rewrite Module
            $requestUri = $this->header('X_ORIGINAL_URL');
            $this->header_remove('X_ORIGINAL_URL');
            $this->header_remove('HTTP_X_ORIGINAL_URL');
            $this->header_remove('UNENCODED_URL');
            $this->header_remove('IIS_WasUrlRewritten');
        } elseif ($this->header_has('X_REWRITE_URL')) {
            // IIS with ISAPI_Rewrite
            $requestUri = $this->header('X_REWRITE_URL');
            $this->header_remove('X_REWRITE_URL');
        } elseif ('1' == $this->server('IIS_WasUrlRewritten') && '' != $this->server('UNENCODED_URL')) {
            // IIS7 with URL Rewrite: make sure we get the unencoded URL (double slash problem)
            $requestUri = $this->server('UNENCODED_URL');
            $this->header_remove('UNENCODED_URL');
            $this->header_remove('IIS_WasUrlRewritten');
        } elseif ($this->header_has('REQUEST_URI')) {
            $requestUri = $this->server('REQUEST_URI');
            // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path, only use URL path
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (0 === strpos($requestUri, $schemeAndHttpHost)) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif ($this->header_has('ORIG_PATH_INFO')) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->server('ORIG_PATH_INFO');
            if ('' != $this->server('QUERY_STRING')) {
                $requestUri .= '?'.$this->server('QUERY_STRING');
            }
            $this->header_remove('ORIG_PATH_INFO');
        }

        // normalize the request URI to ease creating sub-requests from this request
        $_SERVER['REQUEST_URI']= $requestUri;

        return $requestUri;
    }
	public function getRequestUri(){
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }
        return $this->requestUri;
    }
	private function getUrlencodedPrefix($string, $prefix){
        if (0 !== strpos(rawurldecode($string), $prefix)) {
            return false;
        }
        $len = strlen($prefix);
        if (preg_match(sprintf('#^(%%[[:xdigit:]]{2}|.){%d}#', $len), $string, $match)) {
            return $match[0];
        }
        return false;
    }
	protected function prepareBaseUrl(){
		$filename = basename($this->server('SCRIPT_FILENAME'));

        if (basename($this->server('SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server('SCRIPT_NAME');
        } elseif (basename($this->server('PHP_SELF')) === $filename) {
            $baseUrl = $this->server('PHP_SELF');
        } elseif (basename($this->server('ORIG_SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server('ORIG_SCRIPT_NAME'); // 1and1 shared hosting compatibility
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $this->server('PHP_SELF', '');
            $file = $this->server('SCRIPT_FILENAME', '');
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }

        // Does the baseUrl have anything in common with the request_uri?
        $requestUri =  $this->getRequestUri();
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/'.$requestUri;
        }

        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
            // full $baseUrl matches
            return $prefix;
        }

        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, rtrim(dirname($baseUrl), '/'.DIRECTORY_SEPARATOR).'/')) {
            // directory portion of $baseUrl matches
            return rtrim($prefix, '/'.DIRECTORY_SEPARATOR);
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
        if (strlen($requestUri) >= strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && 0 !== $pos) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return rtrim($baseUrl, '/'.DIRECTORY_SEPARATOR);
	}
	public function getBaseUrl(){
		 if (null === $this->BaseUrl) {
            $this->BaseUrl = $this->prepareBaseUrl();
        }
        return $this->BaseUrl;
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
		/*
		$uri=trim($this->getCurrentUri(), '/');
		if(stripos( $uri,'index.php/')!==false){
			$uri=str_replace('index.php/','',$uri);
		}
		return $uri===''?'/':$uri;
		*/
		$pattern = trim($this->getPathInfo(), '/');
        return $pattern == '' ? '/' : $pattern;
    }
	protected function preparePathInfo(){
		if (null === ($requestUri = $this->getRequestUri())) {
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

        $pathInfo = substr($requestUri, strlen($baseUrl));
        if (false === $pathInfo || '' === $pathInfo) {
            // If substr() returns false then PATH_INFO is set to an empty string
            return '/';
        }

        return (string) $pathInfo;
	}
	/**
     * Returns the path being requested relative to the executed script.
     *
     * The path info always starts with a /.
     *
     * Suppose this request is instantiated from /mysite on localhost:
     *
     *  * http://localhost/mysite              returns an empty string
     *  * http://localhost/mysite/about        returns '/about'
     *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
     *  * http://localhost/mysite/about?var=1  returns '/about'
     *
     * @return string The raw path (i.e. not urldecoded)
     */
    public function getPathInfo(){
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }
        return $this->pathInfo;
    }
	protected function prepareBasePath(){
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }
        $filename = basename($this->server('SCRIPT_FILENAME'));
        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }
        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }
        return rtrim($basePath, '/');
    }
	/**
     * Returns the root path from which this request is executed.
     *
     * Suppose that an index.php file instantiates this request object:
     *
     *  * http://localhost/index.php         returns an empty string
     *  * http://localhost/index.php/page    returns an empty string
     *  * http://localhost/web/index.php     returns '/web'
     *  * http://localhost/we%20b/index.php  returns '/we%20b'
     *
     * @return string The raw path (i.e. not urldecoded)
     */
    public function getBasePath(){
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }
        return $this->basePath;
    }
	public function root(){
        //return  $this->getBaseUri();
		return rtrim($this->getSchemeAndHttpHost().$this->getBaseUrl(), '/');
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
	public function getQueryString(){
        $qs = static::normalizeQueryString($this->server('QUERY_STRING'));
        return '' === $qs ? null : $qs;
    }
	public function getUri(){
        if (null !== $qs = $this->getQueryString()) {
            $qs = '?'.$qs;
        }
        return $this->getSchemeAndHttpHost().$this->getBaseUrl().$this->getPathInfo().$qs;
    }
	public function url(){
		/*
		$base_url=$this->getBaseUri();
		$cur_url=$this->getCurrentUri();
		if($base_url===$cur_url){
			return $cur_url;
		}
        return $base_url.'/'.$cur_url;
		*/
		return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
    }
	public function fullUrl(){
		/*
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
		*/
		$query = $this->getQueryString();
        $question = $this->getBaseUrl().$this->getPathInfo() == '/' ? '/?' : '?';
        return $query ? $this->url().$question.$query : $this->url();
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
	public function header_has($name){
		return isset($_SERVER[$name]) ;
	}
	public function header_remove($name){
		unset($_SERVER[$name]) ;
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
        //$this->request=new ParameterBag($_REQUEST);
        $this->request=$this;
    }
    public function add($data){
		foreach($data as $key=>$val){
			$_REQUEST[$key]=$val;
		}
	}
	public function replace($data){
		$this->add($data);
	}
    public function getrequestMethod(){
		return $_SERVER['REQUEST_METHOD'];
    }
    public function method(){
		return $_SERVER['REQUEST_METHOD'];
    }
    public function current(){
        return  $this->url();
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
    public function is($pattern){//from laravel Str class
		$value=rawurldecode($this->path());//decodedPath
        if ($pattern == $value) {
            return true;
        }
        $pattern = preg_quote($pattern, '#');
        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);
        return (bool) preg_match('#^'.$pattern.'\z#u', $value);
    }
    public function isXmlHttpRequest(){
        return 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH'];
    }
    public function ajax(){
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
	public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null){
        $this->server = new ParameterBag($_SERVER);
		$this->headers = new ParameterBag(getallheaders ());
		$dup = clone $this;
		
        if (null !== $query) {
            $dup->query = new ParameterBag($query);
        }
        if (null !== $request) {
            $dup->request = new ParameterBag($request);
        }
        if (null !== $attributes) {
            $dup->attributes = new ParameterBag($attributes);
        }
        if (null !== $cookies) {
            $dup->cookies = new ParameterBag($cookies);
        }
        if (null !== $files) {
            $dup->files = new FileBag($files);
        }
        if (null !== $server) {
            $dup->server = new ParameterBag($server);
            $dup->headers = new ParameterBag(getallheaders ());
        }
        $dup->languages = null;
        $dup->charsets = null;
        $dup->encodings = null;
        $dup->acceptableContentTypes = null;
        $dup->pathInfo = null;
        $dup->requestUri = null;
        $dup->baseUrl = null;
        $dup->basePath = null;
        $dup->method = null;
        $dup->format = null;
        //if (!$dup->get('_format') && $this->get('_format')) {
        //    $dup->attributes->set('_format', $this->get('_format'));
        //}
        //if (!$dup->getRequestFormat(null)) {
        //    $dup->setRequestFormat($this->getRequestFormat(null));
        //}
        return $dup;
    }
	public function setRequest(Request $request){
		$this->requestUri =null;
		$this->BaseUrl =null;
		$this->pathInfo =null;
		$this->basePath =null;
        $this->request = $request;
        return $this;
    }
}