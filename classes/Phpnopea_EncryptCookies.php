<?php
namespace Phpnopea\Cookie\Middleware;
class EncryptCookies{
    public function handle($request, Closure $next, $middleware = 'user'){
		return $next($request); 
    }
}
