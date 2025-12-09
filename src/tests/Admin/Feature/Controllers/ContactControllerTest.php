<?php

namespace Tests\Admin\Feature\Controllers;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class ContactControllerTest extends TestCase
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

    // ユーザーの問い合わせ一覧が、正しく表示されることをテスト
    public function testIndexContactController()
    {
        // 3件の問い合わせを作成
        Contact::factory()->for(User::factory())->count(3)->create();

        // 問い合わせ一覧画面を表示する為に、リクエスト送信
        $response = $this->get(route('admin.contact.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（admin.contacts.index）であることを検証
        $response->assertViewIs('admin.contacts.index');
        // ビューに渡される主要なデータ（全問い合わせ情報）が存在することを検証
        $response->assertViewHas('all_contact');
    }

    // ユーザーの問い合わせ詳細が、正しく表示されることをテスト
    public function testShowContactController()
    {
        // 1件の問い合わせを作成
        $contact = Contact::factory()->for(User::factory())->create();

        // 問い合わせ詳細画面を表示する為に、リクエスト送信
        $response = $this->get(route('admin.contact.show', ['contact' => $contact->id]));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（admin.contacts.show）であることを検証
        $response->assertViewIs('admin.contacts.show');
        // ビューに渡される主要なデータ（選択した問い合わせ情報）が存在することを検証
        $response->assertViewHas('select_contact');
    }

    // ユーザーの問い合わせが、正しく削除（ソフトデリート）されることをテスト
    public function testDestroyContactController()
    {
        // 1件の問い合わせを作成
        $contact = Contact::factory()->for(User::factory())->create();

        // 問い合わせを削除する為に、リクエスト送信
        $response = $this->delete(route('admin.contact.destroy'), ['contentId' => $contact->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('admin.contact.index'));
        $response->assertSessionHas(['message' => 'ユーザーの問い合わせをゴミ箱に移動しました。', 'status' => 'success']);

        // 対象問い合わせがソフトデリートされていることを検証
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }
}
