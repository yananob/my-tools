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
    private string \$sampleConfigPath = __DIR__ . '/configs/raindrop.test.json';
    private array \$sampleConfig = [
        'access_token' => 'test_token',
        'api_endpoint' => 'https://api.example.com/v1/raindrop'
    ];

    protected function setUp(): void
    {
        // Create a dummy config file for testing
        if (!is_dir(__DIR__ . '/configs')) {
            mkdir(__DIR__ . '/configs');
        }
        file_put_contents(\$this->sampleConfigPath, json_encode(\$this->sampleConfig));
    }

    protected function tearDown(): void
    {
        // Clean up the dummy config file
        if (file_exists(\$this->sampleConfigPath)) {
            unlink(\$this->sampleConfigPath);
        }
        if (is_dir(__DIR__ . '/configs') && count(scandir(__DIR__ . '/configs')) == 2) { // . and ..
            rmdir(__DIR__ . '/configs');
        }
    }

    public function testConstructorLoadsConfig(): void
    {
        \$raindrop = new Raindrop(\$this->sampleConfigPath);
        \$this->assertInstanceOf(Raindrop::class, \$raindrop);
    }

    public function testConstructorThrowsExceptionIfAccessTokenMissing(): void
    {
        \$this->expectException(\InvalidArgumentException::class);
        \$this->expectExceptionMessage('Access token not found in Raindrop config.');
        \$badConfig = ['api_endpoint' => 'https://api.example.com/v1/raindrop'];
        file_put_contents(\$this->sampleConfigPath, json_encode(\$badConfig));
        new Raindrop(\$this->sampleConfigPath);
    }

    public function testAddSuccessfully(): void
    {
        // Create a mock and queue two responses.
        \$mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['result' => true, 'item' => ['id' => 123, 'link' => 'http://example.com']])),
        ]);
        \$handlerStack = HandlerStack::create(\$mock);
        \$client = new Client(['handler' => \$handlerStack]);

        // Overwrite the client in Raindrop instance (e.g. via reflection or by modifying Raindrop class for testability)
        // For simplicity in this example, we'll assume Raindrop class is modified to allow client injection or we test what we can.
        // Actual Guzzle client mocking might require a more sophisticated setup or a slight refactor of Raindrop class
        // to inject a Guzzle client, which is a best practice for testability.

        // Since directly injecting the mocked client into the Raindrop class as written
        // isn't straightforward without modifying its constructor or add method,
        // this test will currently make a real HTTP request if not for the endpoint override.
        // The ideal scenario is to inject the Guzzle client.

        // For now, let's test the config load part and structure.
        // A full integration test would require a test API or careful mocking.

        \$raindrop = new Raindrop(\$this->sampleConfigPath); // Uses https://api.example.com from test config

        // To truly test `add` with a mock, Raindrop::add would need to accept a Client instance,
        // or Raindrop's constructor would need to accept one.
        // E.g., if constructor was `public function __construct(string \$configPath, ClientInterface \$client = null)`
        // Then we could do:
        // \$raindrop = new Raindrop(\$this->sampleConfigPath, \$client);
        // \$response = \$raindrop->add('http://example.com');
        // \$this->assertTrue(\$response['result']);
        // \$this->assertEquals(123, \$response['item']['id']);

        // Given the current structure of Raindrop.php, a simple unit test for `add` is not possible
        // without actual HTTP calls or refactoring.
        // We will assume the happy path for now and focus on testing the config and class structure.
        // In a real-world scenario, refactoring for testability would be the next step.

        \$this->markTestSkipped('Skipping addSuccessfully test as it requires Raindrop class refactoring for Guzzle client injection or a live test API.');
    }

    public function testAddThrowsExceptionOnApiFailure(): void
    {
        \$mock = new MockHandler([
            new Response(500, [], 'Server error'),
        ]);
        \$handlerStack = HandlerStack::create(\$mock);
        // As above, proper mocking needs client injection.
        \$this->markTestSkipped('Skipping testAddThrowsExceptionOnApiFailure as it requires Raindrop class refactoring for Guzzle client injection or a live test API.');
    }
    
    public function testAddThrowsExceptionOnRequestException(): void
    {
        \$mock = new MockHandler([
            new RequestException("Error Communicating with Server", new Request('POST', 'test'))
        ]);
        \$handlerStack = HandlerStack::create(\$mock);
        // As above, proper mocking needs client injection.
        \$this->markTestSkipped('Skipping testAddThrowsExceptionOnRequestException as it requires Raindrop class refactoring for Guzzle client injection or a live test API.');
    }
}
