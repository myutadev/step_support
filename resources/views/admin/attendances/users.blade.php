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
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'beneficiary_number',
                                        'sortOrder' => $sortField == 'beneficiary_number' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column">
                                            <span>受給者</span>
                                            <span>番号</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'beneficiary_number' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'beneficiary_number' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'beneficiary_number_expiration',
                                        'sortOrder' => $sortField == 'beneficiary_number_expiration' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>受給者番号</span>
                                            <span>有効期限</span>
                                        </div>
                                        <div class="d-flex flex-column align-items-center gap-1 ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'beneficiary_number_expiration' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'beneficiary_number_expiration' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'name',
                                        'sortOrder' => $sortField == 'name' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>利用者名</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'name' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'name' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'birthdate',
                                        'sortOrder' => $sortField == 'birthdate' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>生年月日</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'birthdate' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'birthdate' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'email',
                                        'sortOrder' => $sortField == 'email' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>メールアドレス</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'email' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'email' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'disability_category_id',
                                        'sortOrder' => $sortField == 'disability_category_id' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>障害</span>
                                            <span>区分</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'disability_category_id' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'disability_category_id' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'is_on_welfare',
                                        'sortOrder' => $sortField == 'is_on_welfare' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>生保</span>
                                            <span>需給</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'is_on_welfare' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'is_on_welfare' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'residence_id',
                                        'sortOrder' => $sortField == 'residence_id' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>住居</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'residence_id' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'residence_id' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'counselor_id',
                                        'sortOrder' => $sortField == 'counselor_id' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>相談員</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'counselor_id' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'counselor_id' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'admission_date',
                                        'sortOrder' => $sortField == 'admission_date' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>入所日</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'admission_date' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'admission_date' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                <div>
                                    <a href="{{ route('admin.users', [
                                        'sortField' => 'discharge_date',
                                        'sortOrder' => $sortField == 'discharge_date' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="no-underline d-flex align-items-center">
                                        <div class="d-flex flex-column text-nowrap">
                                            <span>退所日</span>
                                        </div>
                                        <div class="d-flex flex-column ms-2">
                                            <i class="bi bi-chevron-up negative-mb"
                                                style="color:{{ $sortField == 'discharge_date' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                            <i class="bi bi-chevron-down negative-mb"
                                                style="color:{{ $sortField == 'discharge_date' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                        </div>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="align-middle">
                                編集</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userInfoArray as $userInfo)
                            <tr>
                                <td>{{ $userInfo['beneficiary_number'] }}</td>
                                <td>{{ $userInfo['beneficiary_number_expiration'] }}</td>
                                <td class="text-nowrap">{{ $userInfo['name'] }}</td>
                                <td>{{ $userInfo['birthdate'] }}</td>
                                <td>{{ $userInfo['email'] }}</td>
                                <td>{{ $userInfo['disability_category_id'] }}</td>
                                <td>{{ $userInfo['is_on_welfare'] }}</td>
                                <td>{{ $userInfo['residence_id'] }}</td>
                                <td class="text-nowrap">{{ $userInfo['counselor_id'] }}</td>
                                <td class="text-nowrap">{{ $userInfo['admission_date'] }}</td>
                                <td class="text-nowrap">{{ $userInfo['discharge_date'] }}</td>
                                <td>
                                    <button
                                        onclick="location.href='{{ route('admin.users.edit', $userInfo['user_id']) }}'"
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
