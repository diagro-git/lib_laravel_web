<?php
namespace Diagro\Web\Middleware;

use Closure;
use Diagro\Token\Model\User;
use Illuminate\Http\Request;

/**
 * Check if a application or applications isset for the logged in user.
 *
 * @package Diagro\Web\Middleware
 */
class Application
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ... $applications)
    {
        /** @var User $user */
        $user = $request->user();
        foreach($applications as $application) {
            if(! $user->hasApplication($application)) {
                abort(403, "No access for application $application!");
            }
        }

        return $next($request);
    }


}