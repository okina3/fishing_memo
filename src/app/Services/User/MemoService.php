<?php

namespace App\Services\User;

use App\Models\Memo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MemoService
{
   /**
    * 別のユーザーのメモを見られなくする為のメソッド。
    * @param $request
    * @return void
    */
   public static function checkUserMemo($request): void
   {
      // パラメーターを取得
      $id_memo = $request->route()->parameter('memo');
      // パラメーターが無ければチェック不要
      if (!is_null($id_memo)) {
         // 自分自身のメモなのかチェック
         $memo = Memo::select('user_id')->findOrFail($id_memo);
         if ($memo->user_id !== Auth::id()) {
            abort(404);
         }
      }
   }

   /**
    * メモを一覧表示するメソッド。
    * @param string|null $keyword
    * @param int $perPage
    * @return LengthAwarePaginator
    */
   public static function searchMemos(?string $keyword = null, int $perPage = 15): LengthAwarePaginator
   {
      // 全メモ、または、検索されたメモを取得
      $memos = Memo::availableAllMemos()
         ->searchKeyword($keyword)
         ->paginate($perPage)
         ->withQueryString();
      // 共有されているメモにはステータスを付与
      foreach ($memos as $memo) {
         if ($memo->shareSettings->isNotEmpty()) {
            $memo->status = "共有中";
         }
      }
      return $memos;
   }

   /**
    * メモを保存するメソッド。
    * @param $request
    * @return Memo
    */
   public static function createMemo($request): Memo
   {
      return Memo::create([
         'fishing_date' => $request->input('fishing_date'),
         'start_time' => $request->input('start_time'),
         'end_time' => $request->input('end_time'),
         'weather' => $request->input('weather'),
         'air_temp' => $request->input('air_temp'),
         'wind_dir' => $request->input('wind_dir'),
         'content' => $request->input('content'),
         'user_id' => Auth::id(),
      ]);
   }

   /**
    * メモを更新するメソッド。
    * @param $request
    * @return mixed
    */
   public static function updateMemo($request): mixed
   {
      $memo = Memo::availableSelectMemo($request->memoId)->first();

      $memo->fishing_date = $request->input('fishing_date');
      $memo->start_time = $request->input('start_time');
      $memo->end_time = $request->input('end_time');
      $memo->weather = $request->input('weather');
      $memo->air_temp = $request->input('air_temp');
      $memo->wind_dir = $request->input('wind_dir');
      $memo->content = $request->input('content');

      $memo->save();

      return $memo;
   }

   /**
    * メモに紐づいた釣り場を、中間テーブルに保存するメソッド
    * @param $request
    * @param int $memo_id
    * @return void
    */
   public static function attachExistingSpots($request, int $memo_id): void
   {
      // 場所入力があれば処理を進める
      $spot_areas = $request->input('spot_areas', []);
      if (!is_array($spot_areas) || count($spot_areas) === 0) {
         return;
      }

      // ピボット属性付きで中間テーブルに保存するための配列を作成
      $attachData = [];
      foreach ($spot_areas as $spot_area) {
         $spotId = (int) ($spot_area['spot_id'] ?? 0);
         if ($spotId <= 0) {
            // 無効値はスキップ
            continue;
         }
         $river_flow = isset($spot_area['river_flow']) ? (string) $spot_area['river_flow'] : '';
         $turbidity = isset($spot_area['turbidity']) ? (string) $spot_area['turbidity'] : '';
         $water_level = isset($spot_area['water_level']) ? (float) $spot_area['water_level'] : 0.0;
         $water_temp = isset($spot_area['water_temp']) ? (int) $spot_area['water_temp'] : 0;
         $attachData[$spotId] = [
            'river_flow' => $river_flow,
            'turbidity' => $turbidity,
            'water_level' => $water_level,
            'water_temp' => $water_temp
         ];
      }

      // 場所データを、メモに紐付けて中間テーブルに保存
      if (!empty($attachData)) {
         $memo = Memo::findOrFail($memo_id);
         $memo->spots()->attach($attachData);
      }
   }

   /**
    * メモに紐づいた釣り竿を、中間テーブルに保存するメソッド
    * @param $request
    * @param int $memo_id
    * @return void
    */
   public static function attachExistingRods($request, int $memo_id): void
   {
      // 釣り竿入力があれば処理を進める
      $rod_areas = $request->input('rod_areas', []);
      if (!is_array($rod_areas) || count($rod_areas) === 0) {
         return;
      }

      // ピボット属性付きで中間テーブルに保存するための配列を作成
      $attachData = [];
      foreach ($rod_areas as $rod_area) {
         $rodId = (int) ($rod_area['rod_id'] ?? 0);
         if ($rodId <= 0) {
            // 無効値はスキップ
            continue;
         }
         $main_line = isset($rod_area['main_line']) ? (float) $rod_area['main_line'] : 0.0;
         $attachData[$rodId] = [
            'main_line' => $main_line,
         ];
      }

      // 釣り竿データを、メモに紐付けて中間テーブルに保存
      if (!empty($attachData)) {
         $memo = Memo::findOrFail($memo_id);
         $memo->rods()->attach($attachData);
      }
   }

   /**
    * メモに紐づいた釣り針を、中間テーブルに保存するメソッド
    * @param $request
    * @param int $memo_id
    * @return void
    */
   public static function attachExistingHooks($request, int $memo_id): void
   {
      // 釣り針入力があれば処理を進める
      $hook_areas = $request->input('hook_areas', []);
      if (!is_array($hook_areas) || count($hook_areas) === 0) {
         return;
      }

      // ピボット属性付きで中間テーブルに保存するための配列を作成
      $attachData = [];
      foreach ($hook_areas as $hook_area) {
         $hookId = (int) ($hook_area['hook_id'] ?? 0);
         if ($hookId <= 0) {
            // 無効値はスキップ
            continue;
         }
         $leader_size = isset($hook_area['leader_size']) ? (float) $hook_area['leader_size'] : null;
         $leader_upper_cm = isset($hook_area['leader_upper_cm']) ? (int) $hook_area['leader_upper_cm'] : null;
         $leader_lower_cm = isset($hook_area['leader_lower_cm']) ? (int) $hook_area['leader_lower_cm'] : null;
         $attachData[$hookId] = [
            'leader_size' => $leader_size,
            'leader_upper_cm' => $leader_upper_cm,
            'leader_lower_cm' => $leader_lower_cm,
         ];
      }

      // 釣り針データを、メモに紐付けて中間テーブルに保存
      if (!empty($attachData)) {
         $memo = Memo::findOrFail($memo_id);
         $memo->hooks()->attach($attachData);
      }
   }

   /**
    * メモに紐づいたエサを、中間テーブルに保存するメソッド
    * @param $request
    * @param int $memo_id
    * @return void
    */
   public static function attachExistingBaits($request, int $memo_id): void
   {
      // 既存エサの選択があれば、メモに紐付けて中間テーブルに保存
      if (!empty($request->baits)) {
         $memo = Memo::findOrFail($memo_id);
         $baitIds = array_filter(array_map('intval', (array) $request->baits), fn($id) => $id > 0);
         if (!empty($baitIds)) {
            $memo->baits()->attach($baitIds);
         }
      }
   }

   /**
    * メモに紐づいた釣果データを、中間テーブルに保存するメソッド
    * @param $request
    * @param int $memo_id
    * @return void
    */
   public static function attachExistingFishNames($request, int $memo_id): void
   {
      // 釣果入力があれば処理を進める
      $fishing_results = $request->input('fishing_results', []);
      if (!is_array($fishing_results) || count($fishing_results) === 0) {
         return;
      }

      // ピボット属性付きで中間テーブルに保存するための配列を作成
      $attachData = [];
      foreach ($fishing_results as $fishing_result) {
         $fishNameId = (int) ($fishing_result['fish_name_id'] ?? 0);
         if ($fishNameId <= 0) {
            // 無効値はスキップ
            continue;
         }
         $count = isset($fishing_result['count']) ? (int) $fishing_result['count'] : 0;
         $length = isset($fishing_result['length']) ? (int) $fishing_result['length'] : 0;
         $attachData[$fishNameId] = ['count' => $count, 'length' => $length];
      }

      // 釣果のデータを、メモに紐付けて中間テーブルに保存
      if (!empty($attachData)) {
         $memo = Memo::findOrFail($memo_id);
         $memo->fish_names()->attach($attachData);
      }
   }

   /**
    * メモに紐づいた既存画像を、中間テーブルに値を保存するメソッド。
    * @param $request
    * @param int $memo_id
    * @return void
    */
   public static function attachExistingImages($request, int $memo_id): void
   {
      // 画像の選択があれば、メモに紐付けて中間テーブルに保存
      if (!empty($request->images)) {
         $memo = Memo::findOrFail($memo_id);
         $imageIds = array_map('intval', (array) $request->images);
         if (!empty($imageIds)) {
            $memo->images()->attach($imageIds);
         }
      }
   }

   /**
    * 共有されているメモに目印を付けるメソッド。
    * @param $select_memo
    * @return mixed
    */
   public static function checkShared($select_memo): mixed
   {
      // メモが共有されているかどうかを確認
      $is_shared = $select_memo->shareSettings->isNotEmpty();
      // もしメモが共有されている場合、メモに共有中のステータスを追加
      if ($is_shared) {
         $select_memo->status = "共有中";
      }
      return $select_memo;
   }
}
