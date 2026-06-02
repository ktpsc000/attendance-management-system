@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{asset('css/attendance/index.css')}}">
@endsection

@section('js')
<script src="{{ asset('js/attendance.js') }}"></script>
@endsection

@section('content')
<div class="attendance-content">
    <p>{{ $user }}</p>
    <div class="attendance--status">
        <p>{{auth()->user()->getStatusLabel()}}</p>
    </div>
    <div class="attendance--date">
        <p id="current-date"></p>
    </div>
    <div class="attendance--time">
        <p id="current-time"></p>
    </div>
    <div class="attendance--clock">
        @if($user->isOffDuty())
            <form action="/attendance/clock-in" method="POST">
                @csrf
                <button type="submit">出勤</button>
            </form>
        @elseif($user->isWorking())
            <form action="/attendance/break-start" method="POST">
                @csrf
                <button type="submit">休憩入</button>
            </form>
            <form action="/attendance/clock-out" method="POST">
                @csrf
                <button type="submit">退勤</button>
            </form>
        @elseif($user->isBreak())
            <form action="/attendance/break-end" method="POST">
                @csrf
                <button type="submit">休憩戻</button>
            </form>
        @elseif($user->isFinished())
            <p>お疲れ様でした。</p>
        @endif

    </div>
</div>
@endsection