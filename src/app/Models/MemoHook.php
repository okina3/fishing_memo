<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoHook extends Model
{
   use HasFactory;

   protected $fillable = [
      'memo_id',
      'hook_id',
   ];

   /**
    * データベースのタイムスタンプを有効または無効にするためのプロパティ。
    * @var bool
    */
   public $timestamps = false;
}
