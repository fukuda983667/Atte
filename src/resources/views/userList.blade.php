@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/userList.css') }}">
@endsection

@section('content')

<div class="heading__wrapper">
    <h2 class="heading">ユーザ一覧</h2>
</div>

<table class="user-table" style="width: 100%;">
    <tr class="user-table__row">
        <th class="user-table__header id">id</th>
        <th class="user-table__header name">名前</th>
        <th class="user-table__header email">メールアドレス</th>
        <th class="user-table__header status">ステータス</th>
        <th class="user-table__header"></th>
    </tr>
    @foreach($users as $user)
    <tr class="user-table__row">
        <td class="user-table__item">{{ $user->id }}</td>
        <td class="user-table__item">{{ $user->name }}</td>
        <td class="user-table__item">{{ $user->email }}</td>
        <td class="user-table__item">{{ $user->status }}</td>
        <td class="user-table__item">
            <form action="/users/attendance" method="get">
                <input type="hidden" name="id" value="{{ $user->id }}">
                <button class="button"type="submit">勤怠表</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>


<div class="pagination">
    {{ $users->links('pagination::bootstrap-4') }}
</div>

@endsection