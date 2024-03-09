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
                <h3>管理者アカウント管理</h3>
            </div>
            <button type="button" onclick="location.href='{{ route('admin.admins.create') }}'"
                class="btn btn-create mt-3">新規管理者登録</button>
            <div class="record-list mt-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">社員番号</th>
                            <th scope="col">名前</th>
                            <th scope="col">メールアドレス</th>
                            <th scope="col">役割</th>
                            <th scope="col">入社日</th>
                            <th scope="col">退社日</th>
                            <th scope="col">編集</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($adminInfoArray as $adminInfo)
                            <tr>
                                <td>{{ $adminInfo['emp_number'] }}</td>
                                <td>{{ $adminInfo['name'] }}</td>
                                <td>{{ $adminInfo['email'] }}</td>
                                <td>{{ $adminInfo['role'] }}</td>
                                <td>{{ $adminInfo['hire_date'] }}</td>
                                <td>{{ $adminInfo['termination_date'] }}</td>
                                <td>
                                    <button
                                        onclick="location.href='{{ route('admin.admins.edit', $adminInfo['admin_id']) }}'"
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
@endsection

<script>
    // ここにJavaScriptコードを配置
    document.getElementById('monthInput').addEventListener('change', function() {
        document.getElementById('monthForm').submit();
    });
</script>
