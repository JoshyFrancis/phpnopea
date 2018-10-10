<?php
namespace App\Http\Middleware;
use Closure;

class Sample{
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
		
		if($middleware=='admin'){
			return redirect('/test3');
		}else{
			return $next($request);
		}
	}
}
