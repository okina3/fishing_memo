<?php

namespace App\Services\User;

use App\Models\Memo;
use App\Models\MemoBait;
use App\Models\MemoFishName;
use App\Models\MemoHook;
use App\Models\MemoImage;
use App\Models\MemoRod;
use App\Models\MemoSpot;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrashedMemoService
{
   /**
    * ソフトデリートしたメモ一覧を表示するメソッド。
    * @param string|null $keyword
    * @param int $perPage
    * @return LengthAwarePaginator
    */
   public static function allTrashedMemos(?string $keyword, int $perPage = 15): LengthAwarePaginator
   {
      return Memo::availableAllTrashedMemos()
         ->searchKeyword($keyword)
         ->paginate($perPage)
         ->withQueryString();
   }

   /**
    * 選択したメモの中間テーブルを削除するメソッド。
    *
    * @param int $memoId
    * @return void
    */
   public static function deleteRelatedRecords(int $memoId): void
   {
      MemoSpot::where('memo_id', $memoId)->delete();
      MemoRod::where('memo_id', $memoId)->delete();
      MemoHook::where('memo_id', $memoId)->delete();
      MemoBait::where('memo_id', $memoId)->delete();
      MemoFishName::where('memo_id', $memoId)->delete();
      MemoImage::where('memo_id', $memoId)->delete();
   }
}
