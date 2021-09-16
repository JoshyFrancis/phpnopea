<?php
namespace App\Http;
class Kernel{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    public $middleware = [
		\App\Http\Middleware\VerifyCsrfToken::class,
		\App\Http\Middleware\ValidatePostSize::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    public $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\RedirectIfAuthenticated::class,
            \App\Http\Middleware\Sample::class,
			
        ]
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    public $routeMiddleware = [
		'demo'=>\App\Http\Middleware\Sample2::class,
        /*
        'auth' => \Phpnopea\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Phpnopea\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Phpnopea\Routing\Middleware\SubstituteBindings::class,
        'can' => \Phpnopea\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Phpnopea\Routing\Middleware\ThrottleRequests::class,
        'admin' => \App\Http\Middleware\RedirectIfNotAdmin::class,
        'user2' => \App\Http\Middleware\RedirectIfNotUser2::class,
        */
    ];
}
