<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'memo_id',
        'spot_id',
    ];

    /**
     * データベースのタイムスタンプを有効または無効にするためのプロパティ。
     * @var bool
     */
    public $timestamps = false;
}
