@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">

    <h1 class="attendance-detail__title">
        勤怠詳細
    </h1>

    <form action="" method="POST">
        @csrf

        <table class="attendance-detail__table">

            @if ($errors->any())
            <p class="error-message">{{ $errors->first() }}</p>
            @endif

            <tr>
                <th>名前</th>
                <td>
                    <p>{{ $user->name }}</p>
                </td>
            </tr>

            <tr>
                <th>日付</th>
                <td>
                    <p>{{ $attendance->work_date->format('Y年') }}</p>
                    <p>{{ $attendance->work_date->format('n月j日') }}</p>
                </td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="text" name="clock_in_at" value="{{ old('clock_in_at', optional($attendance->clock_in_at)->format('H:i')) }}">
                    〜
                    <input type="text" name="clock_out_at" value="{{ old('clock_out_at', optional($attendance->clock_out_at)->format('H:i')) }}">
                </td>
            </tr>

            @foreach($attendance->breaks as $index => $break)
            <tr>
                <th>休憩</th>
                <td>
                    <input type="text" name="break_start_at[]" value="{{ old('break_start_at.' . $index, optional($break->break_start_at)->format('H:i')) }}">
                    〜
                    <input type="text" name="break_end_at[]" value="{{ old('break_end_at.' . $index, optional($break->break_end_at)->format('H:i')) }}">
                </td>
            </tr>
            @endforeach

            <tr>
                <th>休憩</th>
                <td>
                    <input type="text" name="break_start_at[]" value="{{ old('break_start_at.' . count($attendance->breaks)) }}">
                    〜
                    <input type="text" name="break_end_at[]" value="{{ old('break_end_at.' . count($attendance->breaks)) }}">
                </td>
            </tr>

            <tr>
                <th>備考</th>
                <td><textarea name="remarks">{{ old('remarks', $attendance->remarks) }}</textarea></td>
            </tr>

        </table>

        <div class="attendance-detail__btn">
            <input class="attendance-detail__btn--submit" type="submit" value="修正">
        </div>


    </form>

</div>
@endsection