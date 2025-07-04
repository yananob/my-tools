<?php

declare(strict_types=1);

namespace yananob\MyTools;

use Exception;

class Line
{
    /**
     * @param array $tokens LINE APIのアクセストークン
     * @param array $presetTargetIds プリセットされた送信先ID
     */
    public function __construct(private array $tokens, private array $presetTargetIds)
    {
    }

    /** 
     * プッシュメッセージを送信します。
     * @param string $bot ボット名
     * @param string|null $target ターゲット line.jsonで指定した宛先を指定
     * @param string|null $targetId ターゲットID eventから取得したIDなど、toを直接指定したい場合に指定
     * @param string $message 送信するメッセージ
     * @throws \Exception 不明なボット名、不明なターゲット、またはターゲットが指定されていない場合にスローされます。
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

    /**
     * リプライメッセージを送信します。
     * @param string $bot ボット名
     * @param string $replyToken リプライトークン
     * @param string $message 送信するメッセージ
     * @param array|null $quickReply クイックリプライのアイテム配列
     * @throws \Exception 不明なボット名の場合にスローされます。
     */
    public function sendReply(
        string $bot,
        string $replyToken,
        string $message,
        array $quickReply = null,
    ): void {
        if (!array_key_exists($bot, $this->tokens)) {
            throw new \Exception("Unknown bot: {$bot}");
        }

        $body = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => $message,
                ],
            ],
        ];

        if (!empty($quickReply)) {
            $body["messages"][0]["quickReply"]["items"] = $quickReply;
        }

        $this->__callApi("https://api.line.me/v2/bot/message/reply", $bot, $body);
    }

    /**
     * ローディングアニメーションを表示します。
     * @param string $bot ボット名
     * @param string|null $target ターゲット line.jsonで指定した宛先を指定
     * @param string|null $targetId ターゲットID eventから取得したIDなど、toを直接指定したい場合に指定
     * @throws \Exception 不明なターゲット、またはターゲットが指定されていない場合にスローされます。
     */
    public function showLoading(
        string $bot,
        ?string $target = null,
        ?string $targetId = null,
    ): void {
        if (!empty($target) && !array_key_exists($target, $this->presetTargetIds)) {
            throw new \Exception("Unknown target: {$target}");
        }
        if (empty($target) && empty($targetId)) {
            throw new \Exception('Please specify $target or $targetId');
        }

        $body = [
            "chatId" => empty($target) ? $targetId : $this->presetTargetIds[$target],
            "loadingSeconds" => 60,
        ];
        // MEMO: ユーザー以外（グループなど）に実行すると400エラーになるので、握りつぶす
        try {
            $this->__callApi("https://api.line.me/v2/bot/chat/loading/start", $bot, $body, ["202"]);
        } catch (Exception $e) {
        }
    }

    /**
     * LINE APIを呼び出します。
     * @param string $url APIのURL
     * @param string $bot ボット名
     * @param array $body リクエストボディ
     * @param array $allowHttpCodes 許可するHTTPステータスコードの配列
     * @throws \Exception API呼び出しに失敗した場合にスローされます。
     */
    private function __callApi(string $url, string $bot, array $body, array $allowHttpCodes = ["200"]): void
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
            if (!in_array($httpcode, $allowHttpCodes)) {
                $bodyVar = var_export($body, true);
                throw new \Exception(
                    "Failed to send message [bot: {$bot}, body: {$bodyVar}]. " .
                        "Http response code: [{$httpcode}]"
                );
            }
        } finally {
            curl_close($ch);
        }
    }

    /**
     * プリセットされた送信先IDのリストを取得します。
     * '__'で始まるターゲットは除外されます。
     * @return array ターゲットの配列
     */
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
