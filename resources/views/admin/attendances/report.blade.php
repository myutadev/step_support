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
            <div class="container-fluid mt-3">
                <div class="timecard-title">
                    <h3>月次勤務レポート</h3>
                </div>
                <div class="timecard-selectors">
                    <form action="{{ '/admin/report/' }}" method="post" id="monthForm" data-base-url="/admin/report/"
                        data-sort-field="{{ $sortField }}" data-sort-order="{{ $sortOrder }}">
                        @csrf
                        <input type="month" name="month" value="{{ $year }}-{{ $month }}"
                            id="monthInput">
                    </form>
                </div>
                <div class="mt-5">
                    <h3>事業所全体</h3>
                </div>
                <div class="row">
                    <div class="col-2 mt-4 d-flex flex-column align-items-start">
                        <div class="text-center ">
                            <h5 class="mb-3">当月合計開所日</h5>
                            <h5>{{ $totalOpeningThisMonth }}</h5>
                        </div>
                    </div>
                    <div class="col-2 mt-4 d-flex flex-column align-items-start">
                        <div class="text-center ">
                            <h5 class="mb-3">本日までの開所日</h5>
                            <h5>{{ $openingSoFarThisMonth }}</h5>
                        </div>
                    </div>
                    <div class="col-2 mt-4 d-flex flex-column align-items-start">
                        <div class="text-center ">
                            <h5 class="mb-3">当月残り開所日</h5>
                            <h5>{{ $totalOpeningThisMonth - $openingSoFarThisMonth }}</h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mt-4 d-flex flex-column align-items-start">
                        <div class="text-center ">
                            <h5 class="mb-3">請求人数累計</h5>
                            <h5>{{ $totalClaimsCount }}</h5>
                        </div>
                    </div>
                    <div class="col-md-2 mt-4 d-flex flex-column align-items-start">
                        <div class="text-center ">
                            <h5 class="mb-3">実勤務時間計</h5>
                            <h5>{{ $companyTotalWorkDuration }}</h5>
                        </div>
                    </div>
                    <div class="col-md-2 mt-4 d-flex flex-column align-items-start">
                        <div class="text-center ">
                            <h5 class="mb-3">目標勤務時間計</h5>
                            <h5>{{ $targetTotalWorkDuration }}</h5>
                        </div>
                    </div>
                    <div class="col-md-2 mt-4 d-flex flex-column align-items-start">
                        <div class="text-center">
                            <h5 class="mb-3">月次勤務時間上限</h5>
                            <h5>{{ $maxTotalWorkDuration }}</h5>
                        </div>
                    </div>
                    <div class="col-2 mt-4 d-flex flex-column align-items-start">
                        <div class="text-center">
                            <h5 class="mb-3">目標までの不足</h5>
                            <h5 class="{{ substr($restToAchieveCompanyTarget, 0, 1) == '-' ? 'text-danger' : '' }}">
                                {{ $restToAchieveCompanyTarget }}</h5>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <h3>利用者別出退勤レポート</h3>
                </div>
                <div class="record-list mt-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <div>
                                        <a class="no-underline d-flex align-items-center"
                                            href="{{ route('admin.report', [
                                                'yearmonth' => $year . '-' . $month,
                                                'sortField' => 'beneficiary_number',
                                                'sortOrder' => $sortField == 'beneficiary_number' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                            ]) }}">受給者番号
                                            <div class="d-flex flex-column ms-2">
                                                <i class="bi bi-chevron-up negative-mb"
                                                    style="color:{{ $sortField == 'beneficiary_number' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                                <i class="bi bi-chevron-down"
                                                    style="color:{{ $sortField == 'beneficiary_number' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                            </div>
                                        </a>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div>
                                        <a class="no-underline d-flex align-items-center"
                                            href="{{ route('admin.report', [
                                                'yearmonth' => $year . '-' . $month,
                                                'sortField' => 'name',
                                                'sortOrder' => $sortField == 'name' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                            ]) }}">利用者名
                                            <div class="d-flex flex-column ms-2">
                                                <i class="bi bi-chevron-up negative-mb"
                                                    style="color:{{ $sortField == 'name' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                                <i class="bi bi-chevron-down"
                                                    style="color:{{ $sortField == 'name' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                            </div>
                                        </a>
                                    </div>
                                <th scope="col">
                                    <div>
                                        <a class="no-underline d-flex align-items-center"
                                            href="{{ route('admin.report', [
                                                'yearmonth' => $year . '-' . $month,
                                                'sortField' => 'is_on_welfare',
                                                'sortOrder' => $sortField == 'is_on_welfare' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                            ]) }}">生活保護
                                            <div class="d-flex flex-column ms-2">
                                                <i class="bi bi-chevron-up negative-mb"
                                                    style="color:{{ $sortField == 'is_on_welfare' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                                <i class="bi bi-chevron-down"
                                                    style="color:{{ $sortField == 'is_on_welfare' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                            </div>
                                        </a>
                                    </div>
                                </th>
                                {{-- <th scope="col">
                                    <div>
                                        <p class="no-margin mb-1">
                                            開所日累計/合計
                                        </p>
                                    </div>
                                </th> --}}
                                <th scope="col">
                                    <div>
                                        <a class="no-underline d-flex align-items-center"
                                            href="{{ route('admin.report', [
                                                'yearmonth' => $year . '-' . $month,
                                                'sortField' => 'daysPresentSoFarThisMonth',
                                                'sortOrder' => $sortField == 'daysPresentSoFarThisMonth' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                            ]) }}">出勤日累計
                                            <div class="d-flex flex-column ms-2">
                                                <i class="bi bi-chevron-up negative-mb"
                                                    style="color:{{ $sortField == 'daysPresentSoFarThisMonth' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                                <i class="bi bi-chevron-down"
                                                    style="color:{{ $sortField == 'daysPresentSoFarThisMonth' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                            </div>
                                        </a>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div>
                                        <a class="no-underline d-flex align-items-center"
                                            href="{{ route('admin.report', [
                                                'yearmonth' => $year . '-' . $month,
                                                'sortField' => 'attendanceRate',
                                                'sortOrder' => $sortField == 'attendanceRate' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                            ]) }}">出勤率
                                            <div class="d-flex flex-column ms-2">
                                                <i class="bi bi-chevron-up negative-mb"
                                                    style="color:{{ $sortField == 'attendanceRate' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                                <i class="bi bi-chevron-down"
                                                    style="color:{{ $sortField == 'attendanceRate' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                            </div>
                                        </a>
                                    </div>

                                </th>
                                <th scope="col">
                                    <div>
                                        <a class="no-underline d-flex align-items-center"
                                            href="{{ route('admin.report', [
                                                'yearmonth' => $year . '-' . $month,
                                                'sortField' => 'workedHourTotalSoFarThisMonth',
                                                'sortOrder' => $sortField == 'workedHourTotalSoFarThisMonth' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                            ]) }}">実労働時間累計
                                            <div class="d-flex flex-column ms-2">
                                                <i class="bi bi-chevron-up negative-mb"
                                                    style="color:{{ $sortField == 'workedHourTotalSoFarThisMonth' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                                <i class="bi bi-chevron-down"
                                                    style="color:{{ $sortField == 'workedHourTotalSoFarThisMonth' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                            </div>
                                        </a>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div>
                                        <a class="no-underline d-flex align-items-center"
                                            href="{{ route('admin.report', [
                                                'yearmonth' => $year . '-' . $month,
                                                'sortField' => 'restToAchieveTarget',
                                                'sortOrder' => $sortField == 'restToAchieveTarget' && $sortOrder == 'asc' ? 'desc' : 'asc',
                                            ]) }}">目標までの不足
                                            <div class="d-flex flex-column ms-2">
                                                <i class="bi bi-chevron-up negative-mb"
                                                    style="color:{{ $sortField == 'restToAchieveTarget' && $sortOrder == 'asc' ? 'black' : 'gray' }}"></i>
                                                <i class="bi bi-chevron-down"
                                                    style="color:{{ $sortField == 'restToAchieveTarget' && $sortOrder == 'desc' ? 'black' : 'gray' }}"></i>
                                            </div>
                                        </a>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sorteUserInfoArray as $userInfo)
                                <tr>
                                    <td>{{ $userInfo['beneficiary_number'] }}</td>
                                    <td> {{ $userInfo['name'] }}</td>
                                    <td>{{ $userInfo['is_on_welfare'] ? '有' : '無' }}</td>
                                    {{-- <td>{{ $userInfo['openingSoFarThisMonth'] }}/{{ $userInfo['totalOpeningThisMonth'] }}
                                    </td> --}}
                                    <td>{{ $userInfo['daysPresentSoFarThisMonth'] }}</td>
                                    <td>{{ $userInfo['attendanceRate'] }}%</td>
                                    <td>{{ $userInfo['workedHourTotalSoFarThisMonth'] }}</td>
                                    <td
                                        class="{{ substr($userInfo['restToAchieveTarget'], 0, 1) == '-' ? 'text-danger' : '' }}">
                                        {{ $userInfo['restToAchieveTarget'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script src="{{ asset('js/calendar/monthChangeHandlerWithSort.js') }}"></script>
@endsection
