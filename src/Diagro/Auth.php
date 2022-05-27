<?php
namespace Diagro\Web\Diagro;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Auth helpers
 *
 * @package Diagro\Web\Diagro
 */
class Auth
{


    /**
     * Try to refresh the AAT token when it's invalid.
     *
     * @param Request $request
     * @throws Exception|InvalidArgumentException
     * @return mixed
     */
    public static function refreshToken(Request $request)
    {
        if(! $request->hasCookie('at')) {
            throw new Exception("AT cookie not presence");
        }

        $token = $request->cookie('at');
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-APP-ID' => config('diagro.app_id'),
            'Accept' => 'application/json'
        ];

        if($request->hasCookie('pref_company')) {
            $headers['x-company-preffered'] = $request->cookie('pref_company');
        }

        $response = Http::withHeaders($headers)->post(config('diagro.service_auth_uri') . '/login');

        if($response->ok()) {
            $json = $response->json();
            if(isset($json['companies'])) {
                return redirect('company') //pick a company!
                    ->with('companies', $json['companies'])
                    ->send();
            } elseif(isset($json['aat'])) {
                Cookie::queue('aat', $json['aat'], 60*24*365);
            }
        } else { //login with the user token failed, so unset all the user token cookie and show form.
            self::clearAT($token);
            throw new Exception("Automatic refresh failed!");
        }
    }


    /**
     * @param string $token
     * @throws Exception| InvalidArgumentException
     */
    private static function clearAT(string $token)
    {
        //set AT token as invalid
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-APP-ID' => config('diagro.app_id'),
            'Accept' => 'application/json'
        ];
        Http::withHeaders($headers)
            ->put(config('diagro.service_auth_uri') . '/validate/revoke', [
                'reason' => 'clearAT from trying to refresh AAT token!'
            ]);

        //other clears
        Cookie::queue('at', '', -1);
        Cookie::queue('pref_company', '', -1);
    }


    /**
     * @return string|null
     */
    public static function getUserToken() : ?string
    {
        $token = \Diagro\Web\Diagro\Cookie::getQueued('at')?->getValue();
        if(empty($token)) {
            $token = \request()->cookie('at');
        }

        return $token;
    }


    /**
     * @return string|null
     */
    public static function getDiagroToken() : ?string
    {
        $token = \Diagro\Web\Diagro\Cookie::getQueued('aat')?->getValue();
        if(empty($token)) {
            $token = \request()->cookie('aat');
        }

        return $token;
    }



}