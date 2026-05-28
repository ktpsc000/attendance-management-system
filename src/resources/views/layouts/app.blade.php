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
            <a href="/attendance" class="header__logo">
                <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            </a>

            @auth
            <div class="header-nav">
                <a href="/login" class="header-nav__login">勤怠</a>
                <a href="/mypage" class="header-nav__mypage">勤怠一覧</a>
                <a href="/sell" class="header-nav__listing">申請</a>
                <form class="header-nav__form" action="{{route('logout')}}" method="post">
                    @csrf
                    <button class="header-nav__form--logout" type="submit">ログアウト</button>
                </form>
            </div>
            @endauth

        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>
</html>