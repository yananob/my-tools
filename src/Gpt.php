<?php

declare(strict_types=1);

namespace yananob\MyTools;

use \GuzzleHttp\Client;

/**
 * GPT処理ラッパー
 */
class Gpt
{
    private string $secret;
    private string $model;
    private Client $client;

    public function __construct(string $configPath)
    {
        $config = Utils::getConfig($configPath);
        $this->secret = $config["secret"];
        $this->model = $config["model"];
        $this->client = new Client();
    }

    public function getAnswer(string $context, string $message): string
    {
        $logger = new Logger();
        $logger->log("Calling ChatApi: [{$context}] <{$message}>");

        $payload = [
            "model" => $this->model,
            "messages" => [
                [
                    "role" => "system",
                    "content" => $context,
                ],
                [
                    "role" => "user",
                    "content" => $message,
                ],
            ],
        ];

        $response = $this->client->post(
            "https://api.openai.com/v1/chat/completions",
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$this->secret}",
                ],
                'body' => json_encode($payload),
            ]
        );

        if (!in_array($response->getStatusCode(), [200], true)) {
            throw new \Exception("Request error: [{$response->getStatusCode()} {$response->getReasonPhrase()}");
        }
        $data = json_decode((string)$response->getBody(), false);
        $answer = $data->choices[0]->message->content;
        $logger->log("answer: {$answer}");

        return $answer;
    }
}
