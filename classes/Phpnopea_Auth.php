<?php
namespace Phpnopea\Support\Facades;
use Route;
trait Auth{
	public static function __callStatic($method, $parameters){
			Route::$auth->_guard();
		return Route::$auth;
	}
	public function __call($method, $parameters){
			Route::$auth->_guard();
		return Route::$auth;
    }
}
