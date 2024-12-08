<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Gpt;

final class GptTest extends TestCase
{
    public function testGetAnswer(): void
    {
        $gpt = new Gpt(__DIR__ . "/configs/gpt.json");

        $answer = $gpt->getAnswer("あなたは日本のコメディアンです。", "自己紹介をしてください。");
        $this->assertNotEmpty($answer);
    }
}
