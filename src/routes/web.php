<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\BaitController;
use App\Http\Controllers\User\FishNameController;
use App\Http\Controllers\User\ImageController;
use App\Http\Controllers\User\MastersController;
use App\Http\Controllers\User\MemoController;
use App\Http\Controllers\User\SpotController;
use App\Http\Controllers\User\TagController;
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

        // マスターズ管理画面（釣り場・エサ・魚名）
        Route::controller(MastersController::class)->prefix('masters')->group(function () {
            Route::get('/', 'index')->name('masters.index');
            Route::post('/spot/store', 'storeSpot')->name('masters.spot.store');
            Route::post('/bait/store', 'storeBait')->name('masters.bait.store');
            Route::post('/fish-name/store', 'storeFishName')->name('masters.fish-name.store');
        });

        // 釣り場の登録
        Route::controller(SpotController::class)->prefix('spot')
            ->group(function () {
                Route::get('/edit/{spot}', 'edit')->name('spot.edit');
                Route::patch('/update', 'update')->name('spot.update');
                Route::delete('/destroy', 'destroy')->name('spot.destroy');
            });

        // エサの登録
        Route::controller(BaitController::class)->prefix('bait')
            ->group(function () {
                Route::get('/edit/{bait}', 'edit')->name('bait.edit');
                Route::patch('/update', 'update')->name('bait.update');
                Route::delete('/destroy', 'destroy')->name('bait.destroy');
            });

        // 魚名の登録
        Route::controller(FishNameController::class)->prefix('fish-name')
            ->group(function () {
                Route::get('/edit/{fishName}', 'edit')->name('fish-name.edit');
                Route::patch('/update', 'update')->name('fish-name.update');
                Route::delete('/destroy', 'destroy')->name('fish-name.destroy');
            });

        //タグ管理画面
        Route::controller(TagController::class)->prefix('tag')->group(function () {
            Route::get('/', 'index')->name('tag.index');
            Route::post('/store', 'store')->name('tag.store');
            Route::delete('/destroy', 'destroy')->name('tag.destroy');
        });

        //画像管理画面
        Route::controller(ImageController::class)->prefix('image')->group(function () {
            Route::get('/', 'index')->name('image.index');
            Route::get('/create', 'create')->name('image.create');
            Route::post('/store', 'store')->name('image.store');
            Route::get('/show/{image}', 'show')->name('image.show');
            Route::delete('/destroy', 'destroy')->name('image.destroy');
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
