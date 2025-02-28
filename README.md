# 商品注文処理 MVC+サービスクラス+リポジトリクラス（リポジトリパターン）

ディレクトリ構造
```
Repositories/Interfaces/… リポジトリのインターフェース
Repositories/Eloquent/… リポジトリ実装
Services/… ビジネスロジックを管理するサービスクラス
```

各レイヤーの役割
```
リポジトリ → データアクセス（DBとのやりとりを担当）
サービス → ビジネスロジック（注文の処理を担当）
コントローラー → クライアントからのリクエストを処理
```

メリット
コントローラーがシンプルになる
データアクセスの抽象化
テストしやすい（モックの利用が可能）
ビジネスロジックを分離し、保守性が向上

処理順
```
src/app/Http/Controllers/OrderController.php
↓
src/app/Services/OrderService.php
↓
src/app/Repositories/Eloquent/OrderRepository.php
src/app/Repositories/Eloquent/Product5Repository.php
```

依存関係
```
src/app/Http/Controllers/OrderController.php
↓ 依存
src/app/Services/OrderService.php
↓ 依存
src/app/Repositories/Interfaces/OrderRepositoryInterface.php
src/app/Repositories/Interfaces/ProductRepositoryInterface.php
↑ 依存（継承）
src/app/Repositories/Eloquent/OrderRepository.php
src/app/Repositories/Eloquent/Product5Repository.php
```
データ取得をAPIからなどに変更した場合、サービスクラスの変更は不要で新しくリポジトリクラスを作成しsrc/app/bootstrap/providers.php（サービスプロバイダ）を書き換えるだけでよい
（依存関係逆転の原則 DIP）


## テストコード
```
src/tests/Unit/OrderServiceTest.php
src/tests/Unit/ProductRepositoryTest.php
```