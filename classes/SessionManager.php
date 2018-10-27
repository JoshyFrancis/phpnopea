<?php
class SessionManager{// implements SessionHandlerInterface{
    protected $id;
    protected $path;
    public $seconds;
    public $session_name;
	protected $attributes = [];
    public function __construct( $path, $seconds,$session_name, $id = null){
        $this->path = $path;
        $this->seconds = $seconds;
        $this->session_name = $session_name;
        if($id!==null){
			$this->setId($id);
		}
        $this->gc($this->seconds);
    }
    public function open($savePath, $sessionName){
        return true;
    }
    public function close(){
        return true;
    }
    public function read($sessionId){
        $path = $this->path.'/'.$sessionId;
        if (file_exists($path)) {		 
            //    return file_get_contents($path );
            $contents='';
			$fp = fopen($path, 'rb');
			//$contents = fread($fp, filesize($path));
			//fseek($fp, $seek_start);
			while(!feof($fp)) {			
			//	set_time_limit(0);//reset time limit for big files
				$contents.=fread($fp, 8192) ;
			}
			
			//var_dump($contents);
			fclose($fp);
			return $contents;
        }
        return '';
    }
    public function write($sessionId, $data){
        $path = $this->path.'/'.$sessionId;
		//file_put_contents($path,$data);
		$fp = fopen($path, "wb");
        fwrite($fp, $data);
        fclose($fp);
        return true;
    }
    public function destroy($sessionId){
        $path = $this->path.'/'.$sessionId;
        if(file_exists($path)){
			unlink($path);
		}
        return true;
    }
    public function gc($lifetime){
        if(is_dir($this->path) === false) {
			return false;
		}
		try {
			$Resource = opendir($this->path);
			$Found = array();
			while(false !== ($Item = readdir($Resource))) {
				if($Item === "." || $Item === "..") {
					continue;
				}
				//if($Recursive === true && is_dir($Item)) {
				//	$Found[] = readDirectory($Directory . $Item);
				//}else				{
				//		$Found[] = $Directory . $Item;
				//}
				$path = $this->path.'/'.$Item; 
				if(!is_dir($path) && filemtime($path) <= (time()-($this->seconds )) ) {
					unlink( $path);
				}
			}
		}catch(Exception $e) {
			return false;
		} 
    }
    public function random($length = 16){
		//return hash("sha256",rand());
		return bin2hex(openssl_random_pseudo_bytes($length));
        /*
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
        */
    }
    protected function generateSessionId(){
       //return hash("sha512",rand());
		//return hash("sha256",rand());
		return $this->random(40);
    }
    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->id = $this->isValidId($id) ? $id : $this->generateSessionId();
    }
    public function isValidId($id){
        //return is_string($id) && ctype_alnum($id) ;
        return  ctype_alnum($id) ;
    }
    public function exists($key){
		return isset($this->attributes[$key]);
    }
    public function has($key){
        return isset($this->attributes[$key]) && $this->attributes[$key]!==null;
    }
    public function get($key, $default = null){
       return isset($this->attributes[$key])?$this->attributes[$key]:$default;
    }
    public function put($key, $value = null){
        $this->attributes[$key]=$value;
    }
    public function set($key, $value = null){
        $this->put($key,$value);
    }
    public function remove($key){
        unset ($this->attributes[$key]);
    }
    public function forget($key){
        self::remove($key);
    }
	public function flush( ){
        $this->attributes = [];
    }
    public function regenerate( ){
        $this->setId($this->generateSessionId());
    }
    public function migrate( ){
        $this->regenerate();
        //$this->save();
    }
    public function token(){
       return $this->attributes['_token'] ;
    }
    public function regenerateToken(){
        $this->put('_token', $this->random(40));
    }
    public function start(){
		$data=$this->read($this->getId());
		if($data!==''){
			$this->attributes =   unserialize($data ) ;
		}
		if (! $this->has('_token')) {
            $this->regenerateToken();
        }
         
			set_cookie( $this->session_name , $this->getId() ,time()+$this->seconds);
   
        return $this->started = true;
    }
    public function save(){	
		//$this->write($this->getId(),   @serialize($this->attributes)  );
		$this->write($this->getId(),   serialize_fast($this->attributes)  );
    }
    public function destroy_current( ){
        $path = $this->path.'/'.$this->getId();
        if(file_exists($path)){
			unlink($path);
		}
        return true;
    }
    public function restart(){	
		$this->flush();
		$this->destroy($this->getId());
		$this->regenerate();
		$this->start();
	}
}
class Session {
	public static function get($key) {
		return Route::$request->session->get($key);
	}
	public static function put($key, $value){
		return Route::$request->session->put($key, $value);
	}
	public static function forget($key ){
		return Route::$request->session->forget($key );
	}
	public static function has($key ){
		return Route::$request->session->has($key );
	}
}
function session(){
	return return Route::$request->session;
}
