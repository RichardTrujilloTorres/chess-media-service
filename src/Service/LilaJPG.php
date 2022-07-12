<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class LilaJPG
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getImage(string $fen)
    {
        try {
            $response = $this->client->get('/image.gif', [
                'query' => [
                    'fen' => $fen,
                    'output' => 'image.gif',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'timeout' => 5000,
            ]);
        } catch (ClientException | RequestException $exception) {
            return null;
        }

        return $response->getStatusCode() === 200 ?
            $response->getBody() :
            null;
    }
}
