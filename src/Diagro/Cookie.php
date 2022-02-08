<?php
namespace Diagro\Web\Diagro;

class Cookie
{


    public static function isQueued(string $name) : bool
    {
        $queued = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
        /** @var \Symfony\Component\HttpFoundation\Cookie $cookie */
        foreach($queued as $cookie) {
            if($cookie->getName() == $name) return true;
        }

        return false;
    }


    public static function getQueued(string $name) : ?\Symfony\Component\HttpFoundation\Cookie
    {
        $queued = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
        /** @var \Symfony\Component\HttpFoundation\Cookie $cookie */
        foreach($queued as $cookie) {
            if($cookie->getName() == $name) return $cookie;
        }

        return null;
    }


}