<?php
namespace Phpnopea\Foundation\Auth;
use Model;
Class User extends Model{
	protected $table='users';
	protected $primaryKey = 'ID';
	protected $fillable = [
        'username', 'email', 'password',
    ];
	//add updated_at(datetime) column to table users laravel requires it for changing password
}

