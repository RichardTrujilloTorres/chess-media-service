<?php

namespace App\Service;

use GuzzleHttp\Client;

class LilaJPGFactory
{
    public static function build(string $baseUrl): LilaJPG
    {
        return new LilaJPG(new Client([
            'base_uri' => $baseUrl,
            'verify' => false,
        ]));
    }
}
