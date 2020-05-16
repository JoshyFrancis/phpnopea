<?php
namespace Illuminate\Foundation\Auth;
use Model;
Class User extends Model{
	protected $table='users';
	protected $primaryKey = 'ID';
	protected $fillable = [
        'username', 'email', 'password',
    ];
	//add updated_at(datetime) column to table users laravel requires it for changing password
	protected static $user =null;
	public function __construct(){
		User::$user=$this;
    }
	public static function find($ID){
		$model=new self;
		$user=User::$user;
		$fillable=$user->fillable+$user->names;
			$sql='SELECT ';
			$c=0;
			foreach($fillable as $name){
				if($c>0){
					$sql.=',';
				}
				$sql.=$name;
				$c+=1;
			}
			$sql.=' FROM '.$user->table.' where '.$user->primaryKey.'=?';

		$rows =\DB::select($sql ,[$ID] );
		if(count($rows)>0){
			foreach($fillable as $name){
				$model->{$name}=$rows[0]->{$name};
			}
			$model->{$model->primaryKey}=$ID;
		}
		return $model;
	}
}

