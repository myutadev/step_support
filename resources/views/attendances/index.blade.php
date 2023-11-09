@extends('layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="dashboard-header">
            <!-- ヘッダーコンテンツ -->
            @include('layouts.navigation');
        </div>

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
                    <div class="menu-text-light ms-3 my-4 d-flex align-items-center">
                        <i class="bi bi-alarm"></i>
                        <p class="mb-0 ms-2">打刻</p>
                    </div>
                    <div class="menu-text-light ms-3 my-4 d-flex align-items-center">
                        <i class="bi bi-card-checklist"></i>
                        <p class="mb-0 ms-2">タイムカード</p>
                    </div>
                </div>
            </div>

            <!-- メインコンテンツのカラム -->
            <div class="col-md-9 pt-5">
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

                            {{-- フラッシュメッセージがセッションにある場合はモーダルを表示 --}}
                            @if (session('checkedIn'))
                                <script>
                                    window.onload = function() {
                                        // ブートストラップのモーダルを使用している場合
                                        const successModal = new bootstrap.Modal(document.getElementById('checkedIn'));
                                        successModal.show();
                                    }
                                </script>
                            @endif


                            {{-- 出勤用確認メッセージのモーダル --}}
                            <div class="modal" tabindex="-1" id="checkedIn">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-no-border">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-title modal-title-centered">
                                            {!! nl2br(e(session('checkedIn'))) !!}
                                        </div>
                                        <div class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">閉じる</button>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <!-- 退勤ボタン -->
                            <button type="button" class="btn btn-leave" data-bs-toggle="modal"
                                data-bs-target="#leaveModal">
                                退勤
                            </button>



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


                            {{-- フラッシュメッセージがセッションにある場合はモーダルを表示 --}}
                            @if (session('checkedOut'))
                                <script>
                                    window.onload = function() {
                                        // ブートストラップのモーダルを使用している場合
                                        const successModal = new bootstrap.Modal(document.getElementById('checkedOut'));
                                        successModal.show();
                                    }
                                </script>
                            @endif


                            {{-- 退勤用確認メッセージのモーダル --}}
                            <div class="modal" tabindex="-1" id="checkedOut">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header modal-header-no-border">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-title modal-title-centered">
                                            {!! nl2br(e(session('checkedOut'))) !!}
                                            </p>
                                        </div>
                                        <div class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">閉じる</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="col d-flex justify-content-between pt-4">
                            <button class="btn btn-break">休憩開始</button>
                            <button class="btn btn-break">休憩終了</button>
                            <button class="btn btn-overtime">残業開始</button>
                            <button class="btn btn-overtime">残業終了</button>
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
                                <th scope="col">体温</th>
                                <th scope="col">作業内容</th>
                                <th scope="col">作業コメント</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="attend-record">出勤</td>
                                <td>11/3(金) 08:55</td>
                                <td>36.2</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>退勤</td>
                                <td>11/3(金) 15:05</td>
                                <td></td>
                                <td>インスタグラム投稿作成、いいね周り</td>
                                <td>今日はインスタの作業の工程を教わりました。でも自分の中でまだ何をやったらいいのかわかっていないので混乱しています。明日は定期通院でお休みするので、その間に忘れてしまいそうで心配です。
                                </td>
                                <td><button class="edit-btn">編集</button></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
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
        </script>
    @endsection
