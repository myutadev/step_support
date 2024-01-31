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
            @include('components.header')

            <!-- テーブルのグループ -->
            <div class="timecard-title">
                <h3>カウンセラー一覧</h3>
            </div>
            <button type="button" onclick="location.href='{{ route('admin.users.create') }}'"
                class="btn btn-create mt-3">新規カウンセラー登録</button>
            <div class="record-list mt-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">番号</th>
                            <th scope="col">氏名</th>
                            <th scope="col">電話番号</th>
                            <th scope="col">メールアドレス</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($counselors as $counselor)
                            <tr>
                                <td>{{ $counselor['id'] }}</td>
                                <td>{{ $counselor['name'] }}</td>
                                <td>{{ $counselor['contact_phone'] }}</td>
                                <td>{{ $counselor['contact_email'] }}</td>
                                <td>
                                    <button onclick="location.href='{{ route('admin.counselors.edit', $counselor['id']) }}'"
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
</script>
