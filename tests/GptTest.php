<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Gpt;

class GptTest extends TestCase
{
    public function test_GetAnswer追加パラメーターなし(): void
    {
        $gpt = new Gpt("dummy_api_key", "gpt-4.1");

        $answer = $gpt->getAnswer(
            "あなたは日本のコメディアンです。",
            "自己紹介をしてください。",
        );
        $this->assertNotEmpty($answer);
    }

    public function test_GetAnswer追加パラメーターあり(): void
    {
        $gpt = new Gpt("dummy_api_key", "gpt-4.1");

        $answer = $gpt->getAnswer(
            "あなたは日本のコメディアンです。",
            "自己紹介をしてください。",
            [
                "reasoning_effort" => "minimal"
            ],
        );
        $this->assertNotEmpty($answer);
    }
}
