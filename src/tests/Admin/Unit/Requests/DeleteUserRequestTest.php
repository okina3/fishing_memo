<?php

namespace Tests\Admin\Unit\Requests;

use App\Http\Requests\Admin\DeleteUserRequest;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\Admin\TestCase;

class DeleteUserRequestTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // 管理者を作成
        $this->admin = Admin::factory()->create();

        // 認証済みの管理者を返す
        $this->actingAs($this->admin, 'admin');
    }

    // DeleteUserRequest のインスタンスを作成するヘルパーメソッド
    private function deleteUserRequest(): DeleteUserRequest
    {
        // DeleteUserRequestのインスタンスを返す
        return new DeleteUserRequest();
    }

    // authorize() が true を返すことを検証するテスト
    public function testAuthorizeReturnsTrue()
    {
        // DeleteUserRequestのインスタンスを初期化
        $request = $this->deleteUserRequest();
        // user() が認証済み admin を返すように設定
        $request->setUserResolver(function () {
            return $this->admin ?? null;
        });

        // authorize() メソッドが常に true を返すことを確認
        $this->assertTrue($request->authorize());
    }

    // バリデーションが、正しく機能することを確認するテスト
    public function testRulesValidation()
    {
        // バリデーション用のデータを設定
        $user = \App\Models\User::factory()->create();
        $data = ['userId' => $user->id];

        // DeleteUserRequestのインスタンスを初期化
        $request = $this->deleteUserRequest();
        // データをマージしてバリデータを作成
        $request->merge($data);
        $validator = Validator::make($request->all(), $request->rules());

        // バリデーションが成功することを確認
        $this->assertTrue($validator->passes());
    }

    // メッセージが正しく定義されていることを確認するテスト
    public function testMessagesMethod()
    {
        // DeleteUserRequestのインスタンスを初期化
        $request = $this->deleteUserRequest();
        // リクエストから、バリデーションメッセージを取得
        $messages = $request->messages();

        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'userId.required' => 'ユーザーIDは必須です。',
            'userId.integer' => 'ユーザーIDは整数で指定してください。',
            'userId.exists' => '指定されたユーザーIDは存在しません。',
        ];

        // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}
