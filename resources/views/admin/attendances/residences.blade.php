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
                <h3>住居情報一覧</h3>
            </div>
            <button type="button" onclick="location.href='{{ route('admin.residences.create') }}'"
                class="btn btn-create mt-3">新規住居情報登録</button>
            <div class="record-list mt-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">番号</th>
                            <th scope="col">名称</th>
                            <th scope="col">連絡先名</th>
                            <th scope="col">電話番号</th>
                            <th scope="col">メールアドレス</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($residences as $residence)
                            <tr>
                                <td>{{ $residence['id'] }}</td>
                                <td>{{ $residence['name'] }}</td>
                                <td>{{ $residence['contact_name'] }}</td>
                                <td>{{ $residence['contact_phone'] }}</td>
                                <td>{{ $residence['contact_email'] }}</td>
                                <td class="d-flex align-item-center gap-2">
                                    <form action="{{ route('admin.residences.edit', $residence['id']) }}" method="get">
                                        <input type="submit" value="編集" class="btn btn-edit">
                                    </form>
                                    <form action="{{ route('admin.residences.destroy', $residence['id']) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <input type="submit" value="削除"
                                            onclick="if(!confirm('削除しますか？')){return false};" class="btn btn-edit">
                                    </form>
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
