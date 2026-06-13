@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{asset('css/stamp_correction_request/index.css')}}">
@endsection

@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">申請一覧</h1>

    <div class="attendance-list__month-nav">
        <a href="">
            承認待ち
        </a>

        <a href="">
            承認済み
        </a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
        @foreach($requests as $request)
            <tr>
                <td>{{ $request->status }}</td>
                <td>{{ $request->user->name }}</td>
                <td>{{ $request->attendance->work_date->format('Y/m/d') }}</td>
                <td>{{ $request->remarks }}</td>
                <td>{{ $request->created_at->format('Y/m/d') }}</td>
                <td><a class="attendance-table__details" href="">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection