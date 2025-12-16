<?php

namespace App\Services;

use App\Models\Memo;
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
     * @return mixed
     */
    public static function searchMemos(): mixed
    {
        // クエリパラメータを取得
        $get_url_tag = request()->query('tag');
        // クエリパラメータがあった場合の処理
        if (!empty($get_url_tag)) {
            // クエリパラメータから絞り込んだタグを取得
            $select_tag = Tag::availableSelectTag($get_url_tag)->first();
            // クエリパラメータから絞り込んだタグに、リレーションされたメモを取得
            $memos = $select_tag->memos;
        } else {
            // 全メモを取得
            $memos = Memo::availableAllMemos()->get();
        }
        foreach ($memos as $memo) {
            // メモが共有されているかどうかを確認
            $is_shared = $memo->shareSettings->isNotEmpty();
            // もしメモが共有されている場合、そのメモに目印を付ける
            if ($is_shared) {
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
