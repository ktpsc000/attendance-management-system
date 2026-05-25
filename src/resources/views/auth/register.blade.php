@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{asset('css/auth/register.css')}}">
@endsection

@section('content')


<div class="register-form">
    <h1 class="register-form__heading content__heading">会員登録</h1>
    <div class="register-form__inner">
        <form class="register-form__form" action="/register" method="post">
            @csrf
            <div class="register-form__group">
                <label for="name" class="register-form__label">ユーザー名</label>
                <input class="register-form__input" type="text" name="name" id="name" value="{{old('name')}}">
                <p class="error-message">
                    @error('name')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="register-form__group">
                <label for="email" class="register-form__label">メールアドレス</label>
                <input class="register-form__input" type="email" name="email" id="email" value="{{old('email')}}">
                <p class="error-message">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="register-form__group">
                <label for="password" class="register-form__label">パスワード</label>
                <input class="register-form__input" type="password" name="password" id="password" value="{{old('password')}}">
                <p class="error-message">
                    @error('password')
                    {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="register-form__group">
                <label for="password_confirmation" class="register-form__label">確認用パスワード</label>
                <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation">
            </div>
            <input class="register-form__btn" type="submit" value="登録する">
        </form>
    </div>
    <a href="/login" class="register-page__login-link">ログインはこちら</a>
</div>

@endsection