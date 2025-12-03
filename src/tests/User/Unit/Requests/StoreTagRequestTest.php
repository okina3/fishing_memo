<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\StoreTagRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class StoreTagRequestTest extends TestCase
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

    // StoreTagRequestのインスタンスを作成するヘルパーメソッド
    private function storeTagRequest(): StoreTagRequest
    {
        // StoreTagRequestのインスタンスを返す
        return new StoreTagRequest();
    }

    // authorizeメソッドが、常にtrueを返すことを検証するテスト
    public function testAuthorizeReturnsTrue()
    {
        // storeTagRequestのインスタンスを初期化
        $request = $this->storeTagRequest();
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
        $data = [
            'new_tag' => 'テストタグ'
        ];
        // storeTagRequestのインスタンスを初期化
        $request = $this->storeTagRequest();
        // データをマージしてバリデータを作成
        $request->merge($data);
        $validator = Validator::make($request->all(), $request->rules());

        // バリデーションが成功することを確認
        $this->assertTrue($validator->passes());
    }

    // バリデーションが、失敗することを確認するテスト
    public function testErrorRulesValidation()
    {
        // バリデーション用のデータを設定（タグ名が、25文字以上）
        $data = [
            'new_tag' => 'テストタグ、テストタグ、テストタグ、テストタグ、テストタグ'
        ];
        // storeTagRequestのインスタンスを初期化
        $request = $this->storeTagRequest();
        // データをマージしてバリデータを作成
        $request->merge($data);
        $validator = Validator::make($request->all(), $request->rules());

        // バリデーションが失敗することを確認
        $this->assertFalse($validator->passes());
    }

    // バリデーションエラーメッセージが、正しく設定されていることを確認するテスト
    public function testMessagesMethod()
    {
        // storeTagRequestのインスタンスを初期化
        $request = $this->storeTagRequest();

        // リクエストから、バリデーションメッセージを取得
        $messages = $request->messages();
        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'new_tag.string' => 'タグが、入力されていません。また、文字列で指定してください。',
            'new_tag.max' => 'タグは、25文字以内で入力してください。',
            'new_tag.unique' => 'このタグは、すでに登録されています。'
        ];

        // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}
