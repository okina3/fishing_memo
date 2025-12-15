<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\DeleteTagRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class DeleteTagRequestTest extends TestCase
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

   // authorizeメソッドが、常にtrueを返すことを検証するテスト
   public function testAuthorizeReturnsTrue()
   {
      // DeleteTagRequestのインスタンスを初期化
      $request = new DeleteTagRequest();
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
      $data = ['tags' => [1, 2, 3]];

      // DeleteTagRequestのインスタンスを初期化
      $request = new DeleteTagRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが成功することを確認
      $this->assertTrue($validator->passes());
   }

   // メッセージが正しく定義されていることを確認するテスト
   public function testMessagesMethod()
   {
      // DeleteTagRequestのインスタンスを初期化
      $request = new DeleteTagRequest();
      // リクエストから、バリデーションメッセージを取得
      $messages = $request->messages();

      // 期待されるバリデーションメッセージを定義
      $expectedMessages = [
         'tags.required' => '削除したいタグに、チェックを入れてください。',
      ];

      // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
      $this->assertEquals($expectedMessages, $messages);
   }
}
