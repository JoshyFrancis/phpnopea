<?php
namespace Phpnopea\Foundation\Http\Middleware;
class TrimStrings{
    protected $except=[];
    public function handle($request, Closure $next, $middleware = 'user'){
		foreach($_REQUEST as $key=>$val){
			if (in_array($key, $this->except, true)) {
				continue;
			}
				$_REQUEST[$key]=is_string($val)?trim($val):$val;
		}
		return $next($request); 
    }
}
