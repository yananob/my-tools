<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Gpt;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class GptTest extends TestCase
{
    public function testGetAnswer(): void
    {
        // Create a mock handler
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'choices' => [
                    ['message' => ['content' => 'Mocked answer']]
                ]
            ]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $gpt = new Gpt("dummy_api_key", "gpt-4.1", $client);

        $answer = $gpt->getAnswer("あなたは日本のコメディアンです。", "自己紹介をしてください。");
        $this->assertEquals("Mocked answer", $answer);
    }
}
