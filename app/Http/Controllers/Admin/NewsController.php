<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Phpnopea\Http\Request;
class NewsController extends Controller{
    public function index(Request $request){
        return 'NewsController index';
    }   
}
