<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\BaitController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\FishNameController;
use App\Http\Controllers\User\ImageController;
use App\Http\Controllers\User\MastersController;
use App\Http\Controllers\User\MemoController;
use App\Http\Controllers\User\RodController;
use App\Http\Controllers\User\ShareSettingController;
use App\Http\Controllers\User\SpotController;
use App\Http\Controllers\User\TrashedMemoController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// 公開トップページ
Route::get('/', function () {
    return view('user.top');
});

// ユーザー用ルーティング
Route::prefix('/')->as('user.')->group(function () {
    Route::middleware('auth:users')->group(function () {
        // ユーザー用ダッシュボード
        Route::get('dashboard', function () {
            return view('user.dashboard');
        })->middleware('verified')->name('dashboard');

        //メモ管理画面
        Route::controller(MemoController::class)->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('show/{memo}', 'show')->name('show');
            Route::get('edit/{memo}', 'edit')->name('edit');
            Route::patch('update', 'update')->name('update');
            Route::delete('destroy', 'destroy')->name('destroy');
        });

        // マスターズ管理画面（釣り場・エサ・魚名）
        Route::controller(MastersController::class)->prefix('masters')->group(function () {
            Route::get('/', 'index')->name('masters.index');
        });

        // 釣り場の登録
        Route::controller(SpotController::class)->prefix('spot')
            ->group(function () {
                Route::post('/store', 'store')->name('spot.store');
                Route::get('/edit/{spot}', 'edit')->name('spot.edit');
                Route::patch('/update', 'update')->name('spot.update');
                Route::delete('/destroy', 'destroy')->name('spot.destroy');
            });

        // 釣り竿の登録
        Route::controller(RodController::class)->prefix('rod')
            ->group(function () {
                Route::post('/store', 'store')->name('rod.store');
                Route::get('/edit/{rod}', 'edit')->name('rod.edit');
                Route::patch('/update', 'update')->name('rod.update');
                Route::delete('/destroy', 'destroy')->name('rod.destroy');
            });

        // エサの登録
        Route::controller(BaitController::class)->prefix('bait')
            ->group(function () {
                Route::post('/store', 'store')->name('bait.store');
                Route::get('/edit/{bait}', 'edit')->name('bait.edit');
                Route::patch('/update', 'update')->name('bait.update');
                Route::delete('/destroy', 'destroy')->name('bait.destroy');
            });

        // 魚名の登録
        Route::controller(FishNameController::class)->prefix('fish-name')
            ->group(function () {
                Route::post('/store', 'store')->name('fish-name.store');
                Route::get('/edit/{fishName}', 'edit')->name('fish-name.edit');
                Route::patch('/update', 'update')->name('fish-name.update');
                Route::delete('/destroy', 'destroy')->name('fish-name.destroy');
            });

        //画像管理画面
        Route::controller(ImageController::class)->prefix('image')->group(function () {
            Route::get('/', 'index')->name('image.index');
            Route::get('/create', 'create')->name('image.create');
            Route::post('/store', 'store')->name('image.store');
            Route::get('/show/{image}', 'show')->name('image.show');
            Route::delete('/destroy', 'destroy')->name('image.destroy');
        });

        //共有メモ画面
        Route::controller(ShareSettingController::class)->prefix('share-setting')->group(function () {
            Route::get('/', 'index')->name('share-setting.index');
            Route::post('/store', 'store')->name('share-setting.store');
            Route::get('/show/{share}', 'show')->name('share-setting.show');
            Route::get('/edit/{share}', 'edit')->name('share-setting.edit');
            Route::patch('/update', 'update')->name('share-setting.update');
            Route::delete('/destroy', 'destroy')->name('share-setting.destroy');
        });

        // 問い合わせ画面
        Route::controller(ContactController::class)->prefix('contact')->group(function () {
            Route::get('/create', 'create')->name('contact.create');
            Route::post('/store', 'store')->name('contact.store');
        });

        //ソフトデリートしたメモの画面
        Route::controller(TrashedMemoController::class)->prefix('trashed-memo')->group(function () {
            Route::get('/', 'index')->name('trashed-memo.index');
            Route::patch('/undo', 'undo')->name('trashed-memo.undo');
            Route::delete('/destroy', 'destroy')->name('trashed-memo.destroy');
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
