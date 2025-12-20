<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\StoreRodRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class StoreRodRequestTest extends TestCase
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
      // StoreRodRequestのインスタンスを初期化
      $request = new StoreRodRequest();
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
         'rod_name' => 'テスト竿'
      ];

      // StoreRodRequestのインスタンスを初期化
      $request = new StoreRodRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが成功することを確認
      $this->assertTrue($validator->passes());
   }

   // バリデーションが、失敗することを確認するテスト
   public function testErrorRulesValidation()
   {
      // 31文字以上を入れて max 制約に引っかける 
      $data = [
         'rod_name' => str_repeat('あ', 31),
      ];

      // StoreRodRequestのインスタンスを初期化
      $request = new StoreRodRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが失敗することを確認
      $this->assertFalse($validator->passes());
   }

   // バリデーションエラーメッセージが、正しく設定されていることを確認するテスト
   public function testMessagesMethod()
   {
      // StoreRodRequestのインスタンスを初期化
      $request = new StoreRodRequest();
      // リクエストから、バリデーションメッセージを取得
      $messages = $request->messages();

      // 期待されるバリデーションメッセージを定義
      $expectedMessages = [
         'rod_name.required' => '釣り竿を入力してください。',
         'rod_name.string' => '釣り竿名は文字列で入力してください。',
         'rod_name.max' => '釣り竿は、30文字以内で入力してください。',
         'rod_name.unique' => 'この釣り竿はすでに登録されています。',
      ];

      // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
      $this->assertEquals($expectedMessages, $messages);
   }
}
