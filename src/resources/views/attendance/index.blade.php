@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{asset('css/attendance/index.css')}}">
@endsection

@section('js')
<script src="{{ asset('js/attendance.js') }}"></script>
@endsection

@section('content')
<div class="attendance-content">
    <div class="attendance--status">
        <p>勤務外</p>
    </div>
    <div class="attendance--date">
        <p id="current-date"></p>
    </div>
    <div class="attendance--time">
        <p id="current-time"></p>
    </div>
    <form class="attendance--clock" action="/attendance" method="post">
        @csrf
    </form>
</div>
@endsection