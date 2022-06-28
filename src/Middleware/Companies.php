<?php
namespace Diagro\Web\Middleware;

use Closure;
use Diagro\API\API;
use Diagro\API\EndpointDefinition;
use Diagro\API\RequestMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;

class Companies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->hasCookie('at')) {
            $endpoint = new EndpointDefinition(env('DIAGRO_SERVICE_AUTH_URI') . '/companies', RequestMethod::GET, $request->cookie('at'), env('DIAGRO_APP_ID'));
            $endpoint->setJsonKey(null)->noCache();
            $result = API::sync($endpoint);

            if(Arr::has($result, ['companies', 'count']) && auth()->user() != null) {
                $company_id = auth()->user()->company()->id();
                View::share('companies', Arr::where($result['companies'], fn($company) => $company['id'] != $company_id));
                View::share('companies_count', $result['count']);
            } else {
                View::share('companies', []);
                View::share('companies_count', 0);
            }
        }

        return $next($request);
    }
}
