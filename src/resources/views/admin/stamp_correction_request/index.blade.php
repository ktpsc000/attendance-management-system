@extends('layouts/admin_app')

@section('css')
<link rel="stylesheet" href="{{asset('css/admin/stamp_correction_request/index.css')}}">
@endsection

@section('content')
<div class="request-list">
    <h1 class="request-list__title">申請一覧</h1>

    <nav class="request-list__nav">
        <a href="{{route('admin.stamp_correction_request.list', ['tab' => 'pending' ])}}" class="{{ $tab === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>

        <a href="{{route('admin.stamp_correction_request.list', ['tab' => 'approved' ])}}" class="{{ $tab === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </nav>

    <table class="request-list__table">
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
                <td>{{ $request->getStatusLabel() }}</td>
                <td>{{ $request->user->name }}</td>
                <td>{{ $request->attendance->work_date->format('Y/m/d') }}</td>
                <td>{{ $request->remarks }}</td>
                <td>{{ $request->created_at->format('Y/m/d') }}</td>
                <td><a class="request-list__table--details" href="{{ route('admin.stamp_correction_request.show', $request->id) }}">詳細</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection