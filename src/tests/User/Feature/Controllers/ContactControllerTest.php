<?php

namespace Tests\User\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\User\TestCase;

class ContactControllerTest extends TestCase
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

    // 管理人への問い合わせの新規作成画面が、正しく表示されることをテスト
    public function testCreateContactController()
    {
        // 管理人への問い合わせの新規作成画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.contact.create'));

        // ステータスコード200（OK）であることを検証
        $response->assertStatus(200);
        // 返却されるビューが期待通り（user.contacts.create）であることを検証
        $response->assertViewIs('user.contacts.create');
    }

    // 管理人への問い合わせが、正しく保存されることをテスト
    public function testStoreContactController()
    {
        // リクエストデータを作成
        $requestData = [
            'subject' => 'テスト、問い合わせ',
            'message' => 'これはテストメッセージです。',
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));

        // 管理人への問い合わせを保存する為に、リクエストを送信
        $response = $this->post(route('user.contact.store'), $requestData);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => '管理人にメッセージを送りました。', 'status' => 'success']);

        // 問い合わせが作成されていることを検証
        $this->assertDatabaseHas('contacts', [
            'user_id' => $this->user->id,
            'subject' => 'テスト、問い合わせ',
            'message' => 'これはテストメッセージです。',
        ]);
    }
}
