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
            <div class="col-md-9 pt-5">

                <!-- テーブルのグループ -->
                <div class="timecard-title">
                    <p>タイムカード</p>
                </div>
                <div class="timecard-selectors">
                    <form action="{{ '/attendances/timecard/submit-month' }}" method="post" id="monthForm">
                        @csrf
                        <input type="month" name="month" value="{{ date('Y-m') }}" id="monthInput">
                    </form>
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
                                <th scope="col">休憩</th>
                                <th scope="col">残業</th>
                                <th scope="col">作業内容</th>
                                <th scope="col">作業コメント</th>
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
                                        <td> {!! $date['rest'] !!} </td>
                                        <td>{{ $date['overtime'] }}</td>
                                        <td>{{ $date['workDescription'] }}</td>
                                        <td>{{ $date['workComment'] }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                </div>
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

<script>
    // ここにJavaScriptコードを配置
    document.getElementById('monthInput').addEventListener('change', function() {
        document.getElementById('monthForm').submit();
    });
</script>
