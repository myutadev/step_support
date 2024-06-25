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
                <h3>利用者アカウント管理</h3>
            </div>
            <button type="button" onclick="location.href='{{ route('admin.users.create') }}'"
                class="btn btn-create mt-3">新規利用者登録</button>
            <div class="record-list mt-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">受給者番号</th>
                            <th scope="col">利用者名</th>
                            <th scope="col">生年月日</th>
                            <th scope="col">メールアドレス</th>
                            <th scope="col">障害区分</th>
                            <th scope="col">生活保護受給</th>
                            <th scope="col">住居</th>
                            <th scope="col">相談員</th>
                            <th scope="col">入所日</th>
                            <th scope="col">退所日</th>
                            <th scope="col">編集</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userInfoArray as $userInfo)
                            <tr>
                                <td>{{ $userInfo['beneficiary_number'] }}</td>
                                <td>{{ $userInfo['name'] }}</td>
                                <td>{{ $userInfo['birthdate'] }}</td>
                                <td>{{ $userInfo['email'] }}</td>
                                <td>{{ $userInfo['disability_category_id'] }}</td>
                                <td>{{ $userInfo['is_on_welfare'] }}</td>
                                <td>{{ $userInfo['residence_id'] }}</td>
                                <td>{{ $userInfo['counselor_id'] }}</td>
                                <td>{{ $userInfo['admission_date'] }}</td>
                                <td>{{ $userInfo['discharge_date'] }}</td>
                                <td>
                                    <button onclick="location.href='{{ route('admin.users.edit', $userInfo['user_id']) }}'"
                                        class="btn btn-edit">
                                        編集
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    </div>

    <script>
        // ここにJavaScriptコードを配置
        document.getElementById('monthInput').addEventListener('change', function() {
            document.getElementById('monthForm').submit();
        });
    </script>
@endsection
