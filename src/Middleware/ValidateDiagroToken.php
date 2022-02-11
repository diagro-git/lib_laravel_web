<?php
namespace Diagro\Web\Middleware;

use Closure;
use Diagro\Web\Diagro\Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Validate the AAT token if present.
 *
 * @package App\Http\Middleware
 */
class ValidateDiagroToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->hasCookie('aat')) {
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('aat'),
                'x-app-id' => config('diagro.app_id')
            ];

            $response = Http::withHeaders($headers)->get(config('diagro.service_auth_uri') . '/validate/token');
            if(! $response->ok()) {
                try {
                    Auth::refreshToken($request);
                } catch(Exception|InvalidArgumentException $e)
                {
                    return redirect('login');
                }
            }
        }

        return $next($request);
    }
}
