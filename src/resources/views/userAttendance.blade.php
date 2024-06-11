@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/userAttendance.css') }}">
@endsection

@section('content')

<div class="heading__wrapper">
    <h2 class="heading">{{ $user->name }}さんの勤怠記録</h2>
</div>

<table class="attendance-table" style="width: 100%;">
    <tr class="attendance-table__row">
        <th class="attendance-table__header">年月日</th>
        <th class="attendance-table__header">勤務開始</th>
        <th class="attendance-table__header">勤務終了</th>
        <th class="attendance-table__header">休憩時間</th>
        <th class="attendance-table__header">勤務時間</th>
    </tr>
    @foreach($attendanceRecords as $record)
    <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ date('Y-m-d', strtotime($record->clock_in)) }}</td>
        <td class="attendance-table__item">{{ $record->formatted_clock_in }}</td>
        <td class="attendance-table__item">{{ $record->formatted_clock_out }}</td>
        <td class="attendance-table__item">{{ $record->totalBreakTime }}</td>
        <td class="attendance-table__item">{{ $record->workTime }}</td>
    </tr>
    @endforeach
</table>

<div class="pagination">
    {{ $attendanceRecords->appends(['id' => $user->id])->links('pagination::bootstrap-4') }}
</div>

@endsection