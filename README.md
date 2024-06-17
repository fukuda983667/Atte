# Atte

簡単な勤怠管理アプリです。以下はホーム画面。

![サンプル画像](/img/sample_img.png)

## 前提条件

- Dockerがインストールされていること
- Docker Composeがインストールされていること

## 環境構築

1. リポジトリをクローンしたい任意のディレクトリで以下のコマンドを実行してください。

    ```bash
    git clone https://github.com/fukuda983667/Atte_fukuda_20240617
    ```

2. Docker Composeを使用してコンテナを作成・起動します。※Docker Descktop起動時に実行してください。

    ```bash
    docker-compose up -d --build
    ```

3. phpコンテナにログイン→`composer`をインストールします。

    ```bash
    docker-compose exec php bash
    ```
    ```
    composer install
    ```

2. `.env.example`ファイルをコピーして`.env`ファイルを作成します。

    ```bash
    cp .env.example .env
    ```

3. `.env`ファイルを編集し、必要な環境変数を設定します（11～16行目）。

   ```
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel_db
   DB_USERNAME=laravel_user
   DB_PASSWORD=laravel_pass
   ```

3. Mailtrapでメール認証機能をテストするため、アカウントを作成してください。

    https://mailtrap.io/

3. 番号の手順に従って環境変数をコピーしてください。

    ![env](/img/Mailtrap_env.png)

3. `.env`ファイルの31～36行目に先ほどコピーした値を貼り付け。37,38行目は追記してください。

   ```
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=**************
   MAIL_PASSWORD=**************
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=Atte@example.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

7. アプリケーションキーを生成します。

    ```bash
    php artisan key:generate
    ```

8. データベースのマイグレーションを実行します。

    ```bash
    php artisan migrate
    ```

8. データベースのシーディングを実行します。

    ```bash
    php artisan db:seed
    ```

9. アプリケーションがhttp://localhost で利用可能になります。

9. ユーザー登録後、MailtrapのInboxに認証メールが届くので、Verify Email Addressをクリックして認証を完了してください。

![認証メール](/img/認証メール.png)

## 仕様技術(実行環境)

- PHP : 7.4.9
- Laravel : 8.83.27
- MySQL : 8.0.26
- NGINX : 1.21.1
- docker-compose.yml : 3.8

## ER図

![ER図](/img/ER.svg)

## URL

- 開発環境(ホームページ) : http://localhost/
- phpMyAdmin : http://localhost:8080
- 日付別勤怠一覧 : http://localhost/attendance
- ユーザ一覧 : http://localhost/users
- ユーザ別勤怠一覧 : http://localhost/users/attendance?id=1
- ユーザ登録ページ : http://localhost/register
- ログインページ : http://localhost/login

## ローカルリポジトリの削除  
`git clone`したローカルリポジトリを完全に削除します。  
```
sudo rm -rf Atte_fukuda_20240617
```

