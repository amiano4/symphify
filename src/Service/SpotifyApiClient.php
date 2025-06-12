<?php

namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpotifyApiClient
{
    private ?string $accessToken = null;

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $clientId,
        private string $clientSecret,
    ) {}

    private function authenticate(): void
    {
        $response = $this->httpClient->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => 'grant_type=client_credentials',
        ]);

        $data = $response->toArray();
        $this->accessToken = $data['access_token'];
    }

    public function apiRequest(string $endpoint, array $params = []): array
    {
        if (!$this->accessToken) {
            $this->authenticate();
        }

        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/' . ltrim($endpoint, '/'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'query' => $params,
        ]);

        return $response->toArray();
    }
}