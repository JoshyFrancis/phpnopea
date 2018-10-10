<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class HomeController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(Request $request){
				// session(['key' => 'value']);
        return view('home',['test'=>$request->url(),'arr'=>[1,2,3,4] ]);
    }
    
}
