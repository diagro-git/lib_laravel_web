<?php
namespace Diagro\Web\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

/**
 * Logout the current users
 *
 * @package Diagro\Web\Controllers
 */
class LogoutController extends Controller
{


    public function logout(Request $request)
    {
        if($request->hasCookie('at')) {
            $token = $request->cookie('at');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-APP-ID' => config('diagro.app_id'),
                'Accept' => 'application/json'
            ])->post(config('diagro.service_auth_uri') . '/logout');

            if($response->ok()) {
                \Diagro\Web\Diagro\Cookie::shared('at', '', -1);
                \Diagro\Web\Diagro\Cookie::shared('pref_company', '', -1);

                if($request->hasCookie('aat')) {
                    Cookie::queue('aat', '', -1);
                }
            } else {
                abort($response->status(), $response->body());
            }
        }

        //destroy session
        if(session()->isStarted()) {
            session()->flush();
            session()->regenerate(true);
        }

        return view('diagro::logout');
    }


}