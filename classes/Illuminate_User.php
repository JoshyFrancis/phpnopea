<?php
namespace App;
use DB;
Class User{
	protected $table='users';
	protected $names=[];
	protected $values=[];
	public function __set($name, $value){
		$this->names[]=$name;
		$this->values[]=$value;
    }
    public function save(){
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
		return DB::insert($sql,$this->values);
	}
}
