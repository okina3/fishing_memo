<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\StoreFishRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class StoreFishRequestTest extends TestCase
{
   use RefreshDatabase;

   private User $user;

   // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
   protected function setUp(): void
   {
      // 親クラスのsetUpメソッドを呼び出し
      parent::setUp();
      // テスト用ユーザーを作成
      $this->user = User::factory()->create();
      // 認証済みのユーザーを返す
      $this->actingAs($this->user, 'users');
   }

   // authorizeメソッドが、常にtrue を返すことを検証するテスト
   public function testAuthorizeReturnsTrue()
   {
      // StoreFishRequestのインスタンスを初期化
      $request = new StoreFishRequest();
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
         'fish_name' => 'テスト魚名'
      ];

      // StoreFishRequestのインスタンスを初期化
      $request = new StoreFishRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが成功することを確認
      $this->assertTrue($validator->passes());
   }

   // バリデーションが、失敗することを確認するテスト
   public function testErrorRulesValidation()
   {
      // バリデーション用のデータを設定（魚名が、31文字以上）
      $data = [
         'fish_name' => str_repeat('あ', 31),
      ];

      // StoreFishRequestのインスタンスを初期化
      $request = new StoreFishRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが失敗することを確認
      $this->assertFalse($validator->passes());
   }

   // バリデーションエラーメッセージが、正しく設定されていることを確認するテスト
   public function testMessagesMethod()
   {
      // StoreFishRequestのインスタンスを初期化
      $request = new StoreFishRequest();
      // リクエストから、バリデーションメッセージを取得
      $messages = $request->messages();

      // 期待されるバリデーションメッセージを定義
      $expectedMessages = [
         'fish_name.required' => '魚名を入力してください。',
         'fish_name.string' => '魚名は文字列で入力してください。',
         'fish_name.max' => '魚名は、30文字以内で入力してください。',
         'fish_name.unique' => 'この魚はすでに登録されています。',
      ];

      // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
      $this->assertEquals($expectedMessages, $messages);
   }
}
