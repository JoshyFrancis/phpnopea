<?php
namespace Illuminate\Support\Facades;
use StorageBase;
trait Storage{
	public static function get($path) {
		return StorageBase::get($path);
	}
	public static function delete($path) {
		return StorageBase::delete($path);
	}
	public static function disk($path) {
		return StorageBase::disk($path);
	}
}
