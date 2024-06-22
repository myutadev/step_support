<?php

use App\Http\Controllers\Admin\Admin\CreateAdminController;
use App\Http\Controllers\Admin\Admin\EditAdminController;
use App\Http\Controllers\Admin\Admin\IndexAdminController;
use App\Http\Controllers\Admin\Admin\StoreAdminController;
use App\Http\Controllers\Admin\Admin\UpdateAdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminRegisterController;
use App\Http\Controllers\Admin\Counselor\CreateCounselorController;
use App\Http\Controllers\Admin\Counselor\DeleteCounselorController;
use App\Http\Controllers\Admin\Counselor\EditCounselorController;
use App\Http\Controllers\Admin\Counselor\IndexCounselorController;
use App\Http\Controllers\Admin\Counselor\StoreCounselorController;
use App\Http\Controllers\Admin\Counselor\UpdateCounselorController;
use App\Http\Controllers\Admin\DailyAttendance\IndexDailyAttendanceController;
use App\Http\Controllers\Admin\DailyAttendance\StoreAdminCommentController;
use App\Http\Controllers\Admin\DailyAttendance\UpdateAdminCommentController;
use App\Http\Controllers\Admin\Export\ExportAttendanceController;
use App\Http\Controllers\Admin\Export\ShowExportPageController;
use App\Http\Controllers\Admin\Residence\CreateResidenceController;
use App\Http\Controllers\Admin\Residence\DeleteResidenceController;
use App\Http\Controllers\Admin\Residence\EditResidenceController;
use App\Http\Controllers\Admin\Residence\IndexResidenceController;
use App\Http\Controllers\Admin\Residence\StoreResidenceController;
use App\Http\Controllers\Admin\Residence\UpdateResidenceController;
use App\Http\Controllers\Admin\Timecard\IndexTimecardController;
use App\Http\Controllers\Admin\User\CreateUserController;
use App\Http\Controllers\Admin\User\EditUserController;
use App\Http\Controllers\Admin\User\IndexUserController;
use App\Http\Controllers\Admin\User\StoreUserController;
use App\Http\Controllers\Admin\User\UpdateUserController;
use App\Http\Controllers\Admin\Workschedule\CreateWorkscheduleController;
use App\Http\Controllers\Admin\Workschedule\DeleteWorkscheduleController;
use App\Http\Controllers\Admin\Workschedule\IndexWorkscheduleController;
use App\Http\Controllers\Admin\Workschedule\StoreWorkscheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminReportController;
// use App\Http\Controllers\AdminTimecard\IndexTimecardController;
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

// 利用者さん用表示ルート
Route::get('attendances/timecard/{yearmonth?}', [AttendanceController::class, 'timecard'])->name('attendances.timecard')->middleware('auth');


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
    Route::post('logout', [AdminLoginController::class, 'destroy'])->name('admin.destroy');

    // 以下の中は認証必須のエンドポイントとなる
    Route::middleware(['auth:admin'])->group(function () {
        // ダッシュボード
        Route::get('dashboard', fn () => view('admin.dashboard'))
            ->name('admin.dashboard');

        Route::get('timecard/{yearmonth?}/{id?}', IndexTimecardController::class)->name('admin.timecard');
        // Route::post('timecard/{yearmonth?}/{id?}', [AdminAttendanceController::class, 'submitMonth'])->name('admin.timecard.submit.month')->middleware('auth');
        Route::get('users', IndexUserController::class)->name('admin.users');
        Route::get('users/create', CreateUserController::class)->name('admin.users.create');
        Route::post('users/store', StoreUserController::class)->name('admin.users.store');
        Route::get('users/{id}/edit', EditUserController::class)->name('admin.users.edit');
        Route::patch('users/{id}/update', UpdateUserController::class)->name('admin.users.update');
        Route::get('daily/{date?}', IndexDailyAttendanceController::class)->name('admin.daily');
        Route::patch('daily/{admincomment}', UpdateAdminCommentController::class)->name('admin.daily.update');
        Route::patch('daily/{attendance}/store', StoreAdminCommentController::class)->name('admin.daily.store');
        Route::get('admins', IndexAdminController::class)->name('admin.admins');
        Route::get('admins/create', CreateAdminController::class)->name('admin.admins.create');
        Route::post('admins/store', StoreAdminController::class)->name('admin.admins.store');
        Route::get('admins/{id}/edit', EditAdminController::class)->name('admin.admins.edit');
        Route::patch('admins/{id}/update', UpdateAdminController::class)->name('admin.admins.update');

        //report 
        Route::get('report', [AdminReportController::class, 'index'])->name('admin.report');


        //settings
        Route::get('settings/counselors', IndexCounselorController::class)->name('admin.counselors');
        Route::get('settings/counselors/create', CreateCounselorController::class)->name('admin.counselors.create');
        Route::post('settings/counselors/store', StoreCounselorController::class)->name('admin.counselors.store');
        Route::get('settings/counselors/{id}/edit', EditCounselorController::class)->name('admin.counselors.edit');
        Route::patch('settings/counselors/{id}/update', UpdateCounselorController::class)->name('admin.counselors.update');
        Route::delete('settings/counselors/{id}', DeleteCounselorController::class)->name('admin.counselors.destroy');
        //residence
        Route::get('settings/residences', IndexResidenceController::class)->name('admin.residences');
        Route::get('settings/residences/create', CreateResidenceController::class)->name('admin.residences.create');
        Route::post('settings/residences/store', StoreResidenceController::class)->name('admin.residences.store');
        Route::get('settings/residences/{id}/edit', EditResidenceController::class)->name('admin.residences.edit');
        Route::patch('settings/residences/{id}/update', UpdateResidenceController::class)->name('admin.residences.update');
        Route::delete('settings/residences/{id}', DeleteResidenceController::class)->name('admin.residences.destroy');
        //workshcedule
        Route::get('settings/workschedules/show/{yearmonth?}', IndexWorkscheduleController::class)->name('admin.workschedules');
        Route::get('settings/workschedules/create/{id}', CreateWorkscheduleController::class)->name('admin.workschedules.create');
        Route::post('settings/workschedules/store', StoreWorkscheduleController::class)->name('admin.workschedules.store');
        Route::delete('settings/workschedules/destroy/{id}', DeleteWorkscheduleController::class)->name('admin.workschedules.destroy');
        // Route::patch('settings/workschedules/{id}/update', [AdminAttendanceController::class, 'updateWorkschedules'])->name('admin.workschedules.update');
        //export
        Route::get('export/show', ShowExportPageController::class)->name('admin.export.show');
        Route::post('export', ExportAttendanceController::class)->name('admin.export');
        //edit attendances
        Route::get('attendance/{id}/edit', [AdminAttendanceController::class, 'editAttendance'])->name('admin.attendance.edit');
        Route::patch('attendance/{id}/update', [AdminAttendanceController::class, 'updateAttendance'])->name('admin.attendance.update');
        Route::post('attendance/store/leave/{user_id}/{sched_id}', [AdminAttendanceController::class, 'storeLeave'])->name('admin.store.leave');
    });
});

// admin用ルート




require __DIR__ . '/auth.php';
