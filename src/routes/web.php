<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\MemoController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// ユーザー用ダッシュボード
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth:users', 'verified'])->name('dashboard');

// 公開トップページ
Route::get('/', function () {
    // ログイン済みの users ガードがいる場合はメモ一覧へリダイレクト
    if (auth('users')->check()) {
        return redirect()->route('user.index');
    }
    // 未ログイン時は公開トップを表示
    return view('user.top');
});

// ユーザー用ルーティング
Route::prefix('/')->as('user.')->group(function () {
    Route::middleware('auth:users')->group(function () {
        //メモ管理画面
        Route::controller(MemoController::class)->group(function () {
            Route::get('index', 'index')->name('index');
        });

        // プロフィール関連（デフォルト）
        // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__ . '/auth.php';
// 管理者用ルートを読み込み
require __DIR__ . '/admin.php';
