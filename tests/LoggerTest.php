<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use yananob\MyTools\Logger;

final class LoggerTest extends TestCase
{
    private Logger $logger;

    public function setUp(): void
    {
        $this->logger = new Logger();
    }

    public function testLog(): void
    {
        $this->logger->log("hogehoge");
        $this->logger->log(1);
        $this->logger->log(1.2345);
        $this->logger->log(null);
        $this->logger->log(["this", "is", "test"]);

        $obj = new stdClass();
        $obj->prop = "prop";
        $this->logger->log($obj);

        $this->assertTrue(true);
    }
}
