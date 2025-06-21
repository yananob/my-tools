<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Line;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class LineTest extends TestCase
{
    public function testSendMessage(): void
    {
        // Create a mock handler
        $mock = new MockHandler([
            new Response(200, [], json_encode([])) // Simulate a successful API call
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $tokens = ["test" => "testTOKEN"];
        $targetIds = ["test" => "testID"];
        $line = new Line($tokens, $targetIds, $client);
        $line->sendPush(
            bot: "test",
            target: "test",
            message: "[LineTest] Sent by Messaging API!",
        );
        $this->assertTrue(true); // Assertion remains the same as the original test
    }

    public function testGetTargets(): void
    {
        $tokens = ["hoge1" => "hoge1TOKEN", "hoge2" => "hoge2TOKEN", "__EOF__" => ""];
        $targetIds = ["hoge1" => "hoge1ID", "hoge2" => "hoge2ID", "__MEMO__" => "ここにグループIDやユーザーIDを指定", "__EOF__" => ""];

        // testGetTargets does not involve API calls, so no need to mock the client here.
        $line = new Line($tokens, $targetIds);
        $this->assertEquals(
            ["hoge1", "hoge2"],
            $line->getTargets()
        );
    }
}
