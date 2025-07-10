<?php
/**
 * A file uploaded through a form.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UploadedFile extends \SplFileInfo {
    private $test = false;
    private $originalName;
    private $mimeType;
    private $size;
    private $error;
    public function __construct($path, $originalName, $mimeType = null, $size = null, $error = null, $test = false)    {
        $this->originalName = $this->getName($originalName);
        $this->mimeType = $mimeType ?: 'application/octet-stream';
        $this->size = $size;
        $this->error = $error ?: UPLOAD_ERR_OK;
       parent::__construct($path);
    }
    public function getClientOriginalName()    {
        return $this->originalName;
    }
    public function getClientOriginalExtension()    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }
    public function getClientMimeType()    {
        return $this->mimeType;
    }
    public function getClientSize()    {
        return $this->size;
    }
    public function getError()    {
        return $this->error;
    }
    public function isValid()    {
        $isOk = UPLOAD_ERR_OK === $this->error;
        return $this->test ? $isOk : $isOk && is_uploaded_file($this->getPathname());
    }
    protected function getName($name)    {
        $originalName = str_replace('\\', '/', $name);
        $pos = strrpos($originalName, '/');
        $originalName = false === $pos ? $originalName : substr($originalName, $pos + 1);
        return $originalName;
    }
	protected function getTargetFile($directory, $name = null)    {
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
            }
        } elseif (!is_writable($directory)) {
            throw new FileException(sprintf('Unable to write in the "%s" directory', $directory));
        }
        $target = rtrim($directory, '/\\').\DIRECTORY_SEPARATOR.(null === $name ? $this->getBasename() : $this->getName($name));	
        return $target;
    }
    public function move($directory, $name = null)    {
        if ($this->isValid()) {
            if ($this->test) {
                return parent::move($directory, $name);
            }
            $target = $this->getTargetFile($directory, $name);
            set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });
            $moved = move_uploaded_file($this->getPathname(), $target);
            restore_error_handler();
            if (!$moved) {
                throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), basename($target), strip_tags($error)));
            }
			$is_php=false;
			$tokens = token_get_all(substr(file_get_contents($target),0,1000));
			foreach ($tokens as $token) {
				if (is_array($token)) {
					if( $token[0]!==314 &&  $token[0]!==321){//T_INLINE_HTML 
						$is_php=true;
						break;
					}
				}
			}
			if($is_php){
				unlink($target);
				$error= 'Not allowed!';
				throw new Exception($error);
			}
            @chmod($target, 0644 & ~umask());//// Read and write for owner, read for everybody else
            return $target;
        }
        throw new FileException($this->getErrorMessage());
    }
    public static function getMaxFilesize()    {
        $iniMax = strtolower(ini_get('upload_max_filesize'));
        if ('' === $iniMax) {
            return PHP_INT_MAX;
        }
        $max = ltrim($iniMax, '+');
        if (0 === strpos($max, '0x')) {
            $max = \intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = \intval($max, 8);
        } else {
            $max = (int) $max;
        }
        switch (substr($iniMax, -1)) {
            case 't': $max *= 1024;
            // no break
            case 'g': $max *= 1024;
            // no break
            case 'm': $max *= 1024;
            // no break
            case 'k': $max *= 1024;
        }
        return $max;
    }
    public function getErrorMessage()    {
        static $errors = array(
            UPLOAD_ERR_INI_SIZE => 'The file "%s" exceeds your upload_max_filesize ini directive (limit is %d KiB).',
            UPLOAD_ERR_FORM_SIZE => 'The file "%s" exceeds the upload limit defined in your form.',
            UPLOAD_ERR_PARTIAL => 'The file "%s" was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_CANT_WRITE => 'The file "%s" could not be written on disk.',
            UPLOAD_ERR_NO_TMP_DIR => 'File could not be uploaded: missing temporary directory.',
            UPLOAD_ERR_EXTENSION => 'File upload was stopped by a PHP extension.',
        );
        $errorCode = $this->error;
        $maxFilesize = UPLOAD_ERR_INI_SIZE === $errorCode ? self::getMaxFilesize() / 1024 : 0;
        $message = isset($errors[$errorCode]) ? $errors[$errorCode] : 'The file "%s" was not uploaded due to an unknown error.';
        return sprintf($message, $this->getClientOriginalName(), $maxFilesize);
    }
    function check_file_uploaded_name ($filename){
		return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$filename)) ? true : false);
	}
	function store($path='',$disk=''){
		//$newfilename = $doc->store('docs');
				//$newfilename = $request->file('avatar')->store(    'avatars/'.$request->user()->id, 's3');//Specifying A Disk
		//		$newfilename = $doc->store('docs', 'public');//Specifying A Disk // docs is folder
		//$filename=$this->originalName;
		$filename=uniqid().'.'.$this->getClientOriginalExtension();
		$path2=Storage::default_disk($path,$disk).'/'.$filename;
		$dir=dirname($path2);
		if(!file_exists($dir)){
			Storage::makeDirectory($dir,0755,true,true);
		}
		$this->move($dir,$filename);
		return ($path!=''?$path.'/':'').$filename;
	}
}
