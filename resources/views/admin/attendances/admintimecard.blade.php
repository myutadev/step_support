@extends('layouts.main')
@section('content')
    <div class="container-fluid">

        <div class="row">
            @include('components.admin-side-menu')
        </div>

        <!-- メインコンテンツのカラム -->
        <div class="col-md-10">
            {{-- ヘッダー --}}
            @include('components.header-admin')


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
            <div class="record-list scrollable-table">
                <table class="table table-striped table-fixed my-table">
                    <thead>
                        <tr>
                            <th scope="col">日付</th>
                            <th scope="col">勤務日種別</th>
                            <th scope="col">勤怠種別</th>
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
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($monthlyAttendanceData)
                            @foreach ($monthlyAttendanceData as $date)
                                <tr>
                                    <td >{{ $date['date'] }}</td>
                                    <td>{{ $date['scheduleType'] }}</td>
                                    <td>{{ $date['attendance_type'] }}</td>
                                    <td>{{ $date['bodyTemp'] }}</td>
                                    <td>{{ $date['checkin'] }}</td>
                                    <td>{{ $date['checkout'] }}</td>
                                    <td>{{ $date['is_overtime'] }}</td>
                                    <td> {!! $date['rest'] !!} </td>
                                    <td>{!! $date['overtime'] !!}</td>
                                    <td>{{ $date['duration'] }}</td>
                                    <td>{{ $date['workDescription'] }}</td>
                                    <td class="td-max">{{ $date['workComment'] }}</td>
                                    <td class="td-mid">{!! $date['admin_comment'] !!}</td>
                                    @if (!$date['attendance_id'])
                                        @if ($date['scheduleType'] == $workDayName)
                                            <td>
                                                <!-- 欠勤登録ボタン -->
                                                <button type="button" class="btn btn-store-leave store-leave"
                                                    id="{{ $date['workSchedule_id'] }}">
                                                    欠勤
                                                </button>
                                                <!-- 欠勤登録モーダル -->
                                                <div class="modal fade" id="leaveModal-{{ $date['workSchedule_id'] }}"
                                                    tabindex="-1" aria-labelledby="leaveModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header modal-header-no-border">
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5 class="modal-title modal-title-centered"
                                                                    id="leaveModalLabel">
                                                                    欠勤データ登録
                                                                </h5>
                                                                {{-- form in modal --}}
                                                                <form id="attendance-form"
                                                                    action="{{ route('admin.store.leave', ['user_id' => $user_id, 'sched_id' => $date['workSchedule_id']]) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    <div class="mb-3">
                                                                        <input type="hidden" name="yearmonth"
                                                                            value="{{ $year . '-' . $month }}">
                                                                        <label class="mb-2">出欠種別</label><br>
                                                                        @foreach ($leaveTypes as $leaveType)
                                                                            <input type="radio"
                                                                                id="leave_radio_{{ $leaveType->id }}{{ $date['workSchedule_id'] }}"
                                                                                name="leave_type_id"
                                                                                value='{{ $leaveType->id }}',required
                                                                                @if ($loop->first) checked @endif>
                                                                            <label class="me-2"
                                                                                for="leave_radio_{{ $leaveType->id }}{{ $date['workSchedule_id'] }}">
                                                                                {{ $leaveType->name }}</label>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="admin_description"
                                                                            class="modal-label">内容</label>
                                                                        <textarea class="form-control" id="admin_description" name="admin_description" required></textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="admin_comment"
                                                                            class="modal-label">コメント</label>
                                                                        <textarea class="form-control" id="admin_comment" name="admin_comment" required></textarea>
                                                                    </div>
                                                                    <div
                                                                        class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                                        <button type="submit"
                                                                            class="btn btn-modal-attend">欠勤登録する</button>
                                                                    </div>
                                                                </form>
                                                                {{-- form in modal --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                        <td></td>
                                    @else
                                        <td>
                                            <button
                                                onclick="location.href='{{ route('admin.attendance.edit', $date['attendance_id']) }}'"
                                                class="btn btn-edit">
                                                編集
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/calendar/monthUserChangeHandler.js') }}"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const leaveButtons = document.querySelectorAll('.store-leave'); // 退勤ボタンのID
            leaveButtons.forEach(function(button) {
                const curId = button.id
                const curLeaveModal = new bootstrap.Modal(document.getElementById(`leaveModal-${curId}`));
                console.log(curLeaveModal)

                button.addEventListener('click', function() {
                    console.log('clicked');
                    curLeaveModal.show();
                });
            });
        });
    </script>

@endsection
