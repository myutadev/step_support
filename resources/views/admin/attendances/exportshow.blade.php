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
            <div class="timecard-title mb-5">
                <h3>月次勤務データ出力</h3>
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

            <div class="timecard-selectors">
                <form action="{{ route('admin.export') }}" method="post">
                    @csrf
                    <input type="month" name="yearmonth"
                        value="{{ $selectedYearMonth['year'] }}-{{ $selectedYearMonth['month'] }}" id="monthInput">
                    <br>
                    <button type="submit" class="btn btn-store mt-3">
                        勤務データ出力
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
