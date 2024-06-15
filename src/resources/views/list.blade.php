@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')

<div class="date-navigation">
    <a href="{{ route('attendance', ['date' => $date->copy()->subDay()->toDateString()]) }}" class="nav-arrow"><</a>
    <span class="current-date">{{ $date->format('Y-m-d') }}</span>
    <a href="{{ route('attendance', ['date' => $date->copy()->addDay()->toDateString()]) }}" class="nav-arrow">></a>
</div>

<table class="attendance-table" style="width: 100%;">
    <tr class="attendance-table__row">
        <th class="attendance-table__header">名前</th>
        <th class="attendance-table__header">勤務開始</th>
        <th class="attendance-table__header">勤務終了</th>
        <th class="attendance-table__header">休憩時間</th>
        <th class="attendance-table__header">勤務時間</th>
    </tr>
    @foreach($attendanceRecords as $record)
    <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ $record->user->name }}</td>
        <td class="attendance-table__item">{{ $record->formatted_clock_in }}</td>
        <td class="attendance-table__item">{{ $record->formatted_clock_out }}</td>
        <td class="attendance-table__item">{{ $record->totalBreakTime }}</td>
        <td class="attendance-table__item">{{ $record->workTime }}</td>
    </tr>
    @endforeach
</table>

<div class="pagination">
    {{ $attendanceRecords->appends(['date' => $date->toDateString()])->links('pagination::bootstrap-4') }}
</div>

@endsection