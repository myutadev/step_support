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
                            <button class="btn btn-attend me-3">出勤</button>
                            <button class="btn btn-leave">退勤</button>
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
