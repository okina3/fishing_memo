<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\StoreSpotRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class StoreSpotRequestTest extends TestCase
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
      // StoreSpotRequestのインスタンスを初期化
      $request = new StoreSpotRequest();
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
         'spot_name' => 'テスト釣り場'
      ];

      // StoreSpotRequestのインスタンスを初期化
      $request = new StoreSpotRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが成功することを確認
      $this->assertTrue($validator->passes());
   }

   // バリデーションが、失敗することを確認するテスト
   public function testErrorRulesValidation()
   {
      // 26文字以上を入れて max 制約に引っかける
      $data = [
         'spot_name' => str_repeat('あ', 26),
      ];

      // StoreSpotRequestのインスタンスを初期化
      $request = new StoreSpotRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが失敗することを確認
      $this->assertFalse($validator->passes());
   }

   // バリデーションエラーメッセージが、正しく設定されていることを確認するテスト
   public function testMessagesMethod()
   {
      // StoreSpotRequestのインスタンスを初期化
      $request = new StoreSpotRequest();
      // リクエストから、バリデーションメッセージを取得
      $messages = $request->messages();

      // 期待されるバリデーションメッセージを定義
      $expectedMessages = [
         'spot_name.required' => '釣り場を入力してください。',
         'spot_name.string' => '釣り場名は文字列で入力してください。',
         'spot_name.max' => '釣り場は、25文字以内で入力してください。',
         'spot_name.unique' => 'この釣り場はすでに登録されています。',
      ];

      // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
      $this->assertEquals($expectedMessages, $messages);
   }
}
