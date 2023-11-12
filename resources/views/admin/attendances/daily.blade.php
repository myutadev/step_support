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
            <div class="timecard-title">
                <p>日別出勤状況</p>
            </div>
            <div class="timecard-selectors">
                <form action="{{ '/attendances/timecard/submit-month' }}" method="post" id="monthForm">
                    @csrf
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" id="monthInput">
                </form>
            </div>
            <div class="record-list mt-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>受給者番号</th>
                            <th>名前</th>
                            <th>体温</th>
                            <th>出勤時間</th>
                            <th>退勤時間</th>
                            <th>休憩</th>
                            <th>残業</th>
                            <th>作業内容</th>
                            <th>作業コメント</th>
                            <th>管理者作業</th>
                            <th>管理者コメント</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($dailyAttendanceData)
                            @foreach ($dailyAttendanceData as $attendance)
                                <tr>
                                    <td>{{ $attendance['beneficialy_number'] }}</td>
                                    <td>{{ $attendance['name'] }}</td>
                                    <td>{{ $attendance['body_temp'] }}</td>
                                    <td>{{ $attendance['check_in_time'] }}</td>
                                    <td>{{ $attendance['check_out_time'] }}</td>
                                    <td> {!! $attendance['rest'] !!} </td>
                                    <td>{{ $attendance['over_time'] }}</td>
                                    <td>{{ $attendance['work_description'] }}</td>
                                    <td>{{ $attendance['work_comment'] }}</td>
                                    @if ($attendance['admin_description'] == null)
                                        <form
                                            action="{{ route('admin.daily.update', $attendance['attendance_id']), $attendance['attendance_id'] }}"
                                            method="post">
                                            @csrf
                                            @method('PATCH')
                                            <td>
                                                <textarea name="admin_description" class="admin_description" id="admin_description"></textarea>
                                            </td>
                                            <td>
                                                <textarea name="admin_comment" class="admin_comment" id="admin_comment"></textarea>
                                            </td>
                                            <td> <button type="submit" class="btn btn-secondary btn-sm">保存</button>
                                            </td>
                                        </form>
                                    @else
                                        <td>{{ $attendance['admin_description'] }}</td>
                                        <td>{{ $attendance['admin_comment'] }}</td>
                                        <td>
                                            <button type="submit" class="btn btn-edit" data-bs-toggle="modal"
                                                data-bs-target="#editModal">編集</button>
                                        </td>
                                        <!--コメント編集用モーダル -->
                                        <div class="modal fade" id="editModal" tabindex="-1"
                                            aria-labelledby="editModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header modal-header-no-border">
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h5 class="modal-title modal-title-centered mb-3"
                                                            id="editModalLabel">
                                                            作業内容&コメント修正<br>
                                                        </h5>

                                                        {{-- form in modal --}}
                                                        <form id="edit-form"
                                                            action="{{ route('admin.daily.update', $attendance['attendance_id']), $attendance['attendance_id'] }}"
                                                            method="post">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="mb-3">
                                                                <label for="work_description"
                                                                    class="modal-label">作業内容</label>
                                                                <textarea class="form-control" id="work_description" name="work_description" required>{{ $attendance['admin_description'] }}</textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="work_comment" class="modal-label">作業コメント</label>
                                                                <textarea class="form-control" id="work_comment" name="work_comment" required>{{ $attendance['admin_comment'] }}</textarea>
                                                            </div>
                                                            <div
                                                                class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                                <button type="submit" class="btn btn-secondary">保存</button>
                                                            </div>
                                                        </form>
                                                        {{-- form in modal --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- コメント編集用モーダル  ここまで-->
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>




@endsection

<script>
    // ここにJavaScriptコードを配置
    document.getElementById('monthInput').addEventListener('change', function() {
        document.getElementById('monthForm').submit();
    });
</script>
