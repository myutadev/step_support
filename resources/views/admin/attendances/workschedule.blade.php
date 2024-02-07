@extends('layouts.main')
@section('content')
    <div class="container-fluid">

        <div class="row">
            @include('components.admin-side-menu')
        </div>

        <!-- メインコンテンツのカラム -->
        <div class="col-md-10">
            {{-- ヘッダー --}}
            @include('components.header')


            <!-- テーブルのグループ -->
            <div class="timecard-title">
                <h3>開所日編集</h3>
            </div>

            <div class="timecard-selectors">
                <div class="row">
                    <!-- 月選択 -->
                    <form action=""></form>
                    <div class="col-sm-2 mt-3">
                        <form action="{{ '/admin/settings/workschedules/show/' }}" method="post" id="monthForm"
                            data-base-url="/admin/settings/workschedules/show/">
                            @csrf
                            <input type="month" name="month" value="{{ $year }}-{{ $month }}"
                                id="monthInput" class="form-control mb-2 mr-sm-2">
                        </form>
                    </div>
                </div>
            </div>
            <div class="record-list mt-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">日付</th>
                            <th scope="col">曜日</th>
                            <th scope="col">開所日区分</th>
                            <th scope="col">備考</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($monthlyWorkScheduleData)
                            @foreach ($monthlyWorkScheduleData as $date)
                                <tr>
                                    <td>{{ $date['date'] }}</td>
                                    <td>{{ $date['day'] }}</td>
                                    <td>{{ $date['scheduleType'] }}</td>
                                    <td>{{ $date['description'] }}</td>
                                    <td>
                                        <button
                                            onclick="location.href='{{ route('admin.workschedules.create', $date['id']) }}'"
                                            class="btn btn-edit" {{ $date['description'] !== '' ? 'disabled' : '' }}>
                                            区分変更
                                        </button>
                                    <td>
                                        <form action="{{ route('admin.workschedules.destroy', $date['special_sched_id']) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <input type="submit" value="削除"
                                                onclick="if(!confirm('削除しますか？')){return false};" class="btn btn-edit"
                                                {{ $date['description'] !== '' ? '' : 'disabled' }}>
                                        </form>
                                    </td>

                                    </td>

                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/calendar/monthChangeHandler.js') }}"></script>


@endsection
