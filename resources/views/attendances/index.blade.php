@extends('layouts.main')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <!-- サイドバーのカラム -->
            <div class="col-md-2 side-bar min-vh-100">
                <div class="d-flex flex-column align-items-start">
                    <div class="app-title my-4 ms-3">
                        <h3>Step Support</h3>
                    </div>
                    <div class="menu-text-dark ms-3">
                        <p>メイン</p>
                    </div>
                    <a href="{{ '/attendances' }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-alarm"></i>
                            <p class="mb-0 ms-2">打刻</p>
                        </div>
                    </a>
                    <a href="{{ '/attendances/timecard' }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex align-items-center">
                            <i class="bi bi-card-checklist"></i>
                            <p class="mb-0 ms-2">タイムカード</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- メインコンテンツのカラム -->
            <div class="col-md-10">
                {{-- ヘッダー --}}
                @include('components.header')

                <div class="d-flex flex-column align-items-center pt-5">
                    <div class="dashboard-buttons">
                        <!-- デジタル時計と日付表示 -->
                        <div class="date-display">{{ now()->format('Y.m.d D') }}</div>
                        <div id="digital-clock" class="digital-clock pt-2"></div>

                        <!-- ボタンのグループ -->
                        <div class="col d-flex justify-content-between pt-2">
                            <!-- 出勤ボタン -->
                            <button type="button" class="btn btn-attend me-3" data-bs-toggle="modal"
                                data-bs-target="#temperatureModal">
                                出勤
                            </button>

                            <!-- 体温入力モーダル -->
                            <div class="modal fade" id="temperatureModal" tabindex="-1"
                                aria-labelledby="temperatureModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-no-border">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="modal-title modal-title-centered" id="temperatureModalLabel">
                                                おはようございます!<br>今日の体温を入力してください。
                                            </h5>

                                            {{-- form in modal --}}
                                            <form id="attendance-form" action="{{ route('attendances.checkin') }}"
                                                method="post">
                                                @csrf
                                                <div class="mb-3">
                                                    <input type="number" class="form-control" id="bodyTemperature"
                                                        name="body_temp" step="0.1" required>
                                                </div>
                                                <div
                                                    class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-modal-attend">送信</button>
                                                </div>
                                            </form>
                                            {{-- form in modal --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 退勤ボタン -->
                            <button type="button" class="btn btn-leave" id="leaveButton">
                                退勤
                            </button>
                            {{-- <button type="button" class="btn btn-leave" data-bs-toggle="modal" data-bs-target="#leaveModal"
                                id="leaveButton">
                                退勤
                            </button>
 --}}


                            <!-- 退勤モーダル -->
                            <div class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-no-border">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="modal-title modal-title-centered" id="leaveModalLabel">
                                                お疲れ様でした!<br>本日の作業内容と感想を<br>入力してください
                                            </h5>
                                            {{-- form in modal --}}
                                            <form id="attendance-form" action="{{ route('attendances.checkout') }}"
                                                method="post">
                                                @csrf
                                                <input type="hidden" id="is_overtime" name="is_overtime" value="0">

                                                <div class="mb-3">
                                                    <label for="work_description" class="modal-label">作業内容</label>
                                                    <textarea class="form-control" id="work_description" name="work_description" required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="work_comment" class="modal-label">作業コメント</label>
                                                    <textarea class="form-control" id="work_comment" name="work_comment" required></textarea>
                                                </div>
                                                <div
                                                    class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-modal-attend">退勤する</button>
                                                </div>
                                            </form>
                                            {{-- form in modal --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 残業確認モーダル -->
                        <div class="modal fade" id="overtimeConfirmationModal" tabindex="-1"
                            aria-labelledby="overtimeConfirmationModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-no-border">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h5 class="modal-title modal-title-centered" id="overtimeConfirmationModalLabel">
                                            残業しましたか?
                                        </h5>
                                        <div class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                            <button type="button" class="btn btn-secondary" id="yesOvertime">はい</button>
                                            <button type="button" class="btn btn-secondary" id="noOvertime"
                                                data-bs-dismiss="modal">いいえ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col d-flex justify-content-between pt-4">



                            {{-- <button class="btn btn-break">休憩開始</button> --}}

                            <!-- 休憩開始ボタン -->
                            <button type="button" class="btn btn-break" data-bs-toggle="modal"
                                data-bs-target="#startRestModal">
                                休憩開始
                            </button>

                            <!-- 休憩開始モーダル -->
                            <div class="modal fade" id="startRestModal" tabindex="-1"
                                aria-labelledby="startRestModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-no-border">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="modal-title modal-title-centered mb-3" id="startRestModalLabel">
                                                休憩を開始します。<br>
                                            </h5>

                                            {{-- form in modal --}}
                                            <form id="attendance-form" action="{{ route('attendances.rest.start') }}"
                                                method="post">
                                                @csrf
                                                <div
                                                    class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-secondary">休憩開始</button>
                                                </div>
                                            </form>
                                            {{-- form in modal --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 休憩終了ボタン -->
                            <button type="button" class="btn btn-break" data-bs-toggle="modal"
                                data-bs-target="#endRestModal">
                                休憩終了
                            </button>

                            <!-- 休憩終了モーダル -->
                            <div class="modal fade" id="endRestModal" tabindex="-1" aria-labelledby="endRestModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-no-border">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="modal-title modal-title-centered mb-3" id="endRestModalLabel">
                                                休憩を終了します。<br>
                                            </h5>

                                            {{-- form in modal --}}
                                            <form id="attendance-form" action="{{ route('attendances.rest.end') }}"
                                                method="post">
                                                @csrf
                                                <div
                                                    class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-secondary">休憩終了</button>
                                                </div>
                                            </form>
                                            {{-- form in modal --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 残業開始ボタン -->
                            <button type="button" class="btn btn-overtime" data-bs-toggle="modal"
                                data-bs-target="#startOvertimeModal">
                                残業開始
                            </button>

                            <!-- 残業開始モーダル -->
                            <div class="modal fade" id="startOvertimeModal" tabindex="-1"
                                aria-labelledby="startOvertimeModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-no-border">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="modal-title modal-title-centered mb-3"
                                                id="startOvertimeModalLabel">
                                                残業を開始します。<br>
                                            </h5>

                                            {{-- form in modal --}}
                                            <form id="attendance-form" action="{{ route('attendances.overtime.start') }}"
                                                method="post">
                                                @csrf
                                                <div
                                                    class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-secondary">残業開始</button>
                                                </div>
                                            </form>
                                            {{-- form in modal --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 残業開始モーダル  ここまで-->


                            <!-- 残業終了ボタン -->
                            <button type="button" class="btn btn-overtime" data-bs-toggle="modal"
                                data-bs-target="#endOvertimeModal">
                                残業終了
                            </button>

                            <!-- 残業終了モーダル -->
                            <div class="modal fade" id="endOvertimeModal" tabindex="-1"
                                aria-labelledby="endOvertimeModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-no-border">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="modal-title modal-title-centered mb-3" id="endOvertimeModalLabel">
                                                残業を終了します。<br>
                                            </h5>

                                            {{-- form in modal --}}
                                            <form id="attendance-form" action="{{ route('attendances.overtime.end') }}"
                                                method="post">
                                                @csrf
                                                <div
                                                    class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-secondary">残業終了</button>
                                                </div>
                                            </form>
                                            {{-- form in modal --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 残業開始モーダル  ここまで-->

                        </div>
                    </div>
                </div>


                {{-- フラッシュメッセージがセッションにある場合はモーダルを表示 --}}
                @if (session('requested'))
                    <script>
                        window.onload = function() {
                            // ブートストラップのモーダルを使用している場合
                            const successModal = new bootstrap.Modal(document.getElementById('requested'));
                            successModal.show();
                        }
                    </script>
                @endif


                {{-- 共通の確認メッセージのモーダル --}}
                <div class="modal" tabindex="-1" id="requested">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header modal-header-no-border">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-title modal-title-centered">
                                {!! nl2br(e(session('requested'))) !!}
                            </div>
                            <div class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- テーブルのグループ -->

                <div class="record-list mt-5">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">種別</th>
                                <th scope="col">打刻日時</th>
                                <th scope="col">残業</th>
                                <th scope="col">体温</th>
                                <th scope="col">作業内容</th>
                                <th scope="col">作業コメント</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($attendancesArray)
                                @foreach ($attendancesArray as $attendance)
                                    <tr>
                                        <td>{{ $attendance['type'] }}</td>
                                        <td>{{ $attendance['dateTime'] }}</td>
                                        <td>{{ $attendance['is_overtime'] }}</td>
                                        <td>{{ $attendance['body_temp'] }}</td>
                                        <td>{{ $attendance['work_description'] }}</td>
                                        <td>{{ $attendance['work_comment'] }}</td>
                                        <td>
                                            @if ($attendance['edit_button'] == 1)
                                                {{-- <button type="submit" class="btn btn-edit"
                                                    onclick="location.href='{{ route('attendances.edit', $attendance['attendance_id']) }}'">編集</button> --}}
                                                <button type="submit" class="btn btn-edit" data-bs-toggle="modal"
                                                    data-bs-target="#editModal">編集</button>
                                            @else
                                                {{ $attendance['edit_button'] }}
                                        </td>
                                @endif
                                </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>


                <!--コメント編集用モーダル -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header modal-header-no-border">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h5 class="modal-title modal-title-centered mb-3" id="editModalLabel">
                                    作業内容&コメント修正<br>
                                </h5>

                                {{-- form in modal --}}
                                @foreach ($attendancesArray as $attendance)
                                    @if ($attendance['type'] == '退勤')
                                        <form id="edit-form"
                                            action="{{ route('attendances.update', $attendance['attendance_id']) }}"
                                            method="post">
                                            @csrf
                                            @method('PATCH')
                                            <div class="mb-3">
                                                <label for="work_description" class="modal-label">作業内容</label>
                                                <textarea class="form-control" id="work_description" name="work_description" required>{{ $attendance['work_description'] }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="work_comment" class="modal-label">作業コメント</label>
                                                <textarea class="form-control" id="work_comment" name="work_comment" required>{{ $attendance['work_comment'] }}</textarea>
                                            </div>
                                    @endif
                                @endforeach
                                <div class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                    <button type="submit" class="btn btn-secondary">保存</button>
                                </div>
                                </form>
                                {{-- form in modal --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- コメント編集用モーダル  ここまで-->


            </div>
        </div>

        <script>
            function updateClock() {
                const now = new Date();
                let hours = now.getHours();
                let minutes = now.getMinutes();
                let day = now.getDate();
                let month = now.getMonth() + 1; // 月は0から始まるため1を足す
                let year = now.getFullYear();
                let dayOfWeek = now.toLocaleString('ja-JP', {
                    weekday: 'short'
                });

                hours = hours < 10 ? '0' + hours : hours;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                month = month < 10 ? '0' + month : month;
                day = day < 10 ? '0' + day : day;

                const timeString = `${hours}:${minutes}`;
                const dateString = `${year}.${month}.${day} (${dayOfWeek})`;

                document.getElementById('digital-clock').textContent = timeString;
                document.querySelector('.date-display').textContent = dateString;
            }
            updateClock(); // 初期時刻を設定
            setInterval(updateClock, 1000); // 1秒ごとに時刻を更新

            // 残業確認ボタン用JS

            document.addEventListener('DOMContentLoaded', function() {
                const leaveButton = document.getElementById('leaveButton'); // 退勤ボタンのID
                const overtimeModal = new bootstrap.Modal(document.getElementById('overtimeConfirmationModal'));
                const leaveModal = new bootstrap.Modal(document.getElementById('leaveModal'));

                leaveButton.addEventListener('click', function() {
                    const now = new Date();
                    console.log(now)
                    if ((now.getHours() >= 15 && now.getMinutes() >= 30) || now.getHours() >= 16) {
                        // 現在時刻が15:30以降の場合、残業確認モーダルを表示
                        overtimeModal.show();
                        console.log('overtime log')
                    } else {
                        // それ以外の場合、通常の退勤モーダルを表示
                        leaveModal.show();
                    }
                });

                // 残業確認モーダルの「はい」ボタンがクリック→value=1、退勤モーダルを表示
                document.getElementById('yesOvertime').addEventListener('click', function() {
                    overtimeModal.hide();
                    // is_overtime フィールドの値を1に更新
                    document.getElementById('is_overtime').value = '1';
                    leaveModal.show();
                });

                // 残業確認モーダルの「いいえ」ボタンがクリック→退勤モーダルを表示のみ
                document.getElementById('noOvertime').addEventListener('click', function() {
                    overtimeModal.hide();
                    // is_overtime フィールドの値を1に更新
                    leaveModal.show();
                });
            });
        </script>
    @endsection
