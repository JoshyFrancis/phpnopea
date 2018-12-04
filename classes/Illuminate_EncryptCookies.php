<?php
namespace Illuminate\Cookie\Middleware;
class EncryptCookies{
    public function handle($request, Closure $next, $middleware = 'user'){
		return $next($request); 
    }
}
