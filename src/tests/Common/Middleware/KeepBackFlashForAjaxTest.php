<?php

namespace Tests\Common\Middleware;

use App\Http\Middleware\KeepBackFlashForAjax;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Common\TestCase;

class KeepBackFlashForAjaxTest extends TestCase
{
    use RefreshDatabase;

    // Ajaxリクエストでは session が次リクエスト以降も保持されることをテスト
    public function test_ajax_keeps_flash(): void
    {
        // ミドルウェアを適用したテスト用ルートを定義
        Route::middleware(['web', KeepBackFlashForAjax::class])
            ->get('/flash-ajax', function () {
                session()->flash('back_button_clicked', true);
                return response('ok');
            })
            ->name('test.flash-ajax');

        // セッション確認用のルートを定義
        Route::middleware(['web', KeepBackFlashForAjax::class])
            ->get('/probe', function () {
                return response('ok');
            })
            ->name('test.probe');

        // 1回目: Ajaxとしてアクセスしてフラッシュを設定（KeepBackFlashForAjax を実行）
        $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])->get('/flash-ajax')->assertStatus(200);

        // 2回目: 次リクエストでも session が存在
        $this->get('/probe')->assertStatus(200);
        $this->assertTrue(session()->has('back_button_clicked'));

        // 3回目: 更に次のリクエストでも session が存在
        $this->get('/probe')->assertStatus(200);
        $this->assertTrue(session()->has('back_button_clicked'));
    }
}
