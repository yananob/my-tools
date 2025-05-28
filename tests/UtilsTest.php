<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Utils;

class UtilsTest extends TestCase
{
    public function testGetConfig(): void
    {
        $config = Utils::getConfig(__DIR__ . "/configs/config.json.test");
        $this->assertEquals("value1", $config["key1"]);
        $this->assertEquals("value2-1", $config["key2"]["key2-1"]);
    }

    public function testGetCarbonWithoutDate(): void
    {
        $now = Utils::getCarbonWithoutDate("Asia/Tokyo");
        $this->assertTrue(true);
    }

    public function testTrimFullSpace(): void
    {
        $this->assertSame("ほげほげ", Utils::trimFullSpace("　ほげほげ"));
        $this->assertSame("ほげほげ", Utils::trimFullSpace("ほげほげ　"));
        $this->assertSame("ほげ　ほげ", Utils::trimFullSpace("ほげ　ほげ"));
    }
}
