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
                <h3>利用者情報編集追加</h3>
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

            <form class='mt-5' action="{{ route('admin.users.update', $user->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="row mb-3">
                    <label for="beneficiary_number" class="col-sm-2 col-form-label">受給者番号</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="beneficiary_number" name="beneficiary_number"
                            value={{ $userDetail->beneficiary_number }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="fullname" class="col-sm-2 col-form-label">名前</label>
                    <div class="col">
                        <input type="text" class="form-control" id='last_name' name='last_name' placeholder="姓"
                            value={{ old('last_name', $user->last_name) }}>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id='first_name' name='first_name' placeholder="名"
                            value={{ old('first_name', $user->first_name) }}>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="birthdate" class="col-sm-2 col-form-label">生年月日</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="birthdate" name="birthdate"
                            value={{ old('birthdate', $userDetail->birthdate) }}>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">メールアドレス</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name='email'
                            value={{ old('email', $user->email) }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">パスワード</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name='password'
                            value='{{ $user->password }}'>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="row mb-3">
                        <label for="disability_category_id" class="col-sm-2 col-form-label">障害区分</label>
                        <div class="col-sm-10">
                            {{-- ドロップダウンここから --}}
                            <div class="dropdown">
                                <select class="btn btn-light" name="disability_category_id" id="disability_category_id">
                                    @foreach ($disability_categories as $disability_category)
                                        <option value="{{ $disability_category->id }}"
                                            {{ old('disability_category_id', $userDetail->disability_category_id) == $disability_category->id ? 'selected' : '' }}>
                                            {{ $disability_category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- ドロップダウンここまで --}}
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="row mb-3">
                        <label for="is_on_welfare" class="col-sm-2 col-form-label">生活保護受給</label>
                        <div class="col-sm-10">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_on_welfare" value="1"
                                    id="is_on_welfare"
                                    {{ old('is_on_welfare', $userDetail->is_on_welfare) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_on_welfare">
                                    生活保護を受給している場合はチェック </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="row mb-3">
                        <label for="residence_id" class="col-sm-2 col-form-label">住居</label>
                        <div class="col-sm-10">
                            {{-- ドロップダウンここから --}}

                            <div class="dropdown">
                                <select class="btn btn-light" name="residence_id" id="residence_id">
                                    @foreach ($residences as $residence)
                                        <option value="{{ $residence->id }}"
                                            {{ old('residence_id', $userDetail->residence_id) == $residence->id ? 'selected' : '' }}>
                                            {{ $residence->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ドロップダウンここまで --}}
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="row mb-3">
                        <label for="counselor_id" class="col-sm-2 col-form-label">相談員</label>
                        <div class="col-sm-10">
                            {{-- ドロップダウンここから --}}
                            <div class="dropdown">
                                <select class="btn btn-light" name="counselor_id" id="counselor_id">
                                    @foreach ($counselors as $counselor)
                                        <option value="{{ $counselor->id }}"
                                            {{ old('counselor_id', $userDetail->counselor_id) == $counselor->id ? 'selected' : '' }}>
                                            {{ $counselor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- ドロップダウンここまで --}}
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="admission_date" class="col-sm-2 col-form-label">入所日</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="admission_date" name="admission_date"
                            value={{ old('admission_date', $userDetail->admission_date) }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="discharge_date" class="col-sm-2 col-form-label">退所日</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="discharge_date" name="discharge_date"
                            value={{ old('discharge_date', $userDetail->discharge_date) }}>
                    </div>
                </div>

                <!-- 情報編集ボタン -->
                <button type="button" class="btn btn-store mt-3" id="update-button" data-bs-toggle="modal"
                    data-bs-target="#updateUserModal">
                    編集内容を保存
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
        document.querySelector("#update-button").addEventListener('click', function() {
            const beneficiaryNumber = document.getElementById('beneficiary_number').value;
            const lastName = document.getElementById('last_name').value;
            const firstName = document.getElementById('first_name').value;
            const email = document.getElementById('email').value;
            // const is_on_welfare = document.getElementById('is_on_welfare').value;
            const disability_category_select = document.getElementById('disability_category_id');
            const disability_category_name = disability_category_select.options[
                disability_category_select.selectedIndex].text;

            const residence_select = document.getElementById('residence_id');
            const residence_name = residence_select.options[residence_select.selectedIndex].text;

            const counselor_select = document.getElementById('counselor_id');
            const counselor_name = counselor_select.options[counselor_select.selectedIndex].text;

            const admission_date = document.getElementById('admission_date').value;
            const discharge_date = document.getElementById('discharge_date').value;


            const isOnWelfareCheckbox = document.getElementById('is_on_welfare');
            const isOnWelfareChecked = isOnWelfareCheckbox.checked;
            const is_on_welfare = isOnWelfareChecked ? '有' : '無'

            console.log('生活保護を受給しているか: ', isOnWelfareChecked ? 'はい' : 'いいえ');


            // const counselor_id = document.getElementById('counselor_id').value;
            const name = lastName + ' ' + firstName;


            // モーダル内の対応する要素に値を設定
            document.querySelector('.confirmation-area').innerHTML =
                `
                <h4 class="mb-3">受給者番号: ${beneficiaryNumber}</h4>
                <h4 class="mb-3">名前: ${name}</h4>
                <h4 class="mb-3">メールアドレス: ${email}</h4>
                <h4 class="mb-3">障害区分: ${disability_category_name}</h4>
                <h4 class="mb-3">生活保護受給: ${is_on_welfare}</h4>
                <h4 class="mb-3">住居: ${residence_name}</h4>
                <h4 class="mb-3">相談員: ${counselor_name}</h4>
                <h4 class="mb-3">入所日: ${admission_date}</h4>
                <h4 class="mb-3">退所日: ${discharge_date}</h4>
            `
            // <h4>生活保護受給:${is_on_welfare}</h4>


            // document.getElementById('modal-full-name').textContent = '名前:' + name;
            // document.getElementById('modal-email').textContent = 'メールアドレス:' + email;
            // 他の値も同様に設定
        });
    });
</script>
