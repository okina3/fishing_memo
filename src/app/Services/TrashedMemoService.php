<?php

namespace App\Services;

use App\Models\MemoBait;
use App\Models\MemoFishName;
use App\Models\MemoImage;
use App\Models\MemoSpot;

class TrashedMemoService
{
   /**
    * 選択したメモの中間テーブルを削除するメソッド。
    *
    * @param int $memoId
    * @return void
    */
   public static function deleteRelatedRecords(int $memoId): void
   {
      MemoSpot::where('memo_id', $memoId)->delete();
      MemoBait::where('memo_id', $memoId)->delete();
      MemoFishName::where('memo_id', $memoId)->delete();
      MemoImage::where('memo_id', $memoId)->delete();
   }
}
