<!-- ユーザー情報とログアウトボタン -->
<div class="user-header bg-light py-3">
    <div class="d-flex justify-content-end align-items-center px-4">
        <!-- ユーザー名とアイコンのドロップダウン -->
        <div class="dropdown">
            <a href="#" class="dropdown-toggle d-flex align-items-center text-decoration-none"
                data-bs-toggle="dropdown">
                {{-- <i class="bi bi-person-circle me-2" style="color: gray;"></i> --}}
                <span
                    class="font-medium text-base text-dark">{{ Auth::user()->last_name . ' ' . Auth::user()->first_name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <!-- ログアウトボタン -->
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            ログアウト
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
