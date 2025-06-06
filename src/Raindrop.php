<?php

declare(strict_types=1);

namespace yananob\MyTools;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

class Raindrop
{
    private string $apiEndpoint = 'https://api.raindrop.io/rest/v1/raindrop'; // Default API endpoint
    private ClientInterface $client;

    public function __construct(private string $accessToken, ?string $apiEndpoint = null, ClientInterface $client = null)
    {
        if ($apiEndpoint !== null) {
            $this->apiEndpoint = $apiEndpoint;
        }
        $this->client = $client ?? new Client();
    }

    public function add(string $url, array $options = []): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ];
        $body = array_merge([
            'link' => $url,
            'pleaseParse' => new \stdClass(),
        ], $options);

        try {
            $response = $this->client->request("post", $this->apiEndpoint, [
                'headers' => $headers,
                'json' => $body,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception("Failed to add URL to Raindrop.io: {$url}. Status code: " . $response->getStatusCode() . " Body: " . $response->getBody()->getContents());
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // $e->getMessage() usually includes enough detail from Guzzle, including the response summary.
            $detailedMessage = "Failed to add URL to Raindrop.io: {$url}. Guzzle error: " . $e->getMessage();
            throw new \Exception($detailedMessage);
        }
    }
}
