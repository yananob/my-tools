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

final class RaindropTest extends TestCase
{
    private string $sampleConfigPath = __DIR__ . '/configs/raindrop.test.json';
    private array $sampleConfig = [
        'access_token' => 'test_token',
        'api_endpoint' => 'https://api.example.com/v1/raindrop'
    ];

    protected function setUp(): void
    {
        // Create a dummy config file for testing
        if (!is_dir(__DIR__ . '/configs')) {
            mkdir(__DIR__ . '/configs');
        }
        file_put_contents($this->sampleConfigPath, json_encode($this->sampleConfig));
    }

    protected function tearDown(): void
    {
        // Clean up the dummy config file
        if (file_exists($this->sampleConfigPath)) {
            unlink($this->sampleConfigPath);
        }
        if (is_dir(__DIR__ . '/configs') && count(scandir(__DIR__ . '/configs')) == 2) { // . and ..
            rmdir(__DIR__ . '/configs');
        }
    }

    public function testConstructorLoadsConfig(): void
    {
        $raindrop = new Raindrop($this->sampleConfigPath);
        $this->assertInstanceOf(Raindrop::class, $raindrop);
    }

    public function testConstructorThrowsExceptionIfAccessTokenMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Access token not found in Raindrop config.');
        $badConfig = ['api_endpoint' => 'https://api.example.com/v1/raindrop'];
        file_put_contents($this->sampleConfigPath, json_encode($badConfig));
        new Raindrop($this->sampleConfigPath);
    }

    public function testAddSuccessfully(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['result' => true, 'item' => ['id' => 123, 'link' => 'http://example.com']])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack, 'base_uri' => $this->sampleConfig['api_endpoint']]); // Added base_uri to ensure mock is hit

        $raindrop = new Raindrop($this->sampleConfigPath, $client);
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

        $raindrop = new Raindrop($this->sampleConfigPath, $client);
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

        $raindrop = new Raindrop($this->sampleConfigPath, $client);
        $raindrop->add('http://example.com/guzzle-fail');
    }
}
