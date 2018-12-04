<?php
namespace App;
use Model;
Class User extends Model{
	protected $table='users';
	protected $primaryKey = 'ID';
	protected $fillable = [
        'name', 'email', 'password',
    ];
	//add updated_at(datetime) column to table users laravel requires it for changing password
}
