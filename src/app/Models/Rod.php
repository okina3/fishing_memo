<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Rod extends Model
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'name',
   ];

   /**
    * Memoモデルとの多対多のリレーションを定義。
    * @return BelongsToMany
    */
   public function memos(): BelongsToMany
   {
      return $this->belongsToMany(Memo::class, 'memo_rods')
         ->withPivot(['main_line']);
   }

   /**
    * Userモデルへのリレーションを返す（一対多）。
    * @return BelongsTo
    */
   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class);
   }

   /**
    * 自分自身の、全ての釣り竿を取得する為のスコープ。
    * @param Builder $query
    * @return void
    */
   public function scopeAvailableAllRods(Builder $query): void
   {
      $query->where('user_id', Auth::id())
         ->orderBy('created_at', 'desc');
   }

   /**
    * 自分自身の、選択した釣り竿を取得する為のスコープ。
    * @param Builder $query
    * @param int $id
    * @return void
    */
   public function scopeAvailableSelectRod(Builder $query, int $id): void
   {
      $query->where('id', $id)
         ->where('user_id', Auth::id());
   }

   /**
    * 検索した釣り竿の名前を表示するの為のスコープ。
    * @param Builder $query
    * @param string|null $keyword
    * @return void
    */
   public function scopeSearchKeyword(Builder $query, ?string $keyword = null): void
   {
      if ($keyword !== null && $keyword !== '') {
         // 全角スペースを半角に変換
         $spaceConvert = mb_convert_kana($keyword, 's');
         // 空白で分割して単語配列にする
         $keywords = preg_split('/\s+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY) ?: [];
         // 各単語ごとに OR 条件で name カラムを部分一致検索
         $query->where(function (Builder $q) use ($keywords) {
            foreach ($keywords as $word) {
               $q->orWhere('name', 'like', '%' . $word . '%');
            }
         });
      }
   }
}
