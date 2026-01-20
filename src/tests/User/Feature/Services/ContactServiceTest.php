<?php

namespace Tests\User\Feature\Services;

use App\Http\Requests\User\ContactRequest;
use App\Models\User;
use App\Services\User\ContactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class ContactServiceTest extends TestCase
{
   use RefreshDatabase;

   private User $user;
   private User $secondaryUser;

   // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
   protected function setUp(): void
   {
      // 親クラスのsetUpメソッドを呼び出し
      parent::setUp();
      // ユーザーを作成
      $this->user = User::factory()->create();
      // 2人目の別のユーザーを作成
      $this->secondaryUser = User::factory()->create();
      // 認証済みのユーザーを返す
      $this->actingAs($this->user, 'users');
   }

   // 問い合わせを保存するメソッドのテスト
   public function testCreateContact()
   {
      // 問い合わせ内容のデータをセット
      $request = ContactRequest::create('/', 'POST', [
         'subject' => 'お問い合わせの件名',
         'message' => '問い合わせ本文の内容です。',
      ]);

      // サービスを使ってメモを作成
      $created = ContactService::createContact($request);

      // DB にレコードが存在することを確認
      $this->assertDatabaseHas('contacts', [
         'id' => $created->id,
         'user_id' => $this->user->id,
         'subject' => 'お問い合わせの件名',
         'message' => '問い合わせ本文の内容です。',
      ]);
   }
}
