<?php
namespace App;
use Phpnopea\Foundation\Auth\User as Authenticatable;

Class User extends Authenticatable{
	protected $table='users';
	protected $primaryKey = 'ID';
	protected $fillable = [
        'name', 'email', 'password',
    ];
	//add updated_at(datetime) column to table users laravel requires it for changing password
}
