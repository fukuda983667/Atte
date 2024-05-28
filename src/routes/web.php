<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StampController;

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

// デフォルトで用意されているミドルウェアを使用
// ログイン済みの場合のみ以下のルーティングが有効になる。
Route::middleware('auth')->group(function () {
    Route::get('/', [StampController::class, 'index'])->name('index');
    Route::post('/store/clock-in', [StampController::class, 'storeClockIn']);
    Route::post('/store/clock-out', [StampController::class, 'storeClockOut']);
    Route::post('/store/start-time', [StampController::class, 'storeStartTime']);
    Route::post('/store/end-time', [StampController::class, 'storeEndTime']);
    // 一覧表示ページに遷移する
    Route::get('/attendance', [StampController::class, 'list'])->name('attendance');
});

