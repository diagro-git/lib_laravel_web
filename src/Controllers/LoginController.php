<?php
namespace Diagro\Web\Controllers;

use Diagro\Token\ApplicationAuthenticationToken;
use Diagro\Web\Diagro\Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Psr\SimpleCache\InvalidArgumentException;

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

        return back();
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
            Cookie::queue('at', $json['at'], 60*24*365);

            if(isset($json['companies'])) {
                session()->flash('companies', $json['companies']);
                return redirect('company');
            } elseif(isset($json['aat'])) {
                Cookie::queue('aat', $json['aat'], 60*24*365);
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
                //store the company in cache
                cache()->put($token, $company['company']);
                //make the AAT cookie
                Cookie::queue('aat', $json['aat'], 60*24*365);
                return redirect('/');
            }
        }

        session()->flash('companies', $companies);
        return view('diagro::company', ['companies' => $companies]);
    }


}