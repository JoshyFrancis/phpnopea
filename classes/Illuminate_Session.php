<?php
namespace Illuminate\Support\Facades;
use Route;
trait Session{
	public static function get($key) {
		return Route::$request->session->get($key);
	}
	public static function put($key, $value){
			Route::$request->session->put($key, $value);
		//return Route::$request->session->save();
	}
	public static function forget($key ){
		return Route::$request->session->forget($key );
	}
	public static function has($key ){
		return Route::$request->session->has($key );
	}
}
