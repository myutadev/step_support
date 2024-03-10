@extends('layouts.main')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <!-- サイドバーのカラム -->
            @include('components.admin-side-menu')
        </div>

        <!-- メインコンテンツのカラム -->
        <div class="col-md-10">
            {{-- ヘッダー --}}
            @include('components.header-admin')


            <!-- テーブルのグループ -->
            <div class="timecard-title">
                <h3>勤務データ編集</h3>
            </div>

            <!-- フォームのエラーメッセージ -->
            @if ($errors->any())
                <div class="error">
                    <p>
                        <b>{{ count($errors) }}件のエラーがあります。</b>
                    </p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class='mt-5' action="{{ route('admin.attendance.update', $attendance->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="row mb-3">
                    <label for="contact_phone" class="col-sm-2 col-form-label">氏名</label>
                    <div class="col">
                        <p>{{ $attendance->user->full_name }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="contact_phone" class="col-sm-2 col-form-label">日付</label>
                    <div class="col">
                        <p>{{ $attendance->work_schedule->year }}年{{ $attendance->work_schedule->month }}月{{ $attendance->work_schedule->day }}日
                        </p>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="contact_phone" class="col-sm-2 col-form-label">体温</label>
                    <div class="col-2">
                        <input type="number" class="form-control" id="body_temp" name="body_temp"
                            value={{ old('body_temp', $attendance->body_temp) }}>

                    </div>
                </div>
                <div class="row mb-3">

                    <label for="is_overtime" class="col-sm-2 col-form-label">出勤区分</label>
                    <div class="col-sm-10">
                        {{-- ドロップダウンここから --}}
                        <div class="dropdown">
                            <select class="btn btn-light" name="attendance_type" id="attendance_type">
                                @foreach ($attendanceTypes as $attendanceType)
                                    <option value={{ $attendanceType->id }}
                                        {{ old('is_overtime', $attendance->attendanceType->id) == $attendanceType->id ? 'selected' : '' }}>
                                        {{ $attendanceType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- ドロップダウンここまで --}}
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="contact_phone" class="col-sm-2 col-form-label">出勤時間</label>
                    <div class="col-2">
                        <input type="time" class="form-control" id="check_in_time" name="check_in_time"
                            value="{{ old('check_in_time', \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i')) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="contact_phone" class="col-sm-2 col-form-label">退勤時間</label>
                    <div class="col-2">
                        <input type="time" class="form-control" id="check_out_time" name="check_out_time"
                            value="{{ old('check_out_time', \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i')) }}">
                    </div>
                </div>
                <div class="row mb-3">

                    <label for="is_overtime" class="col-sm-2 col-form-label">残業有無</label>
                    <div class="col-sm-10">
                        {{-- ドロップダウンここから --}}
                        <div class="dropdown">
                            <select class="btn btn-light" name="is_overtime" id="is_overtime">
                                <option value="0"
                                    {{ old('is_overtime', $attendance->is_overtime) == 0 ? 'selected' : '' }}>
                                    無
                                </option>
                                <option value="1"
                                    {{ old('is_overtime', $attendance->is_overtime) == 1 ? 'selected' : '' }}>
                                    有
                                </option>
                            </select>
                        </div>
                        {{-- ドロップダウンここまで --}}
                    </div>
                </div>
                @foreach ($attendance->rests as $rest)
                    <div class="row mb-3">
                        <label for="rest_start" class="col-sm-2 col-form-label">休憩 {{ $loop->iteration }}</label>
                        <div class="col-5">
                            <div class="row align-items-center">
                                <div class="col">
                                    <input type="time" class="form-control" id="rest_start_{{ $loop->iteration }}"
                                        name="rest_start_{{ $loop->iteration }}"
                                        value="{{ old('rest_start', \Carbon\Carbon::parse($rest->start_time)->format('H:i')) }}">
                                </div>
                                <div class="col-auto d-flex align-items-center mx-2">
                                    <p class="mb-0">-</p>
                                </div>
                                <div class="col">
                                    <input type="time" class="form-control" id="rest_end_{{ $loop->iteration }}"
                                        name="rest_end_{{ $loop->iteration }}"
                                        value="{{ old('rest_end', \Carbon\Carbon::parse($rest->end_time)->format('H:i')) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="row mb-3">
                    <label for="rest_start_add" class="col-sm-2 col-form-label">休憩追加</label>
                    <div class="col-5">
                        <div class="row align-items-center">
                            <div class="col">
                                <input type="time" class="form-control" id="rest_start_add" name="rest_start_add"
                                    value="{{ old('rest_start_add') }}">
                            </div>
                            <div class="col-auto d-flex align-items-center mx-2">
                                <p class="mb-0">-</p>
                            </div>
                            <div class="col">
                                <input type="time" class="form-control" id="rest_end_add" name="rest_end_add"
                                    value="{{ old('rest_end_add') }}">
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($attendance->overtimes as $overtime)
                    <div class="row mb-3">
                        <label for="overtime_start" class="col-sm-2 col-form-label">残業 {{ $loop->iteration }}</label>
                        <div class="col-5">
                            <div class="row align-items-center">
                                <div class="col">
                                    <input type="time" class="form-control"
                                        id="overtime_start_{{ $loop->iteration }}"
                                        name="overtime_start_{{ $loop->iteration }}"
                                        value="{{ old('overtime_start', \Carbon\Carbon::parse($overtime->start_time)->format('H:i')) }}">
                                </div>
                                <div class="col-auto d-flex align-items-center mx-2">
                                    <p class="mb-0">-</p>
                                </div>
                                <div class="col">
                                    <input type="time" class="form-control" id="overtime_end_{{ $loop->iteration }}"
                                        name="overtime_end_{{ $loop->iteration }}"
                                        value="{{ old('overtime_end', \Carbon\Carbon::parse($overtime->end_time)->format('H:i')) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="row mb-3">
                    <label for="overtime_start_add" class="col-sm-2 col-form-label">残業追加</label>
                    <div class="col-5">
                        <div class="row align-items-center">
                            <div class="col">
                                <input type="time" class="form-control" id="overtime_start_add"
                                    name="overtime_start_add" value="{{ old('overtime_start_add') }}">
                            </div>
                            <div class="col-auto d-flex align-items-center mx-2">
                                <p class="mb-0">-</p>
                            </div>
                            <div class="col">
                                <input type="time" class="form-control" id="overtime_end_add" name="overtime_end_add"
                                    value="{{ old('overtime_end_add') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 情報編集ボタン -->
                <input type="submit" class="btn btn-store mt-3" value="編集内容を保存">
            </form>
        </div>
    </div>
    </div>
@endsection
