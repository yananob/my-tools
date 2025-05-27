# このコードベースについて学んだこと

このプロジェクトでは、PHP開発におけるいくつかの一般的な慣習が採用されています。

*   **依存関係管理**: `composer` がPHPパッケージの依存関係を管理するために使用されています。`composer.json`ファイルに必要なライブラリを定義し、`composer install`や`composer update`コマンドでインストール・更新します。
*   **ディレクトリ構造**: ソースコードは主に`src/`ディレクトリ内にクラスとして配置され、PHPUnitを使用したユニットテストは`tests/`ディレクトリに格納される規約になっています。
*   **設定ファイルの読み込み**: `Utils::getConfig()`というユーティリティ関数（存在すると仮定）が、`configs/`ディレクトリ（またはサンプルとして`configs_sample/`ディレクトリ）からJSON形式の設定ファイルを読み込むために使用されるようです。

---

# My Tools

便利なツール群です。

## Raindrop クラス

`Raindrop` クラスは、指定されたURLを [Raindrop.io](https://raindrop.io/) サービスに保存するための機能を提供します。

### 主な機能

*   URLを指定してRaindrop.ioに新しいブックマーク（Raindrop）として追加します。
*   オプションとして、タイトル、抜粋、タグ、コレクションなどの追加情報を指定できます。

### 使用方法

以下は、`Raindrop` クラスを使用して新しいURLを保存する基本的な例です。

```php
<?php

require 'vendor/autoload.php'; // Composerのオートローダーを読み込む

use yananob\MyTools\Raindrop;
use yananob\MyTools\Utils; // 設定ファイル読み込みに必要と仮定

// 設定ファイルのパス
\$configPath = __DIR__ . '/configs/raindrop.json'; // 実際のパスに置き換えてください

try {
    // Raindropインスタンスを作成
    \$raindrop = new Raindrop(\$configPath);

    // 保存したいURL
    \$urlToSave = 'https://www.example.com/interesting-article';

    // オプション (任意)
    \$options = [
        'title' => '興味深い記事のタイトル',
        'excerpt' => 'この記事はXとYについて述べています...',
        'tags' => ['php', 'development', 'tools'],
        // 'collectionId' => 12345 // 特定のコレクションIDを指定する場合
    ];

    // URLをRaindrop.ioに追加
    \$result = \$raindrop->add(\$urlToSave, \$options);

    echo "URLが正常に追加されました！\\n";
    print_r(\$result);

} catch (\InvalidArgumentException \$e) {
    echo "設定エラー: " . \$e->getMessage() . "\\n";
} catch (\Exception \$e) {
    echo "エラー: " . \$e->getMessage() . "\\n";
}

```

### 設定ファイル

`Raindrop` クラスを使用するには、設定ファイルが必要です。サンプルは `configs_sample/raindrop.json` にあります。
このファイルを `configs/raindrop.json` のようにコピーし、内容を編集してください。

```json
{
    "access_token": "YOUR_RAINDROP_ACCESS_TOKEN",
    "api_endpoint": "https://api.raindrop.io/rest/v1/raindrop"
}
```

*   `access_token` (必須): Raindrop.io APIにアクセスするためのあなたのアクセストークンです。Raindrop.ioのアプリ設定ページから取得できます。 **この値は機密情報ですので、リポジトリに直接コミットしないでください。**
*   `api_endpoint` (任意): APIのエンドポイントURLです。デフォルトは `https://api.raindrop.io/rest/v1/raindrop` ですが、テストや将来のAPIバージョン変更のためにオーバーライドできます。通常は変更する必要はありません。

`Utils::getConfig()` 関数（または類似のメカニズム）がこの設定ファイルを読み込み、`Raindrop` クラスのコンストラクタに渡されることを想定しています。
