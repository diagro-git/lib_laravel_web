<?php
namespace Diagro\Web\Controllers;

use Diagro\Token\ApplicationAuthenticationToken;
use Diagro\Web\Diagro\Auth;
use Diagro\Web\Events\CompanyChanged;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

/**
 * The login controller gives two endpoints:
 *
 *  /login
 *      When no AAT is present, the auth redirects to the login route.
 *
 * /company
 *      Let the user pick a company if there are multiple companies linked with the AT token.
 *
 * @package Diagro\Web\Controllers
 */
class LoginController extends Controller
{


    public function login(Request $request)
    {
        try {
            Auth::refreshToken($request);
        } catch(Exception|InvalidArgumentException $e)
        {
            //if an aat cookie exists, delete it. Will be set again after successfull login
            if($request->hasCookie('aat')) {
                Cookie::queue('aat', '', -1);
            }

            return view('diagro::login');
        }

        return redirect('/');
    }


    public function loginProcess(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $response = Http::withHeaders([
            'X-APP-ID' => config('diagro.app_id'),
            'Accept' => 'application/json'
        ])->post(config('diagro.service_auth_uri') . '/login', $data);

        if($response->ok()) {
            $json = $response->json();
            \Diagro\Web\Diagro\Cookie::shared('at', $json['at'], 60*24*365);

            if(isset($json['companies'])) {
                session()->flash('companies', $json['companies']);
                return redirect('company');
            } elseif(isset($json['aat'])) {
                Cookie::queue('aat', $json['aat'], 60*24*365);
                \Diagro\Web\Diagro\Cookie::shared('pref_company', app(ApplicationAuthenticationToken::class)->company()->name(), 60*24*365);
                return redirect('/');
            }
        }

        return view('diagro::login');
    }


    public function company(Request $request)
    {
        if(! $request->hasCookie('at')) {
            return redirect('login');
        }

        if(($companies = session()->get('companies')) == null || empty($companies)) {
            return redirect('login');
        }

        session()->flash('companies', $companies);
        return view('diagro::company', ['companies' => $companies]);
    }


    public function companyProcess(Request $request)
    {
        if(! $request->hasCookie('at')) {
            return redirect('login');
        }

        $token = Auth::getUserToken();
        $companies = session()->get('companies');
        $company = $request->validate(['company' => ['required', 'string', Rule::in(Arr::flatten($companies))]]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-APP-ID' => config('diagro.app_id'),
            'Accept' => 'application/json'
        ])->post(config('diagro.service_auth_uri') . '/company', $company);

        if($response->ok()) {
            $json = $response->json();
            if(isset($json['aat'])) {
                //make the preferred company cookie
                \Diagro\Web\Diagro\Cookie::shared('pref_company', $company['company'], 60*24*365);
                //make the AAT cookie
                Cookie::queue('aat', $json['aat'], 60*24*365);
                return redirect('/');
            }
        }

        session()->flash('companies', $companies);
        return view('diagro::company', ['companies' => $companies]);
    }


    public function companyChange(Request $request, int $id)
    {
        //fetch newt AAT
        try {
            $old = \auth()->user()->company();
            if(Auth::refreshToken($request, $id) === true) {
                $new = ApplicationAuthenticationToken::createFromToken(\Diagro\Web\Diagro\Auth::getDiagroToken())->company();
                \Diagro\Web\Diagro\Cookie::shared('pref_company', $new->name(), 60*24*365);
                CompanyChanged::dispatch($old, $new);
                return redirect('/');
            }
        } catch(Exception $e)
        {
            return redirect('login');
        }
    }


}