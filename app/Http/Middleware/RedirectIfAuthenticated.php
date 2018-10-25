<?php
namespace App\Http\Middleware;
use Auth;
use Closure;
class RedirectIfAuthenticated{
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
		
		#auto login
		if(env('APP_DEMO','false')=='true' && !auth()->guard('user2')->check()){
			
			//Auth::loginUsingId(1, true);// Login and "remember" the given user...
			//Auth::guard('user2')->loginUsingId(1, true);
			Auth::guard('user2')->loginUsingId( intval(env('APP_DEMO_USER','1'))  , true);
			//dd( Auth::guard('user2')->user()->ID );
			return $next($request);
		}
		 
		
		if(stripos(url()->previous(),'?ref=true' )>0 ){
			
			//Session::put('backUrl', $url= url()->current(););
			$request->session()->put('backUrl', explode('?', url()->previous())[0] );
		}
				//var_dump($request->session()->get('backUrl',''));
				var_dump($request->path());
			//if (!\Auth::guard($guard)->check()) {
			if (!auth()->guard('user2')->check() && $request->path()!=='login') {
				//if($request->session()->get('backUrl','')===''){
					$request->session()->put('backUrl',url()->current());
					$request->session->save();
				//}
				//return redirect('user2/login')->withInput();
				return redirect('login');//->withInput();
			}
				
		if($request->session()->get('locked')!=null && $request->session()->get('locked') === true && stripos( $request->path(),'user_lock')===false  && stripos( $request->path(),'get_')===false && stripos( $request->path(),'API')===false){
			
			return redirect('user_lock');
		}
			return $next($request);
		
    }
    
}
