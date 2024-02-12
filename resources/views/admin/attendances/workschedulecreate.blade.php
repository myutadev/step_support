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
                <h3>勤怠日区分登録</h3>
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

            <form class='mt-5' action="{{ route('admin.workschedules.store') }}" method="post">
                @csrf

                <div class="row mb-3">
                    <label for="target_date" class="col-sm-2 col-form-label">変更対象日</label>
                    <div class="col">
                        <input type="hidden" name="workSchedule_id" value="{{ $targetWorkSchedule['id'] }}">
                        <p id="target_date">{{ $targetWorkSchedule['date'] . '(' . $targetWorkSchedule['day'] . ')' }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="row mb-3">
                        <label for="schedule_type_id" class="col-sm-2 col-form-label">開所日区分</label>
                        <div class="col-sm-10">
                            {{-- ドロップダウンここから --}}

                            <div class="dropdown">
                                <select class="btn btn-light" name="schedule_type_id" id="schedule_type_id">
                                    @foreach ($scheduleTypes as $scheduleType)
                                        <option value="{{ $scheduleType->id }}"
                                            {{ old('schedule_type_id') == $scheduleType->id ? 'selected' : '' }}>
                                            {{ $scheduleType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ドロップダウンここまで --}}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="description" class="col-sm-2 col-form-label">備考</label>
                    <div class="col">
                        <input type="text" class="form-control" id='description' name='description'
                            value={{ old('description') }}>
                    </div>
                </div>

                <!-- 情報編集ボタン -->
                <button type="button" class="btn btn-store mt-3" id="update-button" data-bs-toggle="modal"
                    data-bs-target="#updateUserModal">
                    勤怠日区分変更登録
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
            const target_date = document.getElementById('target_date').innerHTML;
            const schedule_type_select = document.getElementById('schedule_type_id');
            const schedule_type_name = schedule_type_select.options[schedule_type_select.selectedIndex]
                .text;
            const description = document.getElementById('description').value;
            // モーダル内の対応する要素に値を設定
            document.querySelector('.confirmation-area').innerHTML =
                `
                <h4 class="mb-3">編集対象日: ${target_date}</h4>
                <h4 class="mb-3">開所日区分: ${schedule_type_name}</h4>
                <h4 class="mb-3">備考: ${description}</h4>
            `
        });
    });
</script>
