<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">

</head>

<body>

    <main class="main">
        <div class="card">
            <div class="card__header">メールアドレスをご確認ください</div>
            <div class="card__body">
                @if (session('resent'))
                <p class="card__text">{{ message }}</p>
                @endif
                <p class="card__text">もし確認用メールが送信されていない場合は、下記をクリックしてください。</p>
                <form class="form" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="button">確認メールを再送信する</button>
                </form>
            </div>
        </div>
    </main>

</body>

</html>