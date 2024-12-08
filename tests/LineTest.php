<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Line;

final class LineTest extends TestCase
{
    public function testSendMessage(): void
    {
        $line = new Line(__DIR__ . "/configs/line.json");
        $line->sendMessage(
            bot: "test",
            target: "test",
            message: "[LineTest] Sent by Messaging API!",
        );
        $this->assertTrue(true);
    }

    public function testGetTargets(): void
    {
        $line = new Line(__DIR__ . "/configs/line_dummy.json");
        $this->assertEquals(
            ["hoge1", "hoge2"],
            $line->getTargets()
        );
    }
}
