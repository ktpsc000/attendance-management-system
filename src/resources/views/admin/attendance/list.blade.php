@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{asset('css/admin/attendance/list.css')}}">
@endsection

@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">{{$currentDay->format('Y年m月d日')}}の勤怠</h1>

    <div class="attendance-list__month-nav">
        <a href="{{ route('admin.attendance.list', [
            'date' => $currentDay->copy()->subDay()->toDateString()
            ]) }}">
            ← 前日
        </a>

        <div class="attendance-list__current-month">
            <img src="{{ asset('images/カレンダー.png') }}" alt="カレンダー">
            <p>{{ $currentDay->format('Y/m') }}</p>
        </div>

        <a href="{{ route('admin.attendance.list', [
            'date' => $currentDay->copy()->addDay()->toDateString()]) }}">
            翌日 →
        </a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user['user']->name }}</td>
                <td>{{ $user['attendance']?->clock_in_at?->format('H:i') }}</td>
                <td>{{ $user['attendance']?->clock_out_at?->format('H:i') }}</td>
                <td>{{ $user['attendance']?->getFormattedBreakTime() }}</td>
                <td>{{ $user['attendance']?->getFormattedWorkingTime() }}</td>
                <td><a class="attendance-table__details" href="{{ route('admin.attendance.detail', $user['attendance']->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection