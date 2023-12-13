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
                <h3>管理者情報編集</h3>
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

            <form class='mt-5' action="{{ route('admin.admins.update', $admin->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="row mb-3">
                    <label for="fullname" class="col-sm-2 col-form-label">名前</label>
                    <div class="col">
                        <input type="text" class="form-control" id='last_name' name='last_name' placeholder="姓"
                            value={{ old('last_name', $admin->last_name) }}>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id='first_name' name='first_name' placeholder="名"
                            value={{ old('first_name', $admin->first_name) }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="emp_number" class="col-sm-2 col-form-label">社員番号</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="emp_number" name="emp_number"
                            value={{ old('emp_number', $adminDetail->emp_number) }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">メールアドレス</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name='email'
                            value={{ old('email', $admin->email) }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">パスワード</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name='password'
                            value="{{ $admin->password }}">
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
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $role->id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
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
                        <input type="date" class="form-control" id="hire_date" name="hire_date"
                            value={{ old('hire_date', $adminDetail->hire_date) }}>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="hire_date" class="col-sm-2 col-form-label">退社日</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="termination_date" name="termination_date"
                            value={{ old('termination_date', $adminDetail->termination_date) }}>
                    </div>
                </div>



                <!--管理者情報編集ボタン -->
                <button type="button" class="btn btn-store" id="store-button" data-bs-toggle="modal"
                    data-bs-target="#editAdminModal">
                    管理者情報編集
                </button>

                <!-- 新規登録モーダル -->
                <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel"
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
            const role_select = document.getElementById('role_id');
            const role_name = role_select.options[
                role_select.selectedIndex].text;

            const hire_date = document.getElementById('hire_date').value;
            const termination_date = document.getElementById('termination_date').value;
            const name = lastName + ' ' + firstName;


            // モーダル内の対応する要素に値を設定
            document.querySelector('.confirmation-area').innerHTML =
                `
                <h4 class="mb-3">名前: ${name}</h4>
                <h4 class="mb-3">社員番号: ${emp_number}</h4>
                <h4 class="mb-3">メールアドレス: ${email}</h4>
                <h4 class="mb-3">役割: ${role_name}</h4>
                <h4 class="mb-3">入社日: ${hire_date}</h4>
                <h4 class="mb-3">退社日: ${termination_date}</h4>


            `
        });
    });
</script>
