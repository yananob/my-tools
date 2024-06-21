<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use yananob\mytools\Utils as Utils;

final class UtilsTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfig(): void
    {
        $config = Utils::getConfig(dirname(__FILE__) . "/config.json.test");
        $this->assertEquals("value1", $config["key1"]);
        $this->assertEquals("value2-1", $config["key2"]["key2-1"]);
    }
}
