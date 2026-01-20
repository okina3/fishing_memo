<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\UpdateSharedMemoRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class UpdateSharedMemoRequestTest extends TestCase
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

   // authorize() が true を返すことを検証
   public function testAuthorizeReturnsTrue()
   {
      // UpdateSharedMemoRequestのインスタンスを初期化
      $request = new UpdateSharedMemoRequest();
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
      // メモが存在すること
      $memo = \App\Models\Memo::factory()->create([
         'user_id' => $this->user->id,
      ]);

      // バリデーション用のデータを設定（全てルールに合う値）
      $data = [
         'memoId' => $memo->id,
         'content' => '共有メモの更新テスト用の備考',
      ];

      // UpdateSharedMemoRequestのインスタンスを初期化
      $request = new UpdateSharedMemoRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが成功することを確認
      $this->assertTrue($validator->passes());
   }

   // バリデーションが、失敗することを確認するテスト
   public function testErrorRulesValidation()
   {
      // memoId が無い、または content が空の場合は通らない
      $data = [
         'memoId' => null,
         'content' => '',
      ];

      // UpdateSharedMemoRequestのインスタンスを初期化
      $request = new UpdateSharedMemoRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが失敗することを確認
      $this->assertFalse($validator->passes());
   }

   // メッセージが正しく定義されていることを確認するテスト
   public function testMessagesMethod()
   {
      // UpdateSharedMemoRequestのインスタンスを初期化
      $request = new UpdateSharedMemoRequest();

      // リクエストから、バリデーションメッセージを取得
      $messages = $request->messages();

      // 期待されるバリデーションメッセージを定義
      $expectedMessages = [
         'memoId.required' => 'メモIDが指定されていません。',
         'memoId.integer' => 'メモIDの形式が不正です。',
         'memoId.exists' => '選択されたメモは存在しません。',

         'content.required' => '備考を入力してください。',
         'content.string' => '備考は文字列で入力してください。',
         'content.max' => '備考は1000文字以内で入力してください。',
      ];

      // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
      $this->assertEquals($expectedMessages, $messages);
   }
}
