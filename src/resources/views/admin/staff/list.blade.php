@extends('layouts/admin_app')

@section('css')
<link rel="stylesheet" href="{{asset('css/admin/staff/list.css')}}">
@endsection

@section('content')
<div class="staff-list">
    <h1 class="staff-list__title">スタッフ一覧</h1>

    <table class="staff-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user['user']->name }}</td>
                <td>{{ $user['email'] }}</td>
                <td><a class="staff-table__details" href="{{ route('admin.attendance.staff', $user['user']->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection