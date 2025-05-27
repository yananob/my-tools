<?php

declare(strict_types=1);

namespace yananob\MyTools;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

final class Raindrop
{
    private string \$accessToken;
    private string \$apiEndpoint = 'https://api.raindrop.io/rest/v1/raindrop'; // Default API endpoint

    public function __construct(string \$configPath)
    {
        \$config = Utils::getConfig(\$configPath);
        if (!isset(\$config['access_token'])) {
            throw new \InvalidArgumentException('Access token not found in Raindrop config.');
        }
        \$this->accessToken = \$config['access_token'];
        // Allow overriding API endpoint from config for testing or future API versions
        if (isset(\$config['api_endpoint'])) {
            \$this->apiEndpoint = \$config['api_endpoint'];
        }
    }

    public function add(string \$url, array \$options = []): array
    {
        \$client = new Client();
        \$headers = [
            'Authorization' => 'Bearer ' . \$this->accessToken,
            'Content-Type' => 'application/json',
        ];
        \$body = array_merge(['link' => \$url], \$options);

        try {
            \$response = \$client->post(\$this->apiEndpoint, [
                'headers' => \$headers,
                'json' => \$body,
            ]);

            if (\$response->getStatusCode() !== 200) {
                throw new \Exception("Failed to add URL to Raindrop.io: {\$url}. Status code: " . \$response->getStatusCode() . " Body: " . \$response->getBody()->getContents());
            }

            return json_decode(\$response->getBody()->getContents(), true);
        } catch (RequestException \$e) {
            \$message = "Failed to add URL to Raindrop.io: {\$url}. Guzzle error: " . \$e->getMessage();
            if (\$e->hasResponse()) {
                \$message .= " Response body: " . \$e->getResponse()->getBody()->getContents();
            }
            throw new \Exception(\$message);
        }
    }
}
