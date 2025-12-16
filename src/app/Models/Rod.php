<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
