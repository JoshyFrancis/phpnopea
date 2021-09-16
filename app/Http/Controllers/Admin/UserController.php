<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Phpnopea\Http\Request;
class UserController extends Controller{
    public function index(Request $request){
        return 'UserController index';
    }   
}
