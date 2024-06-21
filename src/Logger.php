<?php

declare(strict_types=1);

namespace yananob\mytools;

final class Logger
{
    private $fp;

    public function __construct(private string $title = '')
    {
        $this->fp = fopen(getenv('LOGGER_OUTPUT') ?: 'php://stderr', 'wb');
    }

    public function log(string|array|object|null $message): void
    {
        if (is_null($message)) {
            $message = "";
        } else if (in_array(gettype($message), ["array", "object"])) {
            $message = json_encode($message);
        }
        $log_message = $message;
        if (!empty($this->title)) {
            $log_message = "[{$this->title}] {$log_message}";
        }

        fwrite($this->fp, $log_message . PHP_EOL);
    }
}
