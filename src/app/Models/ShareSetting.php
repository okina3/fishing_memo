<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'sharing_user_id',
        'memo_id',
        'edit_access',
    ];

    /**
     * Memoモデルとのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }

    /**
     * Userモデルとのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sharing_user_id');
    }
}
