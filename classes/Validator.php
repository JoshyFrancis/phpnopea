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
			$bail=false;
			 
			//var_dump($data);
			//exit;
		foreach($this->rules as $k=>$v){
				$k=str_replace('.*','',$k);
			if(isset($data[$k])){
				$input=$data[$k];
				$rules=explode('|',$v);
				 
				$vals=[];
					
				//var_dump($input);
					if(is_array($input)){//file
						if(is_array($input['name'])){
							foreach($input['name'] as $key=>$val){
								$val=[];
								$val['name']=$input['name'][$key];
								$val['type']=$input['type'][$key];
								$val['tmp_name']=$input['tmp_name'][$key];
								$val['error']=$input['error'][$key];
								$val['size']=$input['size'][$key];
								$val['valid']=($input['error'][$key]===0);
								$val['size']=intval($val['size']/1024);//into KB
								$vals[]=$val;
							}	
						}else{
							$val=$input;
							$val['valid']=($input['error']===0);
							$val['size']=intval($val['size']/1024);//into KB
							$vals[]=$val;
						}
					}else{
						$val=[];
						$val['data']=trim($input);
						$val['valid']=($val['data']!=='');
						$val['size']=strlen($val['data']);
						$val['type']='';
						$vals[]=$val;
					}
					//var_dump($vals);
				foreach($vals as $key=>$val){
					//var_dump($val);
						$suffix=(count($vals)>1?'.'.($key+1):'');
					foreach($rules as $rule){
						$cond='';
						if(strpos($rule,':')!==false){
							$cond=substr($rule,strpos($rule,':')+1);
							$rule=substr($rule,0,strpos($rule,':'));
						}
						if($rule==='confirmed' && $cond===''){
							$cond=$k.'_confirmation';
							$rule='same';
						}
						if($rule==='bail'){
							$bail=true;
						}
						//if($rule==='nullable'){//if required is absent it will be nullable
						//	$val['valid']=true;
						//}
						$values=str_replace(',',', ',$cond);
							$message='';
							if(isset($this->messages[$rule])){
								$message=$this->messages[$rule];
							}elseif(isset($this->messages[$k.'.'.$rule])){
								$message=$this->messages[$k.'.'.$rule];
							}
							
							$message=str_replace([':attribute',':max',':values'],[$k.$suffix,$cond,$values],$message);
						if($rule==='required' && $val['valid']===false){
							if(!isset($this->errors[$k])){
								$this->errors[$k]=[];
							}
							$this->errors[$k][]=$message===''?'The '.$k.$suffix.' field is required.':$message;
						}elseif($rule==='max' && $val['valid'] && $val['size']>intval($cond)){
							if(!isset($this->errors[$k])){
								$this->errors[$k]=[];
							}
							$this->errors[$k][]=$message===''?'The '.$k.$suffix.' '.($val['type']===''?'may not be greater than '.$cond.' characters':'must be less than '.$cond).'.':$message;
							
						}elseif($rule==='min' && $val['valid'] && $val['size']<intval($cond)){
							if(!isset($this->errors[$k])){
								$this->errors[$k]=[];
							}
							$this->errors[$k][]=$message===''?'The '.$k.$suffix.' must be at least '.$cond.($val['type']===''?' characters':'').'.':$message;
						}elseif($rule==='unique' && $val['valid'] &&  $cond!==''){
							$rows=DB::select('SELECT '.$k.' from '.$cond.' where '.$k.'=? limit 1' ,[$val['data'] ] );
							if(count($rows)>0){
								if(!isset($this->errors[$k])){
									$this->errors[$k]=[];
								}
								$this->errors[$k][]=$message===''?'The '.$k.$suffix.' has already been taken.':$message;
							}
						}elseif($rule==='image' && $val['valid'] && strpos($val['type'],$rule)===false ){
							if(!isset($this->errors[$k])){
								$this->errors[$k]=[];
							}
							$this->errors[$k][]=$message===''?'The '.$k.$suffix.' must be an image.':$message;
						}elseif($rule==='mimes' && $val['valid'] && in_array(explode('/',$val['type'])[1],explode(',',$cond))===false ){
							if(!isset($this->errors[$k])){
								$this->errors[$k]=[];
							}
							$this->errors[$k][]=$message===''?'The '.$k.$suffix.' must be one of the following types: '.$values.'.':$message;
						}elseif($rule==='email' && $val['valid'] && $cond==='' && filter_var($val['data'], FILTER_VALIDATE_EMAIL)===false){
							if(!isset($this->errors[$k])){
								$this->errors[$k]=[];
							}
							$this->errors[$k][]=$message===''?'The '.$k.$suffix.' must be a valid email address.':$message;
						}elseif($rule==='same' && $val['valid'] && isset($data[$cond]) && $data[$cond]!==$val['data']){
							if(!isset($this->errors[$k])){
								$this->errors[$k]=[];
							}
							$this->errors[$k][]=$message===''?'The '.$k.$suffix.' confirmation does not match.':$message;
						}
						
						if($bail===true && count($this->errors)>0){
							break;
						}
					}
					if($bail===true && count($this->errors)>0){
						break;
					}
				}
			}
			if($bail===true && isset($this->errors[$k])){
				break;
			}
		}
		//var_dump($this->errors);
		//exit;
		$this->success=count($this->errors)===0;
	}
    public function fails(){ 
		return !$this->success;
	}
	public function errors(){
		return $this->errors;
	}
}
