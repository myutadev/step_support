            <!-- サイドバーのカラム -->
            <div class="col-md-2 side-bar min-vh-100">
                <div class="d-flex flex-column align-items-start">
                    <div class="app-title my-4 ms-3">
                        <h3>Step Support</h3>
                    </div>
                    <div class="menu-text-dark ms-3">
                        <p>レポート</p>
                    </div>
                    <a href="{{ '/admin/timecard' }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex align-items-center">
                            <i class="bi bi-card-checklist"></i>
                            <p class="mb-0 ms-2">タイムカード</p>
                        </div>
                    </a>
                    <a href="{{ '/admin/daily' }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-calendar2-check"></i>
                            <p class="mb-0 ms-2">日別出勤状況</p>
                        </div>
                    </a>
                    <a href="{{ '/admin/export/show' }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-book"></i>
                            <p class="mb-0 ms-2">月次勤務データ出力</p>
                        </div>
                    </a>
                    <a href="{{ '/admin/report' }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-book"></i>
                            <p class="mb-0 ms-2">月次勤務レポート</p>
                        </div>
                    </a>
                    {{-- <a href="{{ '' }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-book"></i>
                            <p class="mb-0 ms-2">活動記録表出力</p>
                        </div>
                    </a>
                    <a href="{{ '' }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-layout-text-window-reverse"></i>
                            <p class="mb-0 ms-2">実績記録表出力</p>
                        </div>
                    </a> --}}

                    <div class="menu-text-dark ms-3 mt-4">
                        <p>設定</p>
                    </div>
                    <a href="{{ route('admin.users') }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex align-items-center">
                            <i class="bi bi-person-plus"></i>
                            <p class="mb-0 ms-2">利用者アカウント管理</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.admins') }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-person-plus-fill"></i>
                            <p class="mb-0 ms-2">管理者アカウント管理</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.counselors') }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-file-earmark-person-fill"></i>
                            <p class="mb-0 ms-2">相談員情報管理</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.residences') }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-house"></i>
                            <p class="mb-0 ms-2">住居情報管理</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.workschedules') }}" class="side-menu-link">
                        <div class="menu-text-light ms-3 my-4 d-flex
                        align-items-center">
                            <i class="bi bi-calendar-date"></i>
                            <p class="mb-0 ms-2">開所日編集</p>
                        </div>
                    </a>
                </div>
