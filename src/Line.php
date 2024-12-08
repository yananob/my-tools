<?php

declare(strict_types=1);

namespace yananob\mytools;

final class Line
{
    private array $tokens;
    private array $presetTargetIds;

    public function __construct(string $configPath)
    {
        $config = Utils::getConfig($configPath);
        $this->tokens = $config["tokens"];
        $this->presetTargetIds = $config["target_ids"];
    }

    /** 
     * @param $target ターゲット line.jsonで指定した宛先を指定
     * @param $targetId ターゲットID eventから取得したIDなど、toを直接指定したい場合に指定
     */
    public function sendMessage(
        string $bot,
        ?string $target = null,
        ?string $targetId = null,
        string $message = "",
        ?string $replyToken = null,
    ): void
    {
        if (!array_key_exists($bot, $this->tokens)) {
            throw new \Exception("Unknown bot: {$bot}");
        }
        if (!empty($target) && !array_key_exists($target, $this->presetTargetIds)) {
            throw new \Exception("Unknown target: {$target}");
        }
        if (empty($target) && empty($targetId)) {
            throw new \Exception('Please specify $target or $targetId');
        }

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->tokens[$bot]}",
        ];
        $body = [
            "messages" => [
                [
                    "type" => "text",
                    "text" => $message,
                ],
            ],
        ];
        if (!empty($target)) {
            $body["to"] = $this->presetTargetIds[$target];
        } else {
            $body["to"] = $targetId;
        }
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

    public function getTargets(): array
    {
        $result = [];
        foreach (array_keys($this->presetTargetIds) as $target) {
            if (str_starts_with($target, "__")) {
                continue;
            }
            $result[] = $target;
        }
        return $result;
    }
}
