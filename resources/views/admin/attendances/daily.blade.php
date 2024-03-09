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
            <div class="timecard-title">
                <p>日別出勤状況</p>
            </div>
            <div class="timecard-selectors">s
                <input type="date" name="date" value="{{ $selectedDate }}" id="dateInput">
            </div>
            @if ($errors->any())
                <div class="alert alert-danger mt-2">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="record-list mt-5">
                <table class="table">
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
                            <th>管理者名</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody>
                        @if ($dailyAttendanceData)
                            @foreach ($dailyAttendanceData as $attendance)
                                {{-- $attendance['admin_comments']のコレクションの数が1つ以下とそれ以外 --}}
                                @if ($attendance['admin_comments']->count() <= 1)
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
                                        @if ($attendance['admin_comments']->first()->admin_id == null)
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
                                                <td>
                                                </td>
                                                <td> <button type="submit" class="btn btn-secondary btn-sm">保存</button>
                                                </td>
                                            </form>
                                        @else
                                            @foreach ($attendance['admin_comments'] as $adminComment)
                                                <td>{{ $adminComment->admin_description }}</td>
                                                <td>{{ $adminComment->admin_comment }}</td>
                                                <td>{{ $adminComment->admin->full_name }}</td>
                                                @if ($adminComment->admin->id === Auth::user()->id)
                                                    <td>
                                                        <button type="button" class="btn btn-edit" data-bs-toggle="modal"
                                                            data-bs-target="#editModal{{ $adminComment->id }}"
                                                            data-id="{{ $adminComment->id }}"
                                                            data-description="{{ $adminComment->admin_description }}"
                                                            data-comment="{{ $adminComment->admin_comment }}">
                                                            編集
                                                        </button>
                                                    </td>
                                                @else
                                                    <td>
                                                        <button type="button" class="btn btn-edit" data-bs-toggle="modal"
                                                            data-bs-target="#addCommentModal{{ $adminComment->id }}"
                                                            data-id="{{ $adminComment->id }}"
                                                            data-description="{{ $adminComment->admin_description }}"
                                                            data-comment="{{ $adminComment->admin_comment }}">
                                                            コメント追加
                                                        </button>
                                                    </td>
                                                @endif

                                                {{-- コメント追加モーダル createAdminCommentが必要 --}}

                                                <div class="modal fade"
                                                    id="addCommentModal{{ $attendance['attendance_id'] }}" tabindex="-1"
                                                    aria-labelledby="addCommentModalLabel{{ $attendance['attendance_id'] }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header modal-header-no-border">
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5 class="modal-title modal-title-centered mb-3"
                                                                    id="addCommentModalLabel">
                                                                    作業内容&コメント追加<br>
                                                                </h5>


                                                                <!-- モーダルの中身 -->
                                                                <form id="add-form-{{ $attendance['attendance_id'] }}"
                                                                    action="{{ route('admin.daily.store', $attendance['attendance_id']), $attendance['attendance_id'] }}"
                                                                    method="post">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <!-- フォームの内容 -->
                                                                    <div class="mb-3">
                                                                        <label for="admin_description"
                                                                            class="modal-label">作業内容</label>
                                                                        <textarea class="form-control" id="admin_description" name="admin_description" required></textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="admin_comment"
                                                                            class="modal-label">作業コメント</label>
                                                                        <textarea class="form-control" id="admin_comment" name="admin_comment" required></textarea>
                                                                    </div>
                                                                    <div
                                                                        class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                                        <button type="submit"
                                                                            class="btn btn-secondary">コメント追加</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                {{-- コメント編集モーダル --}}
                                                <div class="modal fade" id="editModal{{ $attendance['attendance_id'] }}"
                                                    tabindex="-1"
                                                    aria-labelledby="editModalLabel{{ $attendance['attendance_id'] }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header modal-header-no-border">
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5 class="modal-title modal-title-centered mb-3"
                                                                    id="editModalLabel">
                                                                    作業内容&コメント修正<br>
                                                                </h5>


                                                                <!-- モーダルの中身 -->
                                                                <form id="edit-form-{{ $attendance['attendance_id'] }}"
                                                                    action="{{ route('admin.daily.update', $attendance['attendance_id']), $attendance['attendance_id'] }}"
                                                                    method="post">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <!-- フォームの内容 -->
                                                                    <div class="mb-3">
                                                                        <label for="admin_description"
                                                                            class="modal-label">作業内容</label>
                                                                        <textarea class="form-control" id="admin_description" name="admin_description" required>{{ $adminComment->admin_description }}</textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="admin_comment"
                                                                            class="modal-label">作業コメント</label>
                                                                        <textarea class="form-control" id="admin_comment" name="admin_comment" required>{{ $adminComment->admin_comment }}</textarea>
                                                                    </div>
                                                                    <div
                                                                        class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                                        <button type="submit"
                                                                            class="btn btn-secondary">保存</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </tr>
                                @else
                                    {{-- ここから2つ以上のコメントのときの処理 最初のレコードのみ、出席情報表示 --}}
                                    @foreach ($attendance['admin_comments'] as $adminComment)
                                        @if ($loop->first)
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
                                                <td>{{ $adminComment->admin_description }}</td>
                                                <td>{{ $adminComment->admin_comment }}</td>
                                                <td>{{ $adminComment->admin->full_name }}</td>
                                                <td></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>{{ $adminComment->admin_description }}</td>
                                                <td>{{ $adminComment->admin_comment }}</td>
                                                <td>{{ $adminComment->admin->full_name }}</td>
                                                @if ($loop->last)
                                                    @if ($adminComment->admin->id === Auth::user()->id)
                                                        <td>
                                                            <button type="button" class="btn btn-edit"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editModal{{ $adminComment->id }}"
                                                                data-id="{{ $adminComment->id }}"
                                                                data-description="{{ $adminComment->admin_description }}"
                                                                data-comment="{{ $adminComment->admin_comment }}">
                                                                編集
                                                            </button>
                                                        </td>
                                                    @else
                                                        <td>
                                                            <button type="button" class="btn btn-edit"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#addCommentModal{{ $adminComment->id }}"
                                                                data-id="{{ $adminComment->id }}"
                                                                data-description="{{ $adminComment->admin_description }}"
                                                                data-comment="{{ $adminComment->admin_comment }}">
                                                                コメント追加
                                                            </button>
                                                        </td>
                                                    @endif

                                                    {{-- コメント追加モーダル createAdminCommentが必要 --}}

                                                    <div class="modal fade" id="addCommentModal{{ $adminComment->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="addCommentModalLabel{{ $adminComment->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header modal-header-no-border">
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <h5 class="modal-title modal-title-centered mb-3"
                                                                        id="addCommentModalLabel">
                                                                        作業内容&コメント追加<br>
                                                                    </h5>


                                                                    <!-- モーダルの中身 -->
                                                                    <form id="add-form-{{ $attendance['attendance_id'] }}"
                                                                        action="{{ route('admin.daily.store', $attendance['attendance_id']), $attendance['attendance_id'] }}"
                                                                        method="post">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <!-- フォームの内容 -->
                                                                        <div class="mb-3">
                                                                            <label for="admin_description"
                                                                                class="modal-label">作業内容</label>
                                                                            <textarea class="form-control" id="admin_description" name="admin_description" required></textarea>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="admin_comment"
                                                                                class="modal-label">作業コメント</label>
                                                                            <textarea class="form-control" id="admin_comment" name="admin_comment" required></textarea>
                                                                        </div>
                                                                        <div
                                                                            class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary">コメント追加</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    {{-- コメント編集モーダル --}}
                                                    <div class="modal fade" id="editModal{{ $adminComment->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="editModalLabel{{ $adminComment->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header modal-header-no-border">
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <h5 class="modal-title modal-title-centered mb-3"
                                                                        id="editModalLabel">
                                                                        作業内容&コメント修正<br>
                                                                    </h5>

                                                                    <!-- モーダルの中身 -->
                                                                    <form id="edit-form-{{ $adminComment->id }}"
                                                                        action="{{ route('admin.daily.update', $adminComment->id), $adminComment->id }}"
                                                                        method="post">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <!-- フォームの内容 -->
                                                                        <div class="mb-3">
                                                                            <label for="admin_description"
                                                                                class="modal-label">作業内容</label>
                                                                            <textarea class="form-control" id="admin_description" name="admin_description" required>{{ $adminComment->admin_description }}</textarea>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="admin_comment"
                                                                                class="modal-label">作業コメント</label>
                                                                            <textarea class="form-control" id="admin_comment" name="admin_comment" required>{{ $adminComment->admin_comment }}</textarea>
                                                                        </div>
                                                                        <div
                                                                            class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                                            <button type="submit"
                                                                                class="btn btn-secondary">保存</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </tr>
                                        @else
                                            <td></td>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    @php
        $baseUrl = url('admin/daily');
    @endphp

    <script src="{{ asset('js/calendar/dateChangeHandler.js') }}"></script>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = "{{ $baseUrl }}";

        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const description = this.getAttribute('data-description');
                const comment = this.getAttribute('data-comment');

                const form = document.querySelector(`#edit-form-${id}`);
                if (form) {
                    form.action = baseUrl + '/' + id;
                    form.querySelector('.admin_description').value = description;
                    form.querySelector('.admin_comment').value = comment;
                } else {
                    console.error('Form not found for id:', id);
                }
            });
        });
    });
</script>
