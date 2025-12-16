<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\User\StoreMemoRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class StoreMemoRequestTest extends TestCase
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
      // StoreMemoRequestのインスタンスを初期化
      $request = new StoreMemoRequest();

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
      // 必須のリレーションを作成して、バリデーションに通るデータを準備
      $spot = \App\Models\Spot::factory()->create();

      // バリデーション用のデータを設定（全てルールに合う値）
      $data = [
         // 釣行日・時間・天候・気温・風向
         'fishing_date' => '2024-06-01',
         'start_time' => '08:00',
         'end_time' => '12:00',
         'weather' => '晴れ',
         'air_temp' => 20,
         'wind_dir' => '北',
         // 備考
         'content' => 'テストメモの内容',
      ];
      // StoreMemoRequestのインスタンスを初期化
      $request = new StoreMemoRequest();
      // データをマージしてバリデータを作成
      $request->merge($data);
      $validator = Validator::make($request->all(), $request->rules());

      // バリデーションが成功することを確認
      $this->assertTrue($validator->passes());
   }

   // バリデーションエラーメッセージが、正しく設定されていることを確認するテスト
   public function testMessagesMethod()
   {
      // StoreMemoRequestのインスタンスを初期化
      $request = new StoreMemoRequest();

      // リクエストから、バリデーションメッセージを取得
      $messages = $request->messages();
      // 期待されるバリデーションメッセージを定義
      $expectedMessages = [
         // 釣行日・時間・天候・気温・風向
         'fishing_date.required' => '釣行日を指定してください。',
         'fishing_date.date' => '釣行日の形式が不正です。',
         'fishing_date.before_or_equal' => '釣行日は今日以前の日付を指定してください。',
         'start_time.required' => '開始時間を指定してください。',
         'start_time.date_format' => '開始時間の形式は HH:MM で指定してください。',
         'end_time.required' => '終了時間を指定してください。',
         'end_time.date_format' => '終了時間の形式は HH:MM で指定してください。',
         'end_time.after_or_equal' => '終了時間は開始時間以降を指定してください。',
         'spot_areas.array' => '釣り場データの形式が不正です。',
         'spot_areas.*.spot_id.integer' => '釣り場は整数で指定してください。',
         'spot_areas.*.spot_id.required' => '釣り場を選択してください。また、マスターズ管理から釣り場を登録をしてから選択してください。',
         'spot_areas.*.spot_id.exists' => '選択された釣り場は存在しません。',
         'weather.in' => '天気の値が不正です。',
         'weather.string' => '天気は文字列で指定してください。',
         'air_temp.integer' => '気温は整数で指定してください。',
         'air_temp.min' => '気温は 0 以上で指定してください。',
         'air_temp.max' => '気温は 60 以下で指定してください。',
         'max_wind.integer' => '最大風速は整数で指定してください。',
         'wind_dir.in' => '風向の値が不正です。',
         // 釣り場
         'spot_areas.*.river_flow.in' => '川の流れの値が不正です。',
         'spot_areas.*.turbidity.in' => '濁りの値が不正です。',
         'spot_areas.*.water_level.numeric' => '水位は数値で指定してください。',
         'spot_areas.*.water_level.min' => '水位は 0 以上で指定してください。',
         'spot_areas.*.water_level.max' => '水位は 999.9 以下で指定してください。',
         'spot_areas.*.water_temp.integer' => '水温は整数で指定してください。',
         'spot_areas.*.water_temp.min' => '水温は 0 以上で指定してください。',
         'spot_areas.*.water_temp.max' => '水温は 99 以下で指定してください。',
         // エサ
         'baits.array' => 'エサの形式が不正です。',
         'baits.*.integer' => 'エサの選択値が不正です。',
         'baits.*.distinct' => '同じエサが複数選択されています。',
         'baits.*.exists' => '選択されたエサは存在しません。',
         // 釣果入力（配列）
         'fishing_results.array' => '釣果データの形式が不正です。',
         'fishing_results.*.fish_name_id.integer' => '魚名の値が不正です。',
         'fishing_results.*.fish_name_id.exists' => '選択された魚名は存在しません。',
         'fishing_results.*.count.required_with' => '匹数も入力してください。',
         'fishing_results.*.count.integer' => '匹数は整数で指定してください。',
         'fishing_results.*.count.min' => '匹数は 0 以上で指定してください。',
         'fishing_results.*.length.required_with' => '長さも入力してください。',
         'fishing_results.*.length.integer' => '長さは整数で指定してください。',
         'fishing_results.*.length.min' => '長さは 0 以上で指定してください。',
         // 備考
         'content.string' => 'メモの備考が空です。また、文字列で指定してください。',
         'content.max' => '文字数は、1000文字以内にしてください。',
      ];

      // 取得したメッセージが、期待されるバリデーションメッセージと一致することを確認
      $this->assertEquals($expectedMessages, $messages);
   }
}
