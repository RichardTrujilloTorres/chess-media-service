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

    public function getImage(string $fen): ?\Psr\Http\Message\StreamInterface
    {
        try {
            $response = $this->client->get('/image.gif', [
                'query' => [
                    'fen' => $fen,
                    'output' => 'image.gif',
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

    public function getGameGif(array $game): ?\Psr\Http\Message\StreamInterface
    {
        try {
            $response = $this->client->post('/game.gif', [
                'body' => json_encode($game, true),
                'query' => [
                    'output' => 'game.gif',
                ],
                'timeout' => 5000,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
        } catch (ClientException | RequestException $exception) {
            return null;
        }

        return $response->getStatusCode() === 200 ?
            $response->getBody() :
            null;
    }
}
