<?php

declare(strict_types=1);

namespace yananob\mytools;

final class Line
{
    private array $tokens;
    private array $targetIds;

    public function __construct(string $configPath)
    {
        $config = Utils::getConfig($configPath);
        $this->tokens = $config["tokens"];
        $this->targetIds = $config["target_ids"];
    }

    public function sendMessage(
        string $bot,
        string $target,
        string $message,
        string $replyToken = null,
    ): void
    {
        if (!array_key_exists($bot, $this->tokens)) {
            throw new \Exception("Unknown bot: {$bot}");
        }
        if (!array_key_exists($target, $this->targetIds)) {
            throw new \Exception("Unknown target: {$target}");
        }

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->tokens[$bot]}",
        ];
        $body = [
            "to" => $this->targetIds[$target],
            "messages" => [
                [
                    "type" => "text",
                    "text" => $message,
                ],
            ],
        ];
        if (!empty($replyToken)) {
            $body["replyToken"] = $replyToken;
        }
        $ch = curl_init();
        try {
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => "https://api.line.me/v2/bot/message/push",
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => json_encode($body),
            ]);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            if ($httpcode != "200") {
                throw new \Exception(
                    "Failed to send message [bot: {$bot}, to: {$target}, message: {$message}]. " .
                        "Http response code: [{$httpcode}]"
                );
            }
        } finally {
            curl_close($ch);
        }
    }
}
