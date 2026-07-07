<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>attendance-management-system</title>
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    @yield('css')
    @yield('js')

</head>
<body>
    <div class="app">
        <header class="header">
            <a href="/attendance" class="header__logo">
                <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            </a>

            @auth
            @if(auth()->user()->hasVerifiedEmail())
            <div class="header-nav">
                @if(!auth()->user()->isFinished())
                <a href="/attendance" class="header-nav__attendance">勤怠</a>
                @endif

                @if(auth()->user()->isFinished())
                <a href="/attendance/list" class="header-nav__list">今月の出勤一覧</a>
                @else
                <a href="/attendance/list" class="header-nav__list">勤怠一覧</a>
                @endif

                @if(auth()->user()->isFinished())
                <a href="/stamp_correction_request/list" class="header-nav__request">申請一覧</a>
                @else
                <a href="/stamp_correction_request/list" class="header-nav__request">申請</a>
                @endif

                <form class="header-nav__form" action="{{route('logout')}}" method="post">
                    @csrf
                    <button class="header-nav__form--logout" type="submit">ログアウト</button>
                </form>
            </div>
            @endif
            @endauth

        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>
</html>