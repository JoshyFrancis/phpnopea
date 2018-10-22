<?php
namespace App\Http\Middleware;
use Closure;
class VerifyCsrfToken{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Http\Exceptions\PostTooLargeException
     */
    public function handle($request, Closure $next, $middleware = 'user'){
		$csrf=true;
		
		$session_csrf_name= $request->session()->session_name.'_csrf';
		//if(!in_array($request->method(), ['HEAD', 'GET', 'OPTIONS'])  ){
		if(!in_array($_SERVER['REQUEST_METHOD'], ['HEAD', 'GET', 'OPTIONS'])  ){
			//$token = $request->has('_token') ?$request->input('_token'): $request->cookies->get($session_csrf_name);
			$token =  $request->input('_token') ;
				if($request->has('_token') && $request->cookies->has($session_csrf_name) && $request->input('_token')!==$request->cookies->get($session_csrf_name)){
					$token='';
				}
			if($token!==null){
				$csrf=hash_equals($request->session()->token(), $token) ;
			}
		}
		 
		if($csrf===false){
				$request->session()->destroy_current();
				remove_cookie($session_csrf_name);
			//echo 'token mismatch';
			return token_mismatch();
		}else{
			set_cookie($session_csrf_name , $request->session()->token() ,time()+$request->session()->seconds);	
			return $next($request);
		}		 
    }
    
}
