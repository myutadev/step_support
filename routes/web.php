<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminRegisterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Models\Attendance;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 利用者さん用出退勤route
Route::get('attendances/timecard', [AttendanceController::class, 'timecard'])->name('attendances.timecard')->middleware('auth');
Route::post('attendances/timecard/submit-month', [AttendanceController::class, 'submitMonth'])->name('attendances.timecard.submit.month')->middleware('auth');


Route::resource('attendances', AttendanceController::class)
    ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');



// 利用者さん用出退勤route

Route::post('attendances/checkin', [AttendanceController::class, 'checkin'])->name('attendances.checkin')->middleware('auth');

Route::post('attendances/rest-start', [AttendanceController::class, 'restStart'])->name('attendances.rest.start')->middleware('auth');

Route::post('attendances/rest-end', [AttendanceController::class, 'restEnd'])->name('attendances.rest.end')->middleware('auth');

Route::post('attendances/overtime-start', [AttendanceController::class, 'overtimeStart'])->name('attendances.overtime.start')->middleware('auth');

Route::post('attendances/overtime-end', [AttendanceController::class, 'overtimeEnd'])->name('attendances.overtime.end')->middleware('auth');

Route::post('attendances/checkout', [AttendanceController::class, 'checkout'])->name('attendances.checkout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| 管理者用ルーティング
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin'], function () {
    // 登録
    Route::get('register', [AdminRegisterController::class, 'create'])
        ->name('admin.register');

    Route::post('register', [AdminRegisterController::class, 'store']);

    // ログイン
    Route::get('login', [AdminLoginController::class, 'showLoginPage'])
        ->name('admin.login');

    Route::post('login', [AdminLoginController::class, 'login']);

    // 以下の中は認証必須のエンドポイントとなる
    Route::middleware(['auth:admin'])->group(function () {
        // ダッシュボード
        Route::get('dashboard', fn () => view('admin.dashboard'))
            ->name('admin.dashboard');

        Route::get('timecard', [AdminAttendanceController::class, 'showTimecard'])->name('admin.timecard');
        Route::get('daily', [AdminAttendanceController::class, 'showDaily'])->name('admin.daily');
        Route::get('users', [AdminAttendanceController::class, 'showUsers'])->name('admin.users');
        Route::get('users/create', [AdminAttendanceController::class, 'createUser'])->name('admin.users.create');
        Route::post('users/store', [AdminAttendanceController::class, 'storeUser'])->name('admin.users.store');
        Route::get('users/{id}/edit', [AdminAttendanceController::class, 'editUser'])->name('admin.users.edit');
        Route::patch('users/{id}/update', [AdminAttendanceController::class, 'updateUser'])->name('admin.users.update');
        Route::get('admins', [AdminAttendanceController::class, 'showAdmins'])->name('admin.admins');
        Route::get('admins/create', [AdminAttendanceController::class, 'createAdmin'])->name('admin.admins.create');
        Route::post('admins/store', [AdminAttendanceController::class, 'storeAdmin'])->name('admin.admins.store');
        Route::get('admins/{id}/edit', [AdminAttendanceController::class, 'editAdmin'])->name('admin.admins.edit');
        Route::patch('admins/{id}/update', [AdminAttendanceController::class, 'updateAdmin'])->name('admin.admins.update');
        Route::patch('daily/{attendance}', [AdminAttendanceController::class, 'updateAdminComment'])->name('admin.daily.update');
    });
});

// admin用ルート
Route::post('attendances/timecard/submit-month', [AdminAttendanceController::class, 'submitMonth'])->name('attendances.timecard.submit.month')->middleware('auth');



require __DIR__ . '/auth.php';
