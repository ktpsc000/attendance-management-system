@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{asset('css/auth/login.css')}}">
@endsection

@section('content')

<div class="login-form">
    <h1 class="login-form__heading content__heading">ログイン</h1>
    <div class="login-form__inner">
        <form class="login-form__form" action="/login" method="post">
            @csrf
            <div class="login-form__group">
                <label for="email" class="login-form__label">メールアドレス</label>
                <input class="login-form__input" type="email" name="email" id="email" value="{{old('email')}}">
                <p class="error-message">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="login-form__group">
                <label for="password" class="login-form__label">パスワード</label>
                <input class="login-form__input" type="password" name="password" id="password">
                <p class="error-message">
                    @error('password')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <input class="login-form__btn" type="submit" value="ログインする">
        </form>
    </div>
    <a href="/register" class="login-page__register-link">会員登録はこちら</a>
</div>

@endsection