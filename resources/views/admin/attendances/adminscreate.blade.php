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
                <h3>新規管理者追加</h3>
            </div>


            <form class='mt-5' action="{{ route('admin.admins.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <label for="fullname" class="col-sm-2 col-form-label">名前</label>
                    <div class="col">
                        <input type="text" class="form-control" id='last_name' name='last_name' placeholder="姓">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id='first_name' name='first_name' placeholder="名">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="emp_number" class="col-sm-2 col-form-label">社員番号</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="emp_number" name="emp_number">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">メールアドレス</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name='email'>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">パスワード</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name='password'>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="row">
                        <label for="disability_category_id" class="col-sm-2 col-form-label">役割</label>
                        <div class="col-sm-10">
                            {{-- ドロップダウンここから --}}
                            <div class="dropdown">
                                <select class="btn btn-light" name="role_id" id="role_id">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- ドロップダウンここまで --}}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="hire_date" class="col-sm-2 col-form-label">入社日</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="hire_date" name="hire_date">
                    </div>
                </div>


                <!-- 新規登録ボタン -->
                <button type="button" class="btn btn-store" id="store-button" data-bs-toggle="modal"
                    data-bs-target="#storeUserModal">
                    新規登録
                </button>

                <!-- 新規登録モーダル -->
                <div class="modal fade" id="storeUserModal" tabindex="-1" aria-labelledby="storeUserModalLabel"
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
        document.querySelector("#store-button").addEventListener('click', function() {
            const lastName = document.getElementById('last_name').value;
            const firstName = document.getElementById('first_name').value;
            const emp_number = document.getElementById('emp_number').value;
            const email = document.getElementById('email').value;
            // const is_on_welfare = document.getElementById('is_on_welfare').value;
            const role_select = document.getElementById('role_id');
            const role_name = role_select.options[
                role_select.selectedIndex].text;

            const hire_date = document.getElementById('hire_date').value;

            const name = lastName + ' ' + firstName;


            // モーダル内の対応する要素に値を設定
            document.querySelector('.confirmation-area').innerHTML =
                `
                <h4 class="mb-3">名前: ${name}</h4>
                <h4 class="mb-3">社員番号: ${emp_number}</h4>
                <h4 class="mb-3">メールアドレス: ${email}</h4>
                <h4 class="mb-3">役割: ${role_name}</h4>
                <h4 class="mb-3">入社日: ${hire_date}</h4>


            `
        });
    });
</script>
