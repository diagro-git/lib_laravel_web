<?php
namespace Diagro\Web\Middleware;

use Closure;
use Diagro\Token\ApplicationAuthenticationToken;
use Diagro\Web\Diagro\Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Validate if the pref_company cookie is the same value as logged in users company.
 *
 * @package App\Http\Middleware
 */
class CompanySame
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
        /** @var ApplicationAuthenticationToken $aat */
        $aat = app(ApplicationAuthenticationToken::class);
        if($aat != null && $request->hasCookie('pref_company') && $aat->company()?->id() != $request->cookie('pref_company')) {
            try {
                Auth::refreshToken($request);
            } catch(Exception|InvalidArgumentException $e)
            {
                return redirect('login');
            }
        }

        return $next($request);
    }
}