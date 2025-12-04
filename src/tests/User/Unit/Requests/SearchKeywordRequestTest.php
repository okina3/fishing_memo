<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\SearchKeywordRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class SearchKeywordRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ユーザーを作成
        $this->user = User::factory()->create();

        // 認証済みのユーザーを返す
        $this->actingAs($this->user, 'users');
    }

    // SearchKeywordRequest のインスタンスを作成するヘルパーメソッド
    private function searchKeywordRequest(): SearchKeywordRequest
    {
        // SearchKeywordRequestのインスタンスを返す
        return new SearchKeywordRequest();
    }

    // authorizeメソッドが、常にtrueを返すことを検証するテスト
    public function testAuthorizeReturnsTrue()
    {
        // SearchKeywordRequestのインスタンスを初期化
        $request = $this->searchKeywordRequest();
        // user() が認証ユーザーを返すように UserResolver を設定
        $request->setUserResolver(function () {
            return $this->user ?? null;
        });

        // authorize() メソッドが常に true を返すことを確認
        $this->assertTrue($request->authorize());
    }

    // バリデーションが、正しく機能することを確認するテスト
    public function testRulesValidation()
    {
        // バリデーション用のデータを設定
        $data = ['keyword' => '川釣り'];

        // SearchKeywordRequestのインスタンスを初期化
        $request = $this->searchKeywordRequest();
        // データをマージしてバリデータを作成
        $request->merge($data);
        $validator = Validator::make($request->all(), $request->rules());

        // バリデーションが成功することを確認
        $this->assertTrue($validator->passes());
    }

    // バリデーションが、失敗することを確認するテスト
    public function testErrorRulesValidation()
    {
        // バリデーション用のデータを設定（キーワードが、51文字以上）
        $long = str_repeat('あ', 51);
        $data = ['keyword' => $long];

        // SearchKeywordRequestのインスタンスを初期化
        $request = $this->searchKeywordRequest();
        // データをマージしてバリデータを作成
        $request->merge($data);
        $validator = Validator::make($request->all(), $request->rules());

        // バリデーションが失敗することを確認
        $this->assertFalse($validator->passes());
    }

    // バリデーションエラーメッセージが、正しく設定されていることを確認するテスト
    public function testMessagesMethod()
    {
        // SearchKeywordRequestのインスタンスを初期化
        $request = $this->searchKeywordRequest();
        // リクエストから、バリデーションメッセージを取得
        $messages = $request->messages();

        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'keyword.string' => 'キーワードは、文字列で指定してください。',
            'keyword.max' => 'キーワードは、50文字以内で入力してください。',
        ];

        // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}
