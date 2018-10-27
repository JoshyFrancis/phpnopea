<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \ViewData;

class LoginController extends Controller{
    protected $redirectTo = '/home';
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }
    public function index(Request $request){
        return view('auth.login' );
    }
    public function login(Request $request){
//		if ( auth()->guard('user2')->attempt(['email' => $request->input('email'), 'password' => $request->input('password'),'active' => 1] ,$request->input('remember'))) {	
		if ( auth()->guard('user2')->attempt( $request->all() ,$request->input('remember'))) {	
			$locked=$request->session()->get('locked');
			$backUrl=$request->session()->has('backUrl')?$request->session()->get('backUrl'):'home';
				$request->session()->put('locked', $locked);
				$request->session()->put('backUrl', $backUrl);
				$request->session->save();
			if($locked === true ){
				return redirect('/');
			}
           // var_dump($backUrl);
            // exit;
             
            if ($backUrl!='' && stripos($backUrl,'user_lock')===false ){
				return redirect( $backUrl);//->withInput();
			}else{
				//return redirect()->intended('user2/dashboard')->withInput();
				return redirect()->intended('/');//->withInput();
			}
			return redirect('home');
		}else{
			//return view('auth.login')->withErrors([ 'email'=>'These credentials do not match our records.'  ]);
			return redirect('login')->withInput()->withErrors([ 'email'=>'These credentials do not match our records.'  ]);
		}
    }
}
