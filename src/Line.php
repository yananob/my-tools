<?php

declare(strict_types=1);

namespace yananob\MyTools;

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
    public function sendPush(
        string $bot,
        ?string $target = null,
        ?string $targetId = null,
        string $message = "",
    ): void {
        if (!array_key_exists($bot, $this->tokens)) {
            throw new \Exception("Unknown bot: {$bot}");
        }
        if (!empty($target) && !array_key_exists($target, $this->presetTargetIds)) {
            throw new \Exception("Unknown target: {$target}");
        }
        if (empty($target) && empty($targetId)) {
            throw new \Exception('Please specify $target or $targetId');
        }

        $body = [
            "to" => empty($target) ? $targetId : $this->presetTargetIds[$target],
            "messages" => [
                [
                    "type" => "text",
                    "text" => $message,
                ],
            ],
        ];
        $this->__callApi("https://api.line.me/v2/bot/message/push", $bot, $body);
    }

    public function sendReply(
        string $bot,
        string $replyToken,
        string $message,
        bool $showLoading = true,
        ?string $target = null,
        ?string $targetId = null,
    ): void {
        if (!array_key_exists($bot, $this->tokens)) {
            throw new \Exception("Unknown bot: {$bot}");
        }

        // show loading animation
        if ($showLoading) {
            if (!empty($target) && !array_key_exists($target, $this->presetTargetIds)) {
                throw new \Exception("Unknown target: {$target}");
            }
            if (empty($target) && empty($targetId)) {
                throw new \Exception('Please specify $target or $targetId');
            }

            $body = [
                "chatId" => empty($target) ? $targetId : $this->presetTargetIds[$target],
                "loadingSeconds" => 5,
            ];
            $this->__callApi("https://api.line.me/v2/bot/chat/loading/start", $bot, $body);
        }

        // reply
        $body = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => $message,
                ],
            ],
        ];
        $this->__callApi("https://api.line.me/v2/bot/message/reply", $bot, $body);
    }

    private function __callApi(string $url, string $bot, array $body): void
    {
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->tokens[$bot]}",
        ];
        $ch = curl_init();
        try {
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => json_encode($body),
            ]);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            if ($httpcode != "200") {
                $bodyVar = var_export($body);
                throw new \Exception(
                    "Failed to send message [bot: {$bot}, body: {$bodyVar}]. " .
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
