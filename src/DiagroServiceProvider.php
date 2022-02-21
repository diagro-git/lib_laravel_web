<?php
namespace Diagro\Web;

use Diagro\API\API;
use Diagro\Token\ApplicationAuthenticationToken;
use Diagro\Token\Auth\TokenProvider;
use Diagro\Web\Controllers\LoginController;
use Diagro\Web\Controllers\LogoutController;
use Diagro\Web\Diagro\Cookie;
use Diagro\Web\Exception\InvalidFrontAppIdException;
use Diagro\Web\Middleware\Application;
use Diagro\Web\Middleware\Role;
use Diagro\Web\Middleware\ValidateDiagroToken;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cookie as LaravelCookie;
use Illuminate\Validation\ValidationException;

/**
 * Bridge between package and laravel backend application.
 *
 * @package Diagro\Web
 */
class DiagroServiceProvider extends ServiceProvider
{


    public function register()
    {
        $this->app->singleton(ApplicationAuthenticationToken::class, function() {
            $token = Diagro\Auth::getDiagroToken();
            return ApplicationAuthenticationToken::createFromToken($token);
        });
    }


    /**
     * Boot me up Scotty!
     *
     * @param Kernel $kernel
     * @throws BindingResolutionException
     */
    public function boot(Kernel $kernel)
    {
        //add Diagro AAT driver
        auth()->viaRequest('diagro-aat', function(Request $request) {
            if(! $request->hasCookie('aat') && ! Cookie::isQueued('aat')) return null;

            try {
                //return User Token model
                $aat = app(ApplicationAuthenticationToken::class);
                if($aat instanceof ApplicationAuthenticationToken) {
                    return $aat->user();
                }
            } catch(Exception $e) {
                return null;
            }

            return null;
        });

        //register the auth providers
        Auth::provider('token', function($app, $config) {
            return new TokenProvider($config['token_class_name']);
        });

        //configuration
        $this->mergeConfigFrom(__DIR__ . '/../configs/auth.php', 'auth');
        $this->publishes([
            __DIR__ . '/../configs/diagro.php' => config_path('diagro.php')
        ]);

        //views
        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'diagro');

        //assets
        $this->publishes([
            __DIR__.'/../public' => public_path('assets/diagro'),
        ], 'public');

        //register the routes
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->middleware('web')->group(function() use ($router) {
            $router->get('/login', [LoginController::class, 'login'])->name('login');
            $router->post('/login', [LoginController::class, 'loginProcess']);

            $router->get('/company', [LoginController::class, 'company'])->name('company');
            $router->post('/company', [LoginController::class, 'companyProcess']);

            $router->get('/logout', [LogoutController::class, 'logout'])->name('logout');
        });

        //middleware
        $router->aliasMiddleware('application', Application::class);
        $router->aliasMiddleware('role', Role::class);
        $router->aliasMiddleware('validate-token', ValidateDiagroToken::class);
        //validatie van AAT token gebeurt als eerste, nog voor deze gedecodeerd wordt.
        $kernel->prependToMiddlewarePriority(ValidateDiagroToken::class);

        //blade directives
        Blade::if('can', function ($abilities, $arguments) {
            return \request()->user()->can($abilities, $arguments);
        });

        Blade::if('canRead', function ($arguments) {
            return \request()->user()->can('read', $arguments);
        });

        Blade::if('canCreate', function ($arguments) {
            return \request()->user()->can('create', $arguments);
        });

        Blade::if('canUpdate', function ($arguments) {
            return \request()->user()->can('update', $arguments);
        });

        Blade::if('canDelete', function ($arguments) {
            return \request()->user()->can('delete', $arguments);
        });

        Blade::if('canPublish', function ($arguments) {
            return \request()->user()->can('publish', $arguments);
        });

        Blade::if('canExport', function ($arguments) {
            return \request()->user()->can('export', $arguments);
        });

        Blade::if('hasApplication', function ($application) {
            return \request()->user()->hasApplication($application);
        });

        Blade::if('hasRole', function ($role) {
            return \request()->user()->hasRole($role);
        });

        //API default error handler
        API::withFail(function($response) {
            switch($response->status())
            {
                case 406: //Invalid token
                    LaravelCookie::queue('aat', '', -1); //delete the diagro token cookie
                    redirect('login') //back to the login page bastard!
                    ->with('preferred-company', request()->user()->company()->id())
                        ->send();
                    break;
                case 400: //Invalid front app id
                    throw new InvalidFrontAppIdException();
                case 403: //Unauthorized
                    abort(403);
                case 422: //Validation failed
                    $json = $response->json();
                    if(Arr::has($json, 'errors')) {
                        throw ValidationException::withMessages($json['errors']);
                    } else {
                        abort($response->status());
                    }
                default:
                    abort($response->status());
            }
        });
    }


}