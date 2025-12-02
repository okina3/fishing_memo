<?php

namespace Tests\User\Feature\Services;

use App\Services\SessionService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Session;
use Tests\User\TestCase;

class SessionServiceTest extends TestCase
{
    // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // テスト全体で使用するブラウザバックキーをセット
        config(['common_browser_back.browser_back_key' => 'test_browser_back_key']);
        // セッションをクリアしてテストの独立性を保つ
        Session::flush();
    }

    // 正常系: ブラウザバック用のセッションに値を設定するテスト
    public function testSetBrowserBackSession()
    {
        // セッションにブラウザバック用の値を設定するサービスメソッドを実行
        SessionService::setBrowserBackSession();

        // セッションが設定されたことを確認
        $this->assertTrue(Session::has('back_button_clicked'));

        // セッション値を復号し、期待される環境変数の値と比較
        $decryptedSessionValue = decrypt(Session::get('back_button_clicked'));
        $this->assertEquals(config('common_browser_back.browser_back_key'), $decryptedSessionValue);
    }

    // 異常系: ブラウザバック用のセッションに値を設定する例外のテスト
    public function testErrorSetBrowserBackSession()
    {
        // 現在の設定値を退避してからテスト用に上書き（後で必ず復元）
        $original = config('common_browser_back.browser_back_key');
        // テスト環境でBROWSER_BACK_KEYをnullに設定
        config(['common_browser_back.browser_back_key' => null]);

        try {
            // 例外が投げられることを期待
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('BROWSER_BACK_KEY is not set in the environment file.');

            // メソッドを実行して例外を確認
            SessionService::setBrowserBackSession();
        } finally {
            // 他テストへの影響を避けるため元の設定を復元
            config(['common_browser_back.browser_back_key' => $original]);
        }
    }

    // 正常系: セッション値が正しければ例外を投げないテスト
    public function testClickBrowserBackSession()
    {
        // セッションに正しい値を設定
        Session::flash('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));
        // セッション値を検証するサービスメソッドを実行
        SessionService::clickBrowserBackSession();
        // 例外が投げられないことを確認
        $this->assertTrue(true);
    }

    // 異常系: セッション値が不正な場合に HttpResponseException を投げるテスト
    public function testErrorClickBrowserBackSession()
    {
        // セッションに不正な値を設定
        Session::flash('back_button_clicked', encrypt('wrong_key'));
        // 期待される例外のクラスを指定
        $this->expectException(HttpResponseException::class);
        // セッション値を検証するサービスメソッドを実行し、不正な値のため例外が投げられることを確認
        SessionService::clickBrowserBackSession();
    }

    // ブラウザバック用のセッションの値を削除するテスト
    public function testResetBrowserBackSession()
    {
        // セッションに値を設定
        Session::flash('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));
        // セッションに値があることを確認
        $this->assertTrue(Session::has('back_button_clicked'));
        // セッションの値を削除するサービスメソッドを実行
        SessionService::resetBrowserBackSession();

        // セッションの値が削除されたことを確認
        $this->assertFalse(Session::has('back_button_clicked'));
    }
}
