<?php

namespace Tests\Admin\Feature\Services;

use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class UserServiceTest extends TestCase
{
   use RefreshDatabase;

   private User $user;
   private User $secondaryUser;
   private User $tertiaryUser;
   private User $quaternaryUser;

   // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
   protected function setUp(): void
   {
      // 親クラスのsetUpメソッドを呼び出し
      parent::setUp();
      // ユーザーを作成
      $this->user = User::factory()->create();
      // 2人目の別のユーザーを作成
      $this->secondaryUser = User::factory()->create();
      // 3人目のユーザーを作成
      $this->tertiaryUser = User::factory()->create();
      // 4人目のユーザーを作成
      $this->quaternaryUser = User::factory()->create();
      // 認証済みのユーザーを返す
      $this->actingAs($this->user, 'users');
   }

   // メモを共有させる設定を作成するヘルパーメソッド
   private function createShareSetting(User $sharingUser, Memo $memo): ShareSetting
   {
      return ShareSetting::factory()->create([
         // 共有させたいユーザー
         'sharing_user_id' => $sharingUser->id,
         // メモの選択
         'memo_id' => $memo->id,
         // 編集も可能
         'edit_access' => true
      ]);
   }

   // 停止ユーザーの共有設定を解除するメソッドのテスト。
   public function testDeleteUserShareSettingAll()
   {
      // 2人目のユーザーのメモを作成し、自分に共有する設定を作成
      $secondaryUserMemo = Memo::factory()->create(['user_id' => $this->secondaryUser->id]);
      $shareSetting = $this->createShareSetting($this->user, $secondaryUserMemo);

      // 自分のメモを作成し、2人目のユーザーに共有する設定を作成
      $memo = Memo::factory()->create(['user_id' => $this->user->id]);
      $secondaryUserSetting = $this->createShareSetting($this->secondaryUser, $memo);

      // 3人目のユーザーのメモを作成し、4人目のユーザーに共有する設定を作成
      $tertiaryUserMemo = Memo::factory()->create(['user_id' => $this->tertiaryUser->id]);
      $tertiaryUserSetting = $this->createShareSetting($this->quaternaryUser, $tertiaryUserMemo);

      // 共有設定を削除するサービスメソッドを実行（停止ユーザーが、共有しているメモの共有を解除）
      UserService::deleteUserShareSettingAll($shareSetting->memo->user->id);
      // 共有設定が、削除されたことを確認
      $this->assertDatabaseMissing('share_settings', ['id' => $shareSetting->id]);

      // 共有設定を削除するサービスメソッドを実行（停止ユーザーに、共有しているメモの共有を解除）
      UserService::deleteUserShareSettingAll($secondaryUserSetting->memo->user->id);
      // 共有設定が、削除されたことを確認
      $this->assertDatabaseMissing('share_settings', ['id' => $secondaryUserSetting->id]);

      // 他のユーザーの共有設定が影響を受けていないことを確認する
      $this->assertDatabaseHas('share_settings', ['id' => $tertiaryUserSetting->id]);
   }
}
