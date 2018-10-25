<?php
namespace App\Http\Middleware;
use Closure;
class ValidatePostSize{
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
		//var_dump($middleware);
		
        $max = $this->getPostMaxSize();
		//	var_dump($max);
		//var_dump($request->server('CONTENT_LENGTH'));
		///	exit;
		//$error = 'PostTooLargeException';
		//throw new \Exception($error);
        if ($max > 0 && $request->server('CONTENT_LENGTH') > $max) {
            throw new \Exception('PostTooLargeException') ;
           // exit;
        }

        return $next($request);
    }
    /**
     * Determine the server 'post_max_size' as bytes.
     *
     * @return int
     */
    protected function getPostMaxSize(){
        if (is_numeric($postMaxSize = ini_get('post_max_size'))) {
            return (int) $postMaxSize;
        }

        $metric = strtoupper(substr($postMaxSize, -1));
        $postMaxSize = (int) $postMaxSize;

        switch ($metric) {
            case 'K':
                return $postMaxSize * 1024;
            case 'M':
                return $postMaxSize * 1048576;
            case 'G':
                return $postMaxSize * 1073741824;
            default:
                return $postMaxSize;
        }
    }
}
