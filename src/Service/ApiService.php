<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    const IGBD_API_BASE_URL = 'https://api-v3.igdb.com';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getHeadersForRequest()
    {
        return [
            'user-key' => $_ENV['IGDB_USER_KEY']
        ];
    }

    public function getNintendoGames()
    {
        $response = $this->httpClient->request(
            'POST',
            self::IGBD_API_BASE_URL . '/games',
            [
                'headers' => $this->getHeadersForRequest(),
                'body' => 'fields  id, name, slug, summary, url, cover.*;'
                    . 'where platforms = 4 & rating > 20; sort rating desc; limit 10;'
            ]
        );

        return json_decode($response->getContent());
    }

    public function getNintendoGame($gameId)
    {
        $response = $this->httpClient->request(
            'POST',
            self::IGBD_API_BASE_URL . '/games',
            [
                'headers' => $this->getHeadersForRequest(),
                'body' => 'fields  id, name, slug, summary, url; where id = ' . $gameId . ';'
            ]
        );

        $data = json_decode($response->getContent());

        if (is_array($data)) {
            return $data[0];
        }

        return [];
    }
}
