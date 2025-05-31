<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Gpt;

class GptTest extends TestCase
{
    public function testGetAnswer(): void
    {
        $gpt = new Gpt("dummy_api_key", "gpt-4.1");

        $answer = $gpt->getAnswer("あなたは日本のコメディアンです。", "自己紹介をしてください。");
        $this->assertNotEmpty($answer);
    }
}
