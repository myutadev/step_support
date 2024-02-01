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
                <h3>住居情報登録</h3>
            </div>

            <!-- フォームのエラーメッセージ -->
            @if ($errors->any())
                <div class="error">
                    <p>
                        <b>{{ count($errors) }}件のエラーがあります。</b>
                    </p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class='mt-5' action="{{ route('admin.residences.store') }}" method="post">
                @csrf

                <div class="row mb-3">
                    <label for="name" class="col-sm-2 col-form-label">名称</label>
                    <div class="col">
                        <input type="text" class="form-control" id='name' name='name' value={{ old('name') }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="contact_name" class="col-sm-2 col-form-label">連絡先名</label>
                    <div class="col">
                        <input type="text" class="form-control" id='contact_name' name='contact_name'
                            value={{ old('contact_name') }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="contact_phone" class="col-sm-2 col-form-label">電話番号</label>
                    <div class="col">
                        <input type="text" class="form-control" id='contact_phone' name='contact_phone'
                            value={{ old('contact_phone') }}>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="contact_email" class="col-sm-2 col-form-label">メールアドレス</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="contact_email" name='contact_email'
                            value={{ old('contact_email') }}>
                    </div>
                </div>

                <!-- 情報編集ボタン -->
                <button type="button" class="btn btn-store mt-3" id="update-button" data-bs-toggle="modal"
                    data-bs-target="#updateUserModal">
                    新規カウンセラー登録
                </button>

                <!-- 新規登録モーダル -->
                <div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header modal-header-no-border">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h5 class="modal-title modal-title-centered mb-5" id="modal-beneficiary-number">
                                    登録内容確認 </h5>
                                <div class="confirmation-area">
                                </div>

                                {{-- form in modal --}}
                                <div class="modal-footer modal-footer-no-border d-flex justify-content-center">
                                    <input type="submit" class="btn btn-store" value="登録">
                                </div>
                                {{-- form in modal --}}
                            </div>
                        </div>
                    </div>
                </div>

            </form>

            {{-- 共通の確認メッセージのモーダル --}}
            <div class="modal" tabindex="-1" id="requested">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header modal-header-no-border">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

        </div>
    </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('.dropdown-menu a').forEach(item => {
            item.addEventListener('click', (e) => {
                var text = e.target.text;
                var dropdownButton = e.target.closest('.dropdown').querySelector(
                    '.dropdown-toggle');
                dropdownButton.textContent = text;
            });
        });
    });

    // モーダルにフォームの内容を表示させるためのDOM操作
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelector("#update-button").addEventListener('click', function() {
            const name = document.getElementById('name').value;
            const contact_name = document.getElementById('contact_name').value;
            const contact_phone = document.getElementById('contact_phone').value;
            const contact_email = document.getElementById('contact_email').value;

            // モーダル内の対応する要素に値を設定
            document.querySelector('.confirmation-area').innerHTML =
                `
                <h4 class="mb-3">名称: ${name}</h4>
                <h4 class="mb-3">連絡先名: ${contact_name}</h4>
                <h4 class="mb-3">電話番号: ${contact_phone}</h4>
                <h4 class="mb-3">メールアドレス: ${contact_email}</h4>
            `
        });
    });
</script>
