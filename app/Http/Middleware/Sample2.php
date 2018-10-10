<?php
namespace App\Http\Middleware;
use Closure;

class Sample2{
/**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @param  string|null  $guard
 * @return mixed
 */
	public function handle($request, Closure $next , $middleware = 'user'){
		var_dump($middleware);
		
		if($request->input('head')=='demo'){
			//return redirect('/test5');
			return 'Request redirected';
		}else{
			return $next($request);
		}
	}
}
