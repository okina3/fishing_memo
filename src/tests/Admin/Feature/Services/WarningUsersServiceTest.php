<?php

namespace Tests\Admin\Feature\Services;

use App\Models\Image;
use App\Models\User;
use App\Services\WarningUsersService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\User\TestCase;

class WarningUsersServiceTest extends TestCase
{
   use RefreshDatabase;

   // 選択したユーザー、関連データを完全削除するメソッドのテスト
   public function testPermanentlyDeleteUser()
   {
      // Storage を偽装
      Storage::fake('public');
      // ユーザーと関連する画像レコードを作成
      $user = User::factory()->create();
      // 関連する画像ファイルとレコードを作成
      $filenames = ['test1.jpg', 'folder/test2.png'];
      foreach ($filenames as $filename) {
         // Storage にファイルを作成
         Storage::disk('public')->put($filename, 'dummy');
         // DB に画像レコードを作成
         Image::factory()->create(['user_id' => $user->id, 'filename' => $filename]);
      }

      // ユーザーをソフトデリートして onlyTrashed の対象にする
      $user->delete();

      // ファイルが存在し、ユーザーがソフトデリート状態であることを確認
      foreach ($filenames as $filename) {
         $this->assertTrue(Storage::disk('public')->exists($filename));
      }
      $this->assertDatabaseHas('users', ['id' => $user->id]);

      // ユーザーと関連データを完全削除するサービスを実行
      WarningUsersService::permanentlyDeleteUser($user->id);

      // Storage のファイルが削除されていることを確認
      foreach ($filenames as $filename) {
         $this->assertFalse(Storage::disk('public')->exists($filename));
      }

      // ユーザーレコードが完全に削除されていることを確認
      $this->assertDatabaseMissing('users', ['id' => $user->id]);
   }
}
