<?php

declare(strict_types=1);

namespace yananob\MyTools\Tests;

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Raindrop;
use yananob\MyTools\Utils;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class RaindropTest extends TestCase
{
    private array $sampleConfig = [
        'access_token' => 'test_token',
        'api_endpoint' => 'https://api.example.com/v1/raindrop'
    ];

    public function testConstructorLoadsConfig(): void
    {
        $raindrop = new Raindrop($this->sampleConfig['access_token'], $this->sampleConfig['api_endpoint']);
        $this->assertInstanceOf(Raindrop::class, $raindrop);
    }

    public function testAddSuccessfully(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['result' => true, 'item' => ['id' => 123, 'link' => 'http://example.com']])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack, 'base_uri' => $this->sampleConfig['api_endpoint']]); // Added base_uri to ensure mock is hit

        $raindrop = new Raindrop($this->sampleConfig['access_token'], $this->sampleConfig['api_endpoint'], $client);
        $response = $raindrop->add('http://example.com');

        $this->assertTrue($response['result']);
        $this->assertEquals(123, $response['item']['id']);
        $this->assertEquals('http://example.com', $response['item']['link']);
    }

    public function testAddThrowsExceptionOnApiFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Failed to add URL to Raindrop.io: http://example.com/fail. Status code: 500 Body: Internal Server Error");


        $mock = new MockHandler([
            new Response(500, [], 'Internal Server Error'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        // Ensure Guzzle does not throw its own exception for 500, so our custom logic is hit
        $client = new Client(['handler' => $handlerStack, 'base_uri' => $this->sampleConfig['api_endpoint'], 'http_errors' => false]);

        $raindrop = new Raindrop($this->sampleConfig['access_token'], $this->sampleConfig['api_endpoint'], $client);
        $raindrop->add('http://example.com/fail');
    }
    
    public function testAddThrowsExceptionOnRequestException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/Failed to add URL to Raindrop.io: .* Guzzle error: Error Communicating with Server/");

        $mock = new MockHandler([
            new RequestException("Error Communicating with Server", new Request('POST', $this->sampleConfig['api_endpoint'].'test'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack, 'base_uri' => $this->sampleConfig['api_endpoint']]);

        $raindrop = new Raindrop($this->sampleConfig['access_token'], $this->sampleConfig['api_endpoint'], $client);
        $raindrop->add('http://example.com/guzzle-fail');
    }
}
