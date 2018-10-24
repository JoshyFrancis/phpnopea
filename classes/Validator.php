<?php
class Validator{
	private $success=false;
	private $rules=[];
	private $messages=[];
	private $errors=[];
	function __construct($data,$rules,$messages){
		$this->rules=$rules;
		$this->messages=$messages;
		$this->check($data);
	}
	function __destruct() {
		 
    }
    public static function make($data,$rules,$messages){
		return new Validator($data,$rules,$messages); 
    }
    public function check($data){
			$this->errors=[];
			//$this->errors['username']='These credentials do not match our records.' ;
			//$this->errors['title']='These title do not match.' ;
			$bail_found=false;
			//var_dump($data);
		foreach($this->rules as $k=>$v){
			if(isset($data[$k])){
				$input=$data[$k];
				$rules=explode('|',$v);
				 
				$val=[];
				
				if(is_array($input)){//file
					$val=$input;
					$val['valid']=($input['error']===0);
					$val['size']=$val['size']/1024;//into KB
				}else{
					$val['data']=trim($input);
					$val['valid']=($val['data']!=='');
					$val['size']=strlen($val['data']);
				}
				//var_dump($rules);
				foreach($rules as $rule){
					$cond='';
					if(strpos($rule,':')!==false){
						$cond=substr($rule,strpos($rule,':')+1);
						$rule=substr($rule,0,strpos($rule,':'));
					}
						$message='';
						if(isset($this->messages[$rule])){
							$message=$this->messages[$rule];
						}elseif(isset($this->messages[$k.$rule])){
							$message=$this->messages[$k.$rule];
						}
						$message=str_replace([':attribute',':max'],[$k,$cond],$message);
					if($rule==='required' && $val['valid']===false){
						if(!isset($this->errors[$k])){
							$this->errors[$k]=[];
						}
						$this->errors[$k][]=$message===''?'The '.$k.' field is required.':$message;
					}elseif($rule==='max' && $val['size']>intval($cond)){
						if(!isset($this->errors[$k])){
							$this->errors[$k]=[];
						}
						$this->errors[$k]=$message===''?'The '.$k.' must be less than '.$cond.'.':$message;
					}
				}
				
			}
		}
		//var_dump($this->errors);
		$this->success=count($this->errors)===0;
	}
    public function fails(){ 
		return !$this->success;
	}
	public function errors(){
		return $this->errors;
	}
}
