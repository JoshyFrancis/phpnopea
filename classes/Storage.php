<?php
class Storage{
	public static function name($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return pathinfo($path, PATHINFO_FILENAME);
    }
	public static function basename($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return pathinfo($path, PATHINFO_BASENAME);
    }
	public static function dirname($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return pathinfo($path, PATHINFO_DIRNAME);
    }
	public static function extension($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return pathinfo($path, PATHINFO_EXTENSION);
    }
	public static function type($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return filetype($path);
    }
	public static function mimeType($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }
	public static function size($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return filesize($path);
    }
	public static function lastModified($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return filemtime($path);
    }
    public static function makeDirectory($path, $mode = 0755, $recursive = false, $force = false){
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }
        return mkdir($path, $mode, $recursive);
    }
    public static function deleteDirectory($path ){
        
    }
    public static function delete($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        if(file_exists($path)){
			unlink($path);
		}
    }
    public static function default_disk($path,$disk=''){
		global $GLOBALS;
		$public_path=$GLOBALS['public_path'];
		$storage_config= require( $public_path . '/../config/filesystems.php');	
		$disk=$disk===''?$storage_config['default']:$disk;
		$path=$storage_config['disks'][$disk]['root']  .($path!=''? '/'. $path:'') ;
		return $path;
	}
    public static function put($path,$data,$flags = 0){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
		
		$dir=dirname($path);
		if(!file_exists($dir)){
			Storage::makeDirectory($dir,0755,true,true);
		}
		return file_put_contents($path,$data,$flags);
    }
    public static function append($path,$data){
		Storage::put($path,$data,FILE_APPEND);
	}
	public static function prepend($path,$data){
		$file=Storage::default_disk($path);
		$handle = fopen($file, "r+");
		$len = strlen($data);
		$final_len = filesize($file) + $len;
		$cache_old = fread($handle, $len);
		rewind($handle);
		$i = 1;
		while (ftell($handle) < $final_len) {
		  fwrite($handle, $data);
		  $cache_new = $cache_old;
		  $cache_old = fread($handle, $len);
		  fseek($handle, $i * $len);
		  $i++;
		}
		fclose($handle);
	}
    public static function get($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
		
        if(file_exists($path)){
			return file_get_contents($path);
		}
		return '';
    }
    public static function exists($path){
		//$path=storage_path($path);
		$path=Storage::default_disk($path);
        return file_exists($path) ;
    }    
	public static function disk($disk){
		global $GLOBALS;
		$public_path=$GLOBALS['public_path'];
		$storage_config= require( $public_path . '/../config/filesystems.php');	 
		$path=$storage_config['disks'][$disk]['root'];
        return new disk($path);
    }
    public static function putFileAs($path,$file,$filename=null){//$path,UploadedFile $file,$filename
		//$path=storage_path($path);
		//if(gettype($path)=='string'){
		if(!($path instanceof UploadedFile)){
			$path=Storage::default_disk($path);
		}else{
			$filename=$file;
			$file=$path;
			$path=Storage::default_disk('');
		}
		if(!file_exists($path)){
			Storage::makeDirectory($path,0755,true,true);
		}		 
		$file->move($path,$filename);
		$path=$path.'/'.$filename;
		return $path;
    }
    public static function move($path,$new_path){
		$path=Storage::default_disk($path);
		$new_path=Storage::default_disk($new_path);
		
		$dir=dirname($new_path);
		if(!file_exists($dir)){
			Storage::makeDirectory($dir,0755,true,true);
		}
		return rename($path,$new_path );
    }
    public static function copy($path,$new_path){
		$path=Storage::default_disk($path);
		$new_path=Storage::default_disk($new_path);
		
		$dir=dirname($new_path);
		if(!file_exists($dir)){
			Storage::makeDirectory($dir,0755,true,true);
		}
		return copy($path,$new_path);
    }
}
class disk{
	//public $storage_path;
	public $path;
	function __construct($path=''){
		//$this->storage_path= storage_path('');
		//$this->path=$this->storage_path.$path.'/';
			if(!file_exists($path)){
				Storage::makeDirectory($path,0755,true,true);
			}
		$this->path= $path.'/';
	}
	public function put($file,$data){
		file_put_contents($this->path.$file ,$data);
    }
	public function get($file ){
		return file_get_contents($this->path.$file );
    }
	public function exists($file ){
		return file_exists($this->path.$file );
    }    
}
