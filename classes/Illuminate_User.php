<?php
namespace App;
use DB;
Class User{
	protected $names=[];
	protected $values=[];
	public function __set($name, $value){
		$this->names[]=$name;
		$this->values[]=$value;
    }
    public function save(){
		$table=get_class($this);
		$sql='INSERT into '.$table.'(';
		$c=0;
		$values='';
		foreach($This->names as $name){
			if($c>0){
				$sql.=',';
				$values.=',';
			}
			$sql.=$name;
			$values.='?';
			$c+=1;
		}
		$sql.=') Values(';
		$sql.=$values;
		$sql.=')';
		return DB::insert($sql,$This->values);
	}
}
