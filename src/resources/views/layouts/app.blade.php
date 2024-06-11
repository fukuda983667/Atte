<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atee</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                Atte
                </a>
                <nav>
                    <ul class="header-nav">
                        <!-- ログイン済みかをチェックしてログイン済みの場合にnavを表示させる -->
                        @if (Auth::check())
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="/">ホーム</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="/attendance">日付一覧</a></a>
                        </li>
                        <li class="header-nav__item">
                            <a class="header-nav__link" href="/users">ユーザ一覧</a></a>
                        </li>
                        <li class="header-nav__item">
                            <form action="/logout" method="post">
                                @csrf
                                <button class="header-nav__button">ログアウト</button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="main__inner">
            @yield('content')
        </div>
    </main>

    <footer class="footer">
        <div class="footer__inner">
            <span>Atte,inc.</span>
        </div>
    </footer>
</body>

</html>