@extends('layouts.main')
@section('content')
    <div class="container-fluid">

        <div class="row">
            @include('components.admin-side-menu')
        </div>

        <!-- メインコンテンツのカラム -->
        <div class="col-md-10">
            {{-- ヘッダー --}}
            @include('components.header')


            <!-- テーブルのグループ -->
            <div class="timecard-selectors">
                <div class="row">
                    <!-- 月選択 -->
                    <div class="col-sm-2">
                        <p>タイムカード</p>
                        <input type="month" name="month" value="{{ $year }}-{{ $month }}" id="monthInput"
                            class="form-control mb-2 mr-sm-2">
                    </div>
                    <!-- 利用者名選択 -->
                    <div class="col-sm-2">
                        <div class="form-group mb-2 mr-sm-2">
                            <p class="mb-4">利用者名</p>
                            <select class="form-control" id="userInput" name="user">
                                @foreach ($users as $user)
                                    <option value="{{ $user['id'] }}" {{ $user['id'] == $user_id ? 'selected' : '' }}>
                                        {{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="record-list mt-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">日付</th>
                            <th scope="col">勤務日種別</th>
                            <th scope="col">体温</th>
                            <th scope="col">出勤時間</th>
                            <th scope="col">退勤時間</th>
                            <th scope="col">残業有無</th>
                            <th scope="col">休憩</th>
                            <th scope="col">残業</th>
                            <th scope="col">勤務時間</th>
                            <th scope="col">作業内容</th>
                            <th scope="col">作業コメント</th>
                            <th scope="col">管理者コメント</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($monthlyAttendanceData)
                            @foreach ($monthlyAttendanceData as $date)
                                <tr>
                                    <td>{{ $date['date'] }}</td>
                                    <td>{{ $date['scheduleType'] }}</td>
                                    <td>{{ $date['bodyTemp'] }}</td>
                                    <td>{{ $date['checkin'] }}</td>
                                    <td>{{ $date['checkout'] }}</td>
                                    <td>{{ $date['is_overtime'] }}</td>
                                    <td> {!! $date['rest'] !!} </td>
                                    <td>{{ $date['overtime'] }}</td>
                                    <td>{{ $date['duration'] }}</td>
                                    <td>{{ $date['workDescription'] }}</td>
                                    <td>{{ $date['workComment'] }}</td>
                                    <td>{!! $date['admin_comment'] !!}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/calendar/monthUserChangeHandler.js') }}"></script>
@endsection
