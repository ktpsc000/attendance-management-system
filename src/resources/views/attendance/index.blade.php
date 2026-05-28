@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{asset('css/attendance/index.css')}}">
@endsection

@section('content')
<div class="attendance-content">
    <div class="attendance--status">
        <p>勤務外</p>
    </div>
    <div class="attendance--date">
        <p>{{ $now->locale('ja')->isoFormat('YYYY年M月D日(ddd)')}}</p>
    </div>
    <div class="attendance--time">
        <p>{{ $now->format('H:i') }}</p>
    </div>
    <form class="attendance--clock" action="/attendance" method="post">
        @csrf
    </form>
</div>
@endsection