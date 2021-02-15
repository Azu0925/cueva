# CUVEA
HEW チーム２制作アプリ  

---

ポジショニングマップのリアルタイム共有・作成アプリ

## 開発環境

- php 7.3.6
- MySQL
- xampp 3.2.4
- composer 2.0.6

## 環境構築

apache環境下（デフォルトの場合はhtdocs）で以下コマンドを実行

```
git clone https://github.com/Azu0925/cueva.git

cd cueva

composer install

composer dump-autoload

```

.env.sampleファイルをコピーする。※必ずコピーする。直接編集しない。  
ファイル名を.envに変更する。  
中の設定はサンプルなのでdb名等はローカルのDBに合わせて変更してください。

### websocketサーバーの実行

websocketサーバー未実装

## 設計ドキュメント
google共有ドライブの [hew_チーム２\documents\システム設計](https://drive.google.com/drive/u/0/folders/1OdVOA8lQhCEX5xLRV1eHHmye0z2oL5iC)   

- [クラス設計](https://docs.google.com/spreadsheets/d/1DSekoGfacdyXfhJCMJ6LPnOv8QyaFyjz09yHzwA6Edw/edit#gid=0)　※まだ全部書いてない。てゆうか多分使わない
- [URI設計](https://docs.google.com/spreadsheets/d/1ntQswypzLi_ubRAX6D7sdibndYA8jIGEHbl8psTHTBg/edit#gid=0) ※まだ書いてない

## 仕様ライブラリ
- [Dotenv](https://github.com/vlucas/phpdotenv)
- [idiorm](https://idiorm.readthedocs.io/en/latest/index.html)
- [ratchat](http://socketo.me/)
- [Valitron](https://github.com/vlucas/valitron)