<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\mytools\Line;

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
}
