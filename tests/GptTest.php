<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Gpt;

class GptTest extends TestCase
{
    public function testGetAnswer(): void
    {
        $gpt = new Gpt("gpt-4.1", "dummy_api_key");

        $answer = $gpt->getAnswer("あなたは日本のコメディアンです。", "自己紹介をしてください。");
        $this->assertNotEmpty($answer);
    }
}
