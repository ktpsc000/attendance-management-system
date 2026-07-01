@extends('layouts/admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/stamp_correction_request/show.css') }}">
@endsection

@section('content')
<div class="attendance-detail">

    <h1 class="attendance-detail__title">
        勤怠詳細
    </h1>

    <form action="/admin/stamp_correction_request/approve/{{ $pendingRequest->id }}" method="POST">
        @csrf

        <table class="attendance-detail__table">

            <tr>
                <th>名前</th>
                <td><p>{{ $pendingRequest->user->name }}</p></td>
            </tr>

            <tr>
                <th>日付</th>
                <td>
                    <p>{{ $pendingRequest->attendance->work_date->format('Y年') }}</p>
                    <p>{{ $pendingRequest->attendance->work_date->format('n月j日') }}</p>
                </td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    <p>{{ $pendingRequest->request_clock_in_at->format('H:i') }}</p>
                    〜
                    <p>{{ $pendingRequest->request_clock_out_at->format('H:i') }}</p>
                </td>
            </tr>

            @foreach($pendingBreaks as $index => $break)
            <tr>
                <th>休憩
                    @if(!$pendingBreaks->count()-1 == 0){
                        {{$pendingBreaks->count()-1}}
                    }
                    @endif
                </th>
                <td>
                    <p>{{ $break->request_break_start_at->format('H:i') }}</p>
                    〜
                    <p>{{ $break->request_break_end_at->format('H:i') }}</p>
                </td>
            </tr>
            @endforeach

            <tr>
                <th>休憩{{$pendingBreaks->count()+1}}</th>
            </tr>

            <tr>
                <th>備考</th>
                <td><p>{{ $pendingRequest->remarks }}</p></td>
            </tr>

        </table>

        <div class="attendance-detail__btn">
            @if($pendingRequest->status == 1 )
            <input class="attendance-detail__btn--submit" type="submit" value="承認">
            @else
            <div class="attendance-detail__btn--after">
                <p>承認済み</p>
            </div>
            @endif
        </div>

    </form>

</div>
@endsection