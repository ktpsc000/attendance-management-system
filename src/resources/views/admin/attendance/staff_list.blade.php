@extends('layouts/admin_app')

@section('css')
<link rel="stylesheet" href="{{asset('css/admin/attendance/staff_list.css')}}">
@endsection

@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">{{$user->name}}さんの勤怠</h1>

    <div class="attendance-list__month-nav">
        <a href="{{ route('admin.attendance.staff', [
            'id' => $user->id,
            'year' => $currentMonth->copy()->subMonth()->year,
            'month' => $currentMonth->copy()->subMonth()->month,]) }}">
            ← 前月
        </a>

        <div class="attendance-list__current-month">
            <img src="{{ asset('images/カレンダー.png') }}" alt="カレンダー">
            <p>{{ $currentMonth->format('Y/m') }}</p>
        </div>

        <a href="{{ route('admin.attendance.staff', [
            'id' => $user->id,
            'year' => $currentMonth->copy()->addMonth()->year,
            'month' => $currentMonth->copy()->addMonth()->month,]) }}">
            翌月 →
        </a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
        @foreach($days as $day)
            <tr>
                <td>{{ $day['day']->locale('ja')->isoFormat('MM/DD(dd)') }}</td>
                <td>{{ $day['attendance']?->clock_in_at?->format('H:i') }}</td>
                <td>{{ $day['attendance']?->clock_out_at?->format('H:i') }}</td>
                <td>{{ $day['attendance']?->getFormattedBreakTime() }}</td>
                <td>{{ $day['attendance']?->getFormattedWorkingTime() }}</td>
                <td><a class="attendance-table__details" href="{{ route('admin.attendance.detail', $day['attendance']->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="export-form">
        <form action="{{ route('admin.attendance.export', [
            'id' => $user->id,
            'year' => $currentMonth->year,
            'month' => $currentMonth->month,]) }}" method="POST">
            @csrf
            <input class="export-form__btn" type="submit" value="CSV出力">
        </form>
    </div>

</div>
@endsection