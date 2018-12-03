<?php
namespace App;
use DB;
Class User{
	protected $table='users';
	protected $names=[];
	protected $values=[];
	public $ID=null;
	public function __set($name, $value){
		if($name==='ID'){
			return;
		}
		$p=array_search($name,$this->names);
		if($p===false){
			$this->names[]=$name;
			$p=count($this->names)-1;
		}
		
		$this->values[$p]=$value;
    }
    public function __get($name){
		if($name==='ID'){
			return $this->ID;
		}
		$p=array_search($name,$this->names);
		if($p!==false){
			return $this->values[$p];
		}
		return null;
    }
    public static function create($data){
		$user=new self;
		foreach($data as $key=>$val){
			$user->{$key}=$val;
		}
		return $user->save();
	}
    public function save(){
		if($this->ID===null){
			$sql='INSERT INTO '.$this->table.'(';
			$c=0;
			$values='';
			foreach($this->names as $name){
				if($c>0){
					$sql.=',';
					$values.=',';
				}
				$sql.=$name;
				$values.='?';
				$c+=1;
			}
			$sql.=') VALUES(';
			$sql.=$values;
			$sql.=')';
			DB::insert($sql,$this->values);
			$this->ID=DB::lastInsertId();
		}else{
			$sql='UPDATE '.$this->table.' SET ';
			$c=0;
			foreach($this->names as $name){
				if($c>0){
					$sql.=',';
				}
				$sql.=$name.'=?';
				$c+=1;
			}
			$sql.=' where ID=?';
			$this->values[]=$this->ID;
			DB::update($sql,$this->values);
			array_pop($this->values);
		}
		return $this;
	}
	public static function find($ID){
		$user=new User;
		$rows =DB::select('SELECT ID,password,username,first_name from users where ID=?' ,[$ID] );
		if(count($rows)>0){
			$user->ID=$rows[0]->ID;
			$user->username=$rows[0]->username;
			$user->first_name=$rows[0]->first_name;
			$user->password=$rows[0]->password;
		}
		return $user;
	}
}
