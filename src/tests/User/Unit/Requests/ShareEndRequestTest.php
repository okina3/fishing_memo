<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\ShareEndRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class ShareEndRequestTest extends TestCase
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
      // ShareEndRequestのインスタンスを初期化
      $request = new ShareEndRequest();

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
      // バリデーション用のユーザーを作成
      User::factory()->create(['email' => 'test@example.com']);
      // バリデーション用のデータを設定
      $data = [
         'share_user_end' => 'test@example.com'
      ];
      // ShareEndRequestのインスタンスを初期化
      $request = new ShareEndRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが成功することを確認
      $this->assertTrue($validator->passes());
   }

   // バリデーションが、失敗することを確認するテスト
   public function testErrorRulesValidation()
   {
      // バリデーション用のユーザーを作成（メールアドレスが、文字列）
      User::factory()->create(['email' => 'あいうえお']);
      // バリデーション用のデータを設定
      $data = [
         'share_user_end' => 'あいうえお'
      ];
      // ShareEndRequestのインスタンスを初期化
      $request = new ShareEndRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが失敗することを確認
      $this->assertFalse($validator->passes());
   }

   // バリデーションエラーメッセージが、正しく設定されていることを確認するテスト
   public function testMessagesMethod()
   {
      // ShareEndRequestのインスタンスを初期化
      $request = new ShareEndRequest();
      // リクエストから、バリデーションメッセージを取得
      $messages = $request->messages();

      // 期待されるバリデーションメッセージを定義
      $expectedMessages = [
         'share_user_end.required' => 'メールアドレスが、入力されていません。共有停止できません。',
         'share_user_end.email' => 'メールアドレスを、入力してください。共有停止できません。',
         'share_user_end.exists' => '指定されたメールアドレスのユーザーが見つかりません。また自分のものです。共有停止できません。'
      ];

      // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
      $this->assertEquals($expectedMessages, $messages);
   }
}
