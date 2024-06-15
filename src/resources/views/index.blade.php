@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="attendance__container">
    <div class="attendance__alert">
        <p>{{ auth()->user()->name }}さんお疲れ様です!</p>
    </div>
    <div class="attendance__content">
        @if ($isWorking)
        <!-- 勤務中の場合 -->
        <form class="form">
            @csrf
            <button class="button-submit disabled" type="submit" disabled>勤務開始</button>
        </form>
        @else
        <form class="form" action="/store/clock-in" method="POST">
            @csrf
            <!-- ifで勤務中のみattendance_record_idをhiddenでフォームに含める -->
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            <input type="hidden" name="clock_in" value="{{ \Carbon\Carbon::now()->toDateTimeString() }}">
            <button class="button-submit" type="submit">勤務開始</button>
        </form>
        @endif
        @if ($isWorking && !$isOnBreak)
        <form class="form" action="/store/clock-out" method="POST">
            @csrf
            <!-- ifで勤務中のみattendance_record_idをhiddenでフォームに含める -->
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            <input type="hidden" name="clock_out" value="{{ \Carbon\Carbon::now()->toDateTimeString() }}">
            <button class="button-submit" type="submit">勤務終了</button>
        </form>
        @else
        <form class="form">
            @csrf
            <button class="button-submit disabled" type="submit" disabled>勤務終了</button>
        </form>
        @endif
    </div>
    <div class="break__content">
        @if ($isWorking && !$isOnBreak)
        <form class="form" action="/store/start-time" method="POST">
            @csrf
            <!-- ifで勤務中のみattendance_record_idをhiddenでフォームに含める -->
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            <input type="hidden" name="start_time" value="{{ \Carbon\Carbon::now()->toDateTimeString() }}">
            <button class="button-submit" type="submit">休憩開始</button>
        </form>
        @else
        <form class="form">
            <button class="button-submit disabled" type="button" disabled>休憩開始</button>
        </form>
        @endif
        @if ($isOnBreak)
        <form class="form" action="/store/end-time" method="POST">
            @csrf
            <!-- ifで勤務中のみattendance_record_idをhiddenでフォームに含める -->
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            <input type="hidden" name="end_time" value="{{ \Carbon\Carbon::now()->toDateTimeString() }}">
            <button class="button-submit" type="submit">休憩終了</button>
        </form>
        @else
        <form class="form">
            <button class="button-submit disabled" type="button" disabled>休憩終了</button>
        </form>
        @endif
    </div>

</div>

<!-- YYYY-MM-DD HH:MM:SS形式 -->
<!-- {{ \Carbon\Carbon::now()->toDateTimeString() }} -->

@endsection