<?php
namespace App;
use Model;
Class User extends Model{
	protected $table='users';
	/*
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
	*/
}
