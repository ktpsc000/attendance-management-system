<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>attendance-management-system</title>
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    @yield('css')

</head>
<body>
    <div class="app">
        <header class="header">
            <a href="/" class="header__logo">
                <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            </a>

            <div class="header-nav">
                @auth
                <form class="header-nav__form" action="{{route('logout')}}" method="post">
                    @csrf
                    <button class="header-nav__form--logout" type="submit">ログアウト</button>
                </form>
                @else
                <a href="/login" class="header-nav__login">ログイン</a>
                @endauth
                <a href="/mypage" class="header-nav__mypage">マイページ</a>
                <a href="/sell" class="header-nav__listing">出品</a>
            </div>

        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>
</html>