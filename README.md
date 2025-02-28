# 商品注文処理 MVC+サービスクラス+リポジトリクラス（リポジトリパターン）

## ディレクトリ構造
```
src/app/Repositories/Interfaces/… リポジトリのインターフェース
src/app/Repositories/Eloquent/… リポジトリ実装
src/app/Services/… ビジネスロジックを管理するサービスクラス
```

## 各レイヤーの役割
```
リポジトリ → データアクセス（DBとのやりとりを担当）
サービス → ビジネスロジック（注文の処理を担当）
コントローラー → クライアントからのリクエストを処理
```

## メリット
```
コントローラーがシンプルになる
データアクセスの抽象化
テストしやすい（モックの利用が可能）
ビジネスロジックを分離し、保守性が向上
```

## 処理順
```
src/app/Http/Controllers/OrderController.php
↓
src/app/Services/OrderService.php
↓
src/app/Repositories/Eloquent/OrderRepository.php
src/app/Repositories/Eloquent/Product5Repository.php
```

## 依存関係
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
データ取得をAPIやNoSQLなどに変更したい場合、サービスクラスの変更は不要で新しくリポジトリクラスを作成しsrc/app/bootstrap/providers.php（サービスプロバイダ）を書き換えるだけでよい
（依存関係逆転の原則 DIP）

### DIP（依存関係逆転の原則）とは？
上位モジュール（高レベルモジュール）は、下位モジュール（低レベルモジュール）に依存してはならない。
　→ 代わりに「抽象（インターフェース）」に依存するべき
抽象（インターフェース）は、詳細（具象クラス）に依存してはならない。
　→ 詳細（リポジトリの実装など）は、抽象（インターフェース）に依存するべき


## テストコード
```
src/tests/Unit/OrderServiceTest.php
src/tests/Unit/ProductRepositoryTest.php
```
![Image](https://github.com/user-attachments/assets/21f6a1f2-3dc2-45ef-9a6b-3bfe322584c5)