<?php
namespace Diagro\Web\Diagro;

class Cookie
{


    public static function isQueued(string $name) : bool
    {
        $queued = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
        /** @var \Symfony\Component\HttpFoundation\Cookie $cookie */
        foreach($queued as $cookie) {
            if($cookie->getName() == $name && ! $cookie->isCleared()) return true;
        }

        return false;
    }


    public static function getQueued(string $name) : ?\Symfony\Component\HttpFoundation\Cookie
    {
        $queued = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
        /** @var \Symfony\Component\HttpFoundation\Cookie $cookie */
        foreach($queued as $cookie) {
            if($cookie->getName() == $name && ! $cookie->isCleared()) return $cookie;
        }

        return null;
    }


    public static function shared(string $name, $value, int $minutes)
    {
        $domain = request()->getHost();
        $components = array_slice(explode('.', $domain), -2);
        $domain = implode('.', $components);
        \Illuminate\Support\Facades\Cookie::queue($name, $value, $minutes, '/', $domain);
    }


}