<?php
namespace Diagro\Web\Middleware;
use Diagro\Token\Model\User;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Check if a role isset for the logged in user.
 *
 * @package Diagro\Web\Middleware
 */
class Role
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        /** @var User $user */
        $user = $request->user();
        if(! $user->hasRole($role)) {
            abort(403, "You need the role $role!");
        }

        return $next($request);
    }


}