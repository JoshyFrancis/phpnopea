<?php
namespace App\Http\Controllers;
use Validator;
class Controller{
	public function middleware(){
		return $this;
	}
	public function except(){
	}
	public function validate($request, $rules,$messages=[]){
		$validator = Validator::make($request->all(),$rules,$messages);
		if ($validator->fails()) {
            echo redirect($request->path())->withInput()->withErrors($validator);
            die();
        }
	}
}
