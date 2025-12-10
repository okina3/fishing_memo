<?php

namespace Tests\Admin\Feature\Controllers;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class TrashedContactControllerTest extends TestCase
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
        // 認証済みの管理者を返す（ガード名は 'admin'）
        $this->actingAs($this->admin, 'admin');
    }

    // ソフトデリートした問い合わせ一覧が、正しく表示されることをテスト
    public function testIndexTrashedContactController()
    {
        // 認証済み管理者で一覧ルートへアクセス
        $response = $this->get(route('admin.trashed-contact.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（admin.trashedContacts.index）であることを検証
        $response->assertViewIs('admin.trashedContacts.index');
        // ビューに渡される主要なデータ（ソフトデリートされた問い合わせ）が存在することを検証
        $response->assertViewHasAll(['all_trashed_contacts']);
    }

    // ソフトデリートした問い合わせを元に戻せることをテスト
    public function testUndoTrashedContactController()
    {
        // 1件のソフトデリートされた問い合わせを作成
        $contact = Contact::factory()->for(User::factory())->create(['deleted_at' => now()]);

        // ソフトデリートした問い合わせを、元に戻す為に、リクエストを送信
        $response = $this->patch(route('admin.trashed-contact.undo'), ['contentId' => $contact->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('admin.trashed-contact.index'));
        $response->assertSessionHas(['message' => 'ユーザーの問い合わせを、元に戻しました。', 'status' => 'success']);

        // 問い合わせが元に戻されたことを確認
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'deleted_at' => null,
        ]);
    }

    // ソフトデリートした問い合わせを完全削除できることをテスト
    public function testDestroyTrashedContactController()
    {
        // 1件のソフトデリートされた問い合わせを作成
        $contact = Contact::factory()->for(User::factory())->create(['deleted_at' => now()]);

        // ソフトデリートした問い合わせを、完全削除する為に、リクエストを送信
        $response = $this->delete(route('admin.trashed-contact.destroy'), ['contentId' => $contact->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('admin.trashed-contact.index'));
        $response->assertSessionHas(['message' => 'ユーザーの問い合わせを、完全に削除しました。', 'status' => 'success']);

        // 問い合わせが完全に削除されたことを確認
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
