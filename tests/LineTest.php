<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Line;

class LineTest extends TestCase
{
    public function testSendMessage(): void
    {
        $tokens = ["test" => "testTOKEN"];
        $targetIds = ["test" => "testID"];
        $line = new Line($tokens, $targetIds);
        $line->sendPush(
            bot: "test",
            target: "test",
            message: "[LineTest] Sent by Messaging API!",
        );
        $this->assertTrue(true);
    }

    public function testGetTargets(): void
    {
        $tokens = ["hoge1" => "hoge1TOKEN", "hoge2" => "hoge2TOKEN", "__EOF__" => ""];
        $targetIds = ["hoge1" => "hoge1ID", "hoge2" => "hoge2ID", "__MEMO__" => "ここにグループIDやユーザーIDを指定", "__EOF__" => ""];
        $line = new Line($tokens, $targetIds);
        $this->assertEquals(
            ["hoge1", "hoge2"],
            $line->getTargets()
        );
    }
}
