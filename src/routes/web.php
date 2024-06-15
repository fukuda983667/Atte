<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\StampController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// デフォルトで用意されているミドルウェアを使用
// ミドルウェアは複数適用できる。左から順に処理される。ミドルウェアはリクエストのフィルターの役割
Route::middleware('auth','verified')->group(function () {
    Route::get('/', [StampController::class, 'index'])->name('index');
    Route::post('/store/clock-in', [StampController::class, 'storeClockIn']);
    Route::post('/store/clock-out', [StampController::class, 'storeClockOut']);
    Route::post('/store/start-time', [StampController::class, 'storeStartTime']);
    Route::post('/store/end-time', [StampController::class, 'storeEndTime']);
    // 一覧表示ページに遷移する
    Route::get('/attendance', [StampController::class, 'list'])->name('attendance');

    //ユーザ一覧、個別の勤怠表
    Route::get('/users', [UserController::class, 'userList'])->name('userList');
    Route::get('/users/attendance', [UserController::class, 'userAttendance'])->name('usersAttendance');
});

