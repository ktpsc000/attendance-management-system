@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{asset('css/auth/verify-email.css')}}">
@endsection

@section('content')

<div class="verify-content">
    <div class="verify-texts">
        <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p>メール認証を完了してください。</p>
    </div>

    <a class="verify-link" href="http://localhost:8025">認証はこちらから</a>

    <form class="verify-form" method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="verify-form__button" type="submit">認証メールを再送する</button>
    </form>
</div>

@endsection