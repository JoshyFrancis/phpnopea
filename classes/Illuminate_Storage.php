<?php
namespace Illuminate\Support\Facades;
use StorageBase;
trait Storage{
	public static function __callStatic($method,$arguments) {
		return call_user_func_array('StorageBase::'.$method,$arguments);
	}
}
